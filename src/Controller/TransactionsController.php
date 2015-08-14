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
    public function index($list_day = null, $list_month = null, $list_year = null)
    {
        $user = $this->getCurrentUserInfo();
        $now = Time::now();
        $current_month = $now->month;
        $current_year = $now->year;
        $current_day = $now->day;
        $time_range = null;

        if (empty($user->last_wallet)) {
            return $this->redirect(['controller' => 'wallets', 'action' => 'add']);
        }
        $current_wallet = $this->Wallets->get($user->last_wallet);
        if (empty($list_month) || empty($list_year)) {
            $list_day = $current_day;
            $list_month = $current_month;
            $list_year = $current_year;
        }
                if ($this->request->is('post')) {
            if ($this->request->is('post')) {
                $data = $this->request->data['time_range'];
                $condition_list = $this->changeTimeRange($data, $current_wallet->id, $list_day, $list_month, $list_year);
            }
        }
        if ($current_wallet->checkCreatedWallet($list_month, $list_year) == false) {
            $current_wallet->init_balance = $current_wallet->current_balance = 0;
        }
        $this->paginate = [
            'conditions' => [
                'Transactions.wallet_id' => $user->last_wallet,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date)' => $list_month,
                'YEAR(Transactions.done_date)' => $list_year,
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        $this->set('transactions', $this->paginate($this->Transactions));
        $types = $this->Types->find('all', [
            'fields' => ['title'],
            'limit' => 2,
        ]);
        $wallets = $this->Wallets->getAllWalletsOfUser($user);
        $mothly_reports = $this->Transactions->monthlyReport($current_wallet, $list_month, $list_year);
        $total_balance = $user->total_balance;
        $last_wallet = $user->last_wallet;
        $this->set(compact('current_wallet', 'wallets', 'last_wallet', 'list_month', 'list_year', 'current_month', 'current_year', 'mothly_reports', 'total_balance', 'types'));
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
    public function changeTimeRange($data, $wallet_id, $list_day, $list_month, $list_year)
    {

        switch ($data) {
            case 'day' :
                $condition_list = $this->Transactions->conditionDay($wallet_id, $list_day, $list_month, $list_year);
                var_dump($condition_list);
                die;
                break;
            case 'week' :
                $condition_list = $this->Transactions->Week();
                break;
            case 'month' :
                $condition_list = $this->Transactions->conditionMonth();
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
        return [$condition_list, $data];
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

}
