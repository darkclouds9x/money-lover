<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Transaction;
use Cake\I18n\Time;
use Cake\Database\Connection;

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
    public function index($list_month = null, $list_year = null)
    {
        $user = $this->getCurrentUserInfo();
        $current_month = Time::now()->month;
        $current_year = Time::now()->year;
        $current_wallet = $this->Wallets->get($user->last_wallet);

        if (empty($list_month) || empty($list_year)) {
            $list_month = $current_month;
            $list_year = $current_year;
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
            'contain' => ['Categories.Types']
        ];
        $this->set('transactions', $this->paginate($this->Transactions));
        $wallets = $this->Wallets->getAllWalletsOfUser($user);
        $mothly_reports = $this->Transactions->monthlyReport($current_wallet, $list_month, $list_year);
        $last_wallet = $user->last_wallet;
        $this->set(compact('current_wallet', 'wallets', 'last_wallet', 'list_month', 'list_year', 'current_month', 'current_year', 'mothly_reports'));
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
//                $this->commit();
//                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
//                $this->rollback();
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
                return $this->redirect(['action' => 'index']);
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
//        return $this->redirect(['action' => 'index']);
//        $this->index();
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

            $transfer_transaction = $this->Transactions->newEntity([
                'wallet_id' => $transfer_wallet->id,
                'category_id' => $data['category_id'],
                'title' => __('Transfer Money'),
                'amount' => $data['amount'],
                'note' => __('Transfer money to ') . $receiver_wallet->title,
            ]);

            $receiver_transaction = $this->Transactions->newEntity([
                'wallet_id' => $receiver_wallet->id,
                'category_id' => $this->Categories->getReceiverCategoryId($receiver_wallet->id),
                'title' => __('Transfer Money'),
                'amount' => $data['amount'],
                'note' => __('Received from ') . $transfer_wallet->title,
            ]);
            if ($transfer_wallet->checkCreatedWallet($data['done_date']['month'], $data['done_date']['year'])) {
                $transfer_wallet->current_balance = $transfer_wallet->current_balance - $transfer_transaction->amount;
                $receiver_wallet->current_balance = $receiver_wallet->current_balance + $receiver_transaction->amount;
            }
            if ($this->Transactions->saveTransfer($transfer_wallet, $receiver_wallet, $transfer_transaction, $receiver_transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $expense_categories = $this->Categories->getListExpenseCategories($user);
        $wallets = $this->Transactions->Wallets->find('list', [
            'conditions' => [
                'Wallets.user_id' => $user->id,
            ],
            'limit' => 200]);
        $this->set(compact('wallets', 'expense_categories', 'user', 'transaction'));
        $this->set('_serialize', ['transaction']);
        $this->set('title', __('Transfer Money Between Wallets'));
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
        if (in_array($action, ['index', 'view', 'add', 'edit', 'delete', 'transferMoney'])) {
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
