<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Transaction;

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
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $user = $this->getCurrentUserInfo();
        $this->paginate = [
            'conditions' => [
                'Transactions.wallet_id' => $user->last_wallet,
            ],
            'contain' => ['Categories']
        ];
        $this->set('transactions', $this->paginate($this->Transactions));
        $wallets = $this->Transactions->Wallets->find('list', [
            'conditions' => [
                'Wallets.user_id' => $this->Auth->user('id')
            ],
            'limit' => 200
        ]);
        $last_wallet = $user->last_wallet;
        $this->set(compact('wallets', 'last_wallet'));
        $this->set('_serialize', ['transactions']);
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
        $transaction = $this->Transactions->newEntity();
        if ($this->request->is('post')) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            $transaction->wallet_id = $this->Auth->user('last_wallet');
            $transaction->user_id = $this->Auth->user('id');
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $categories = $this->Transactions->Categories->find('list', [
            'conditions' => [
                'Categories.wallet_id' => $user->last_wallet,
            ],
            'limit' => 200]);
        $this->set(compact('transaction', 'categories'));
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
     * Delete method
     *
     * @param string|null $id Transaction id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transaction = $this->Transactions->get($id);
        if ($this->Transactions->delete($transaction)) {
            $this->Flash->success(__('The transaction has been deleted.'));
        } else {
            $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
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
            $receiver_wallet = $this->Wallets->getReceiverWallet($data['to_wallet']);
            $transfer_wallet = $this->Wallets->getTransferWallet($data['from_wallet']);
            
            $transfer_transaction = $this->Transactions->newEntity([
                'wallet_id' => $transfer_wallet->id,
                'category_id' => $data['category_id'],
                'title' => __('Transfer Money'),
                'balance' => $data['balance'],
                'note' => __('Transfer money to ') . $receiver_wallet->title,
            ]);
            
            $receiver_transaction = $this->Transactions->newEntity([
                'wallet_id' => $receiver_wallet->id,
                'category_id' => $this->Categories->getReceiverCategoryId($receiver_wallet->id),
                'title' => __('Transfer Money'),
                'balance' => $data['balance'],
                'note' => __('Received from ') . $transfer_wallet->title,
            ]);

            $transfer_wallet->init_balance = $transfer_wallet->init_balance - $transfer_transaction->balance;
            $receiver_wallet->init_balance = $receiver_wallet->init_balance + $receiver_transaction->balance;
//            var_dump($receiver_wallet); var_dump($receiver_transaction);die;
            if ($this->Transactions-> saveTransfer($transfer_wallet, $receiver_wallet, $transfer_transaction, $receiver_transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
//        $categories = $this->Transactions->Categories->find('list', [
//            'conditions' => [
//                'Categories.wallet_id' => $user->last_wallet,
//            ],
//            'limit' => 200]);
        $expense_categories = $this->Categories->getListExpenseCategories($user);
        $wallets = $this->Transactions->Wallets->find('list', [
            'conditions' => [
                'Wallets.user_id' => $user->id,
            ],
            'limit' => 200]);
//        pr($categories);die;
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
        if (in_array($action, [ 'index', 'view', 'add', 'edit', 'transferMoney'])) {
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
