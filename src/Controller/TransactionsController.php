<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use DateTime;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 */
class TransactionsController extends AppController
{

    /**
     * Load Model
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
        $this->loadModel('Categories');
        $this->loadModel('Wallets');
        $this->loadModel('Types');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index($time_range = 'month', $current = 0)
    {
        $user = $this->getCurrentUserInfo();
        $now = Time::now();
        //add first wallet
        if (empty($user->last_wallet)) {
            return $this->redirect(['controller' => 'wallets', 'action' => 'add']);
        }
        //get info last wallet
        $current_wallet = $this->Wallets->get($user->last_wallet);

        //change time_range request
        if ($this->request->is('post')) {
            if (!empty($this->request->data['time_range'])) {
                $data = $this->request->data();
                var_dump($data);die;
                $time_range = $this->request->data['time_range'];
            }
        }
        // get conditions of last list or next list
        if ($this->request->is('get')) {
            $data = $this->request->params['pass'];
            if (!empty($data)) {
                $time_range = $data[0];
                $current = $data[1];
            }
        }
        $time_to_list = $this->getTimeToList($time_range, $now, $current);


        $condition_list = $this->changeTimeRange($time_range, $current_wallet->id, $time_to_list, $current);
        $this->paginate = $condition_list;
        if ($current_wallet->checkCreatedWallet($time_to_list) == false) {
            $current_wallet->init_balance = $current_wallet->current_balance = 0;
        }

        $this->set('transactions', $this->paginate($this->Transactions));
        $types = $this->Types->find('all', [
            'fields' => ['title'],
            'limit' => 2,
        ]);
        $titleOfTransactionsList = $this->titleOfTransactionsList($time_range, $time_to_list, $current);
        $wallets = $this->Wallets->getAllWalletsOfUser($user);
        $mothly_reports = $this->Transactions->monthlyReport($current_wallet, $now, $current);
        $total_balance = $user->total_balance;
        $last_wallet = $user->last_wallet;
        $this->set(compact('current_wallet', 'wallets', 'last_wallet', 'now', 'mothly_reports', 'total_balance', 'types', 'time_range', 'titleOfTransactionsList', 'current'));
        $this->set('_serialize', ['transactions']);
        $this->set('title', __('Monthly Report'));
    }

    /**
     * View method
     *
     * @param string|null $id Transaction id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transaction = $this->Transactions->get($id, [
            'contain' => ['Categories']
        ]);
        $this->set('transaction', $transaction);
        $this->set('_serialize', ['transaction']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->getCurrentUserInfo();
        $income_categories = $this->Categories->getListIncomeCategories($user);
        $expense_categories = $this->Categories->getListExpenseCategories($user);
        $transaction = $this->Transactions->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $category = $this->Categories->find()->where(['id' => $data['category_id']])->first();
            $wallet = $this->Wallets->get($user->last_wallet);
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            $transaction->wallet_id = $user->last_wallet;
            $transaction->user_id = $this->Auth->user('id');
            if ($category->type_id == 1) {
                $wallet->current_balance = $wallet->current_balance + $transaction->amount;
            } elseif ($category->type_id == 2) {
                $wallet->current_balance = $wallet->current_balance - $transaction->amount;
            }
            if ($this->Transactions->save($transaction) && $this->Wallets->save($wallet)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['controller' => 'transactions', 'action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('transaction', 'income_categories', 'expense_categories'));
        $this->set('_serialize', ['transaction']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Transaction id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->getCurrentUserInfo();
        $transaction = $this->Transactions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['controller' => 'transactions', 'action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $income_categories = $this->Categories->getListIncomeCategories($user);
        $expense_categories = $this->Categories->getListExpenseCategories($user);
        $this->set(compact('transaction', 'income_categories', 'expense_categories'));
        $this->set('_serialize', ['transaction']);
    }

    /**
     * Soft delete method
     *
     * @param string|null $id Transaction id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transaction = $this->Transactions->get($id);

        if ($this->Transactions->saveAfterDelete($transaction)) {
            $this->Flash->success(__('The transaction has been deleted.'));
        } else {
            $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
        }
        $this->redirect(['controller' => 'transactions', 'action' => 'index']);
    }

    /**
     *  Transfer money between wallets
     * 
     * return void
     */
    public function transferMoney()
    {
        $user = $this->getCurrentUserInfo();
        $transaction = $this->Transactions->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $receiver_wallet = $this->Wallets->get($data['to_wallet']);
            $transfer_wallet = $this->Wallets->get($data['from_wallet']);
            $transfer_transaction = $this->Transactions->setTransferTransaction($data, $transfer_wallet->id, $receiver_wallet->title);
            $receiver_transaction = $this->Transactions->setReceiverTransaction($data, $receiver_wallet->id, $transfer_wallet->title);

            if ($transfer_wallet->checkCreatedWallet($data['done_date']['month'], $data['done_date']['year'])) {
                $transfer_wallet->current_balance = $transfer_wallet->current_balance - $transfer_transaction->amount;
                $receiver_wallet->current_balance = $receiver_wallet->current_balance + $receiver_transaction->amount;
            }
            if ($this->Transactions->saveTransfer($transfer_wallet, $receiver_wallet, $transfer_transaction, $receiver_transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['controller' => 'transactions', 'action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $expense_categories = $this->Categories->getListExpenseCategories($user);
        $wallets = $this->Transactions->Wallets->find('list', [
            'conditions' => [
                'Wallets.user_id' => $user->id,
                'Wallets.status' => 1,
            ],
            'limit' => 200]);
        $this->set(compact('wallets', 'expense_categories', 'user', 'transaction'));
        $this->set('_serialize', ['transaction']);
        $this->set('title', __('Transfer Money Between Wallets'));
    }

    public function changeTimeRange($time_range, $wallet_id, $now, $current)
    {
        switch ($time_range) {
            case 'day' :
                $condition_list = $this->Transactions->conditionDay($wallet_id, $now);
                break;
            case 'week' :
                $condition_list = $this->Transactions->conditionWeek($wallet_id, $now, $current);
                break;
            case 'month' :
                $condition_list = $this->Transactions->conditionMonth($wallet_id, $now);
                break;
            case 'quarter' :
                $condition_list = $this->Transactions->conditionQuarter($wallet_id, $now);
                break;
            case 'year' :
                $condition_list = $this->Transactions->conditionYear($wallet_id, $now);
                break;
            default :
                $condition_list = $this->Transactions->conditionAll($wallet_id);
                break;
        }
        return $condition_list;
    }

    /**
     * Authorization logic for transactions
     * 
     * @param type $user
     * @return boolean
     */
    public function isAuthorized($user)
    {
        $action = $this->request->params['action'];

        // The add and index actions are always allowed.
        if (in_array($action, ['index', 'view', 'add', 'edit', 'delete', 'transferMoney', 'changeTimeRange'])) {
            return true;
        }
        // All other actions require an id.
        if (empty($this->request->params['pass'][0])) {
            return false;
        }

        // Check that the wallet belongs to the current user.
        $id = $this->request->params['pass'][0];
        $transaction = $this->Transactions->get($id);
        if ($transaction->user_id == $user['id']) {
            return true;
        }
        return parent::isAuthorized($user);
    }

    /**
     * Get title of transaction list by time range
     * 
     * @param type $time_range
     * @param type $list_day
     * @param type $list_month
     * @param type $list_year
     * @return string
     */
    public function titleOfTransactionsList($time_range, $time, $current)
    {
        switch ($time_range) {
            case 'day':
                if ($time->isWithinNext(0)) {
                    $titleOfTransactionsList = __('Today');
                } elseif ($time->wasWithinLast(1)) {
                    $titleOfTransactionsList = __('Yesterday');
                } elseif ($time->isWithinNext(1)) {
                    $titleOfTransactionsList = __('Tomorrow');
                } else {
                    $titleOfTransactionsList = $time->day . '/ ' . $time->month . '/ ' . $time->year;
                }
                break;

            case 'month':
                if ($time->isThisMonth()) {
                    $titleOfTransactionsList = __('This Month');
                } elseif ($time->wasWithinLast('1 month')) {
                    $titleOfTransactionsList = __('Last Month');
                } elseif ($time->isWithinNext('1 month')) {
                    $titleOfTransactionsList = __('Next Month');
                } else {
                    $titleOfTransactionsList = $time->month . '/ ' . $time->year;
                }
                break;
            case 'year':
                if ($time->isThisYear()) {
                    $titleOfTransactionsList = __('This Year');
                } elseif ($time->wasWithinLast('1 year')) {
                    $titleOfTransactionsList = __('Last Year');
                } elseif ($time->isWithinNext('1 year')) {
                    $titleOfTransactionsList = __('Next Year');
                } else {
                    $titleOfTransactionsList = $time->year;
                }
                break;
            case 'week':
                $now = Time::now()->weekOfYear;
                $day_of_week = Time::now()->dayOfWeek;
                $start = new DateTime((-$day_of_week + 7 * $current +1) . ' Days');
                $end = new DateTime((7 - $day_of_week + 7 * $current) . ' Day');
                $current_week = $time->weekOfYear;
                if ($now == $current_week) {
                    $titleOfTransactionsList = __('This Week');
                } elseif ($current_week === ($now - 1)) {
                    $titleOfTransactionsList = __('Last Week');
                } elseif (($current_week === ($now + 1))) {
                    $titleOfTransactionsList = __('Next Week');
                } else {
                    $titleOfTransactionsList = $start->format('d/m/o') . ' => ' . $end->format('d/m/o');
                }
                break;
            case 'quarter' :
                $date = new Time();
                $now_quarter = ceil(( $date->month)/3);
                $time_quarter = $time->toQuarter();

                if (($now_quarter == $time_quarter) && ($date->year == $time->year)) {
                    $titleOfTransactionsList = __('This Quarter');
                } elseif ((($now_quarter - $time_quarter === 1) && ($now->year === $time->year)) || ( $now_quarter === 1 && $time_quarter === 4 && ( $now->year - $time->year === 1))) {
                    $titleOfTransactionsList = __('Last Quarter');
                } elseif ((($now_quarter - $time_quarter === -1) && ($now->year === $time->year)) || ( $now_quarter === 4 && $time_quarter === 1 && ( $now->year - $time->year === -1))) {
                    $titleOfTransactionsList = __('Next Quarter');
                } else {
                    $titleOfTransactionsList = __('Q') . $time_quarter . '/ ' . $time->year;
                }
                break;
        }
        return $titleOfTransactionsList;
    }

    /**
     * Get time to list data
     * 
     * @param type $time_range
     * @param type $now
     * @param type $current
     * @return type
     */
    public function getTimeToList($time_range, $now, $current)
    {
        switch ($time_range) {
            case 'day':
                $now->modify($current . ' days');
                break;
            case 'month':
                $now->modify($current . ' months');
                break;
            case 'year':
                $now->modify($current . ' years');
                break;
            case 'week':
                $now->modify($current . ' weeks');
                break;
            case 'quarter':
                $current = $current * 3;
                $now->modify($current . ' months');
                break;
        }
        return $now;
    }

}
