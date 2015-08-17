<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

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
    public function index($time_range = null, $change_time = null)
    {
        $user = $this->getCurrentUserInfo();
        $now = Time::now();
        $current_month = $now->month;
        $current_year = $now->year;
        $current_day = $now->day;

        //add first wallet
        if (empty($user->last_wallet)) {
            return $this->redirect(['controller' => 'wallets', 'action' => 'add']);
        }

        //get info last wallet
        $current_wallet = $this->Wallets->get($user->last_wallet);
        if (empty($change_time)) {
            $list_day = $current_day;
            $list_month = $current_month;
            $list_year = $current_year;
        }

        //request to get last list or next list
        if ($this->request->is('get')) {
            $data = $this->request->params['pass'];
            if (!empty($data)) {
                $time_range = $data[0];
                $change_time = $data[1];
                if ($change_time == 'last') {
                    $last_list = $this->lastList($time_range, $list_day, $list_month, $list_year);
                } elseif ($change_time == 'next') {
                    $next_list = $this->nextList($time_range, $list_day, $list_month, $list_year);
                }
            }
        }
        //change time_range request
        if ($this->request->is('post')) {
            $time_range = $this->request->data['time_range'];
        } else {
            $time_range = 'month';
        }
        $condition_list = $this->changeTimeRange($time_range, $current_wallet->id, $list_day, $list_month, $list_year);
        $this->paginate = $condition_list;
        if ($current_wallet->checkCreatedWallet($list_month, $list_year) == false) {
            $current_wallet->init_balance = $current_wallet->current_balance = 0;
        }

        $this->set('transactions', $this->paginate($this->Transactions));
        $types = $this->Types->find('all', [
            'fields' => ['title'],
            'limit' => 2,
        ]);
        $titleOfTransactionsList = $this->titleOfTransactionsList($time_range, $list_day, $list_month, $list_year);
        $wallets = $this->Wallets->getAllWalletsOfUser($user);
        $mothly_reports = $this->Transactions->monthlyReport($current_wallet, $list_month, $list_year);
        $total_balance = $user->total_balance;
        $last_wallet = $user->last_wallet;
        $this->set(compact('current_wallet', 'wallets', 'last_wallet', 'list_month', 'list_year', 'current_month', 'current_year', 'mothly_reports', 'total_balance', 'types', 'time_range', 'titleOfTransactionsList'));
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
        $categories = $this->Transactions->Categories->find('list', ['limit' => 200]);
        $this->set(compact('transaction', 'categories'));
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

    /**
     * Change time range
     * 
     * @param type $wallet_id
     * @param type $list_day
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function changeTimeRange($time_range, $wallet_id, $list_day, $list_month, $list_year)
    {
        switch ($time_range) {
            case 'day' :
                $condition_list = $this->Transactions->conditionDay($wallet_id, $list_day, $list_month, $list_year);
                break;
            case 'week' :
                $condition_list = $this->Transactions->Week();
                break;
            case 'month' :
                $condition_list = $this->Transactions->conditionMonth($wallet_id, $list_month, $list_year);
                break;
            case 'quarter' :
                $condition_list = $this->Transactions->conditionQuarter();
                break;
            case 'year' :
                $condition_list = $this->Transactions->conditionYear();
                break;
            case 'all' :
                $condition_list = $this->Transactions->conditionAll();
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
    public function titleOfTransactionsList($time_range, $list_day, $list_month, $list_year)
    {
        $now = Time::now();
        $current_month = $now->month;
        $current_year = $now->year;
        $current_day = $now->day;
        switch ($time_range) {
            case 'day':
                if (($list_day == $current_day) && ($list_month == $current_month) && ($list_year == $current_year)) {
                    $titleOfTransactionsList = __('Transactions List of Today');
                } elseif (($list_day == ($current_day - 1)) && ($list_month == $current_month) && ($list_year == $current_year)) {
                    $titleOfTransactionsList = __('Transactions List of Yesterday');
                } elseif ((($list_day == $current_day + 1) && ($list_month == $current_month) && ($list_year == $current_year))) {
                    $titleOfTransactionsList = __('Transactions List of Tomorrow');
                } else {
                    $titleOfTransactionsList = __('Transactions List of ') . $list_day . '/ ' . $list_month . '/ ' . $list_year;
                }
                break;

            case 'month':
                if (($list_month == $current_month) && ($list_year == $current_year)) {
                    $titleOfTransactionsList = __('Transactions List of This Month');
                } elseif (($list_day == ($current_month - 1)) && ($list_year == $current_year)) {
                    $titleOfTransactionsList = __('Transactions List of Last Month');
                } elseif ((($list_day == $current_month + 1) && ($list_year == $current_year))) {
                    $titleOfTransactionsList = __('Transactions List of Next Month');
                } else {
                    $titleOfTransactionsList = __('Transactions List of ') . $list_month . '/ ' . $list_year;
                }
                break;
            case 'year':
                if ($list_year == $current_year) {
                    $titleOfTransactionsList = __('Transactions List of This Year');
                } elseif (($list_year - 1) == $current_year) {
                    $titleOfTransactionsList = __('Transactions List of Last Year');
                } elseif (($list_year + 1) == $current_year) {
                    $titleOfTransactionsList = __('Transactions List of Next Year');
                } else {
                    $titleOfTransactionsList = __('Transactions List of ') . $list_year;
                }
                break;
        }
        return $titleOfTransactionsList;
    }

    /**
     * Get time of last list
     * 
     * @param type $time_range
     * @param type $list_day
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function lastList($time_range, $list_day, $list_month, $list_year)
    {
        $time = Time::now();
        $time->setDate($list_year, $list_month, $list_day);
        switch ($time_range) {
            case 'day':
                $time = $time->subDay();
                break;
            case 'month':
                $time = $time->subMonth();
                break;
            case 'year':
                $time = $time->subYear();
                break;
            case 'week':
                $time = $time->subWeek();
                break;
            case 'week':
                $time = $time->subWeek();
                break;
            case 'quarter':
                $time = $time->subMonths(3);
        }
        return [$time_range, $time];
    }

    /**
     * Get time of next list
     * 
     * @param type $time_range
     * @param type $list_day
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function nextList($time_range, $list_day, $list_month, $list_year)
    {
        $time = Time::now();
        $time->setDate($list_year, $list_month, $list_day);
        switch ($time_range) {
            case 'day':
                $time = $time->addDay();
                break;
            case 'month':
                $time = $time->addMonth();
                break;
            case 'year':
                $time = $time->addYear();
                break;
            case 'week':
                $time = $time->addWeek();
                break;
            case 'quarter':
                $time = $time->addMonths(3);
                break;
        }
        return [$time_range, $time];
    }
}
