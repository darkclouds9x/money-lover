<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\WalletsTable;

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
    public function index()
    {
        $this->paginate = [
            'condition' => [
                'Transactions.wallet_id' => $this->Auth->user('last_wallet'),
            ],
            'contain' => ['Categories']
        ];
        $this->set('transactions', $this->paginate($this->Transactions));
        $wallets = $this->Transactions->Wallets->find('list', ['limit' => 200]);
        $this->set(compact('wallets'));
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
        $transaction = $this->Transactions->newEntity();
        if ($this->request->is('post')) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            $transaction->wallet_id = $this->Auth->user('last_wallet');

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
     * Authorization logic for transactions
     * 
     * @param type $user
     * @return boolean
     */
    public function isAuthorized($user)
    {
        $action = $this->request->params['action'];


        // The add and index actions are always allowed.
        if (in_array($action, ['add', 'edit'])) {
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
