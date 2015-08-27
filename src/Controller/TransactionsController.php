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
     * Index method
     *
     * @return void
     */
    public function index($time_range = 'month', $current = 0)
    {
        $user = $this->getCurrentUserInfo();
        $now = Time::now();
        //if don't have any wallet-> add wallet
        if (empty($user->last_wallet)) {
            return $this->redirect(['controller' => 'wallets', 'action' => 'add']);
        }
        //get info last wallet
        $current_wallet = $this->Wallets->get($user->last_wallet);

        // get conditions of last list or next list
        if ($this->request->is('get')) {
            $data = $this->request->params['pass'];
            if (!empty($data)) {
                $time_range = $data[0];
                $current = $data[1];
            }
        }
        //change time_range request
        if ($this->request->is('post')) {
            if (!empty($this->request->data['time_range'])) {
                $data = $this->request->data();
                $time_range = $this->request->data['time_range'];
                $last_time_range = $data['current_time_range'];
                $last_current = $data['current'];
                $time_to_list = $this->Transactions->getTimeToList($last_time_range, $now, $last_current);
                $current = $this->Transactions->getNewCurrentAfterChangeTimeRange($time_to_list, $time_range);
            }
        } else {
            $time_to_list = $this->Transactions->getTimeToList($time_range, $now, $current);
        }

        $condition_list = $this->Transactions->changeTimeRange($time_range, $current_wallet->id, $time_to_list, $current);
        $this->paginate = $condition_list;

        //check existence of wallet at time to list
        if ($current_wallet->checkCreatedWallet($time_to_list) == false) {
            $current_wallet->init_balance = $current_wallet->current_balance = 0;
        }

        $this->set('transactions', $this->paginate($this->Transactions));
        $types = $this->Types->getAllTypes();
        $titleOfTransactionsList = $this->Transactions->titleOfTransactionsList($time_range, $time_to_list, $current);
        $wallets = $this->Wallets->getAllWalletsOfUser($user->id);
        $mothly_reports = $this->Transactions->monthlyReport($current_wallet, $now, $current);
        $total_balance = $user->total_balance;
        $last_wallet = $user->last_wallet;
        $this->set(compact('current_wallet', 'wallets', 'last_wallet', 'now', 'mothly_reports', 'total_balance', 'types', 'time_range', 'titleOfTransactionsList', 'current'));
        $this->set('_serialize', ['transactions']);
        $this->set('title', __('Monthly Report'));
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
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            if ($this->Transactions->saveAfterAdd($transaction, $data['category_id'], $user->last_wallet)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['controller' => 'transactions', 'action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $title = __('Add transaction');
        $this->set(compact('transaction', 'income_categories', 'expense_categories', 'title'));
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

        $transaction = $this->Transactions->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['controller' => 'transactions', 'action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $title = __('Edit transaction');
        $income_categories = $this->Categories->getListIncomeCategories($user);
        $expense_categories = $this->Categories->getListExpenseCategories($user);
        $this->set(compact('transaction', 'income_categories', 'expense_categories', 'title'));
        $this->set('_serialize', ['transaction']);
        $this->render('add');
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

    public function monthlyReport($time_range = 'month', $current = 0)
    {
        $user = $this->getCurrentUserInfo();
        $now = new DateTime();
        $current_wallet = $this->Wallets->get($user->last_wallet);
        //get $time_range and $current
        if ($this->request->is('get')) {
            $data = $this->request->params['pass'];
            if (!empty($data)) {
                $time_range = $data[0];
                $current = $data[1];
            }
        }
        $condition_list = $this->Transactions->getListOfMonthlyReport($current_wallet->id, $now, $current);
        $this->paginate = $condition_list;
        $types = $this->Types->getAllTypes();
        $monthly_reports = $this->Transactions->monthlyReport($current_wallet, $now->format('m'), $now->format('y'));
        $this->set('transactions', $this->paginate($this->Transactions));
        $this->set(compact('current_wallet', 'monthly_reports', 'types'));
        $this->set('title', __('Monthly Report'));
    }

}
