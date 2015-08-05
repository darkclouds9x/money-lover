<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Wallets Controller
 *
 * @property \App\Model\Table\WalletsTable $Wallets
 */
class WalletsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
        $this->loadModel('Transactions');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'conditions' => [
                'Wallets.user_id' => $this->Auth->user('id'),
            ]
        ];
        $this->set('wallets', $this->paginate($this->Wallets));
        $this->set('_serialize', ['wallets']);
    }

    /**
     * View method
     *
     * @param string|null $id Wallet id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $wallet = $this->Wallets->get($id, [
            'contain' => ['Users', 'Categories']
        ]);
        $this->set('wallet', $wallet);
        $this->set('_serialize', ['wallet']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $id = $this->Auth->user('id');
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        $wallet = $this->Wallets->newEntity();
        if ($this->request->is('post')) {
            $wallet = $this->Wallets->patchEntity($wallet, $this->request->data);
            $wallet->user_id = $id;
            if ($this->Wallets->save($wallet)) {
                if (empty($user->last_wallet)) {
                    $wallet->is_current = 1;
                    $user->last_wallet = $wallet->id;
                    if ($this->Wallets->save($wallet) && $this->Users->save($user)) {
                        $this->Flash->success(__('The wallet has been saved.'));
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $this->Flash->error(__('The wallet could not be saved. Please, try again.'));
                    }
                }
                $this->Flash->success(__('The wallet has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The wallet could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('wallet'));
        $this->set('_serialize', ['wallet']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Wallet id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $wallet = $this->Wallets->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $wallet = $this->Wallets->patchEntity($wallet, $this->request->data);
            $wallet->user_id = $this->Auth->user('id');
            if ($this->Wallets->save($wallet)) {
                $this->Flash->success(__('The wallet has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The wallet could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('wallet'));
        $this->set('_serialize', ['wallet']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Wallet id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $wallet = $this->Wallets->get($id);
        if ($this->Wallets->delete($wallet)) {
            $this->Flash->success(__('The wallet has been deleted.'));
        } else {
            $this->Flash->error(__('The wallet could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Change current method
     * 
     * @return void
     */
    public function changeCurrentWallet()
    {
        $id = $this->Auth->user('id');
        $user = $this->Users->find()->where(['id' => $id])->first();
        $last_wallet = $this->Wallets->find()->where(['id' => $user->last_wallet])->first();
        if ($this->request->is(['post'])) {
            $current_wallet = $this->Wallets->find()->where(['id' => $this->request->data['wallet_id']])->first();
            $last_wallet->is_current = 0;
            $current_wallet->is_current = 1;
            $user->last_wallet = $this->request->data['wallet_id'];
            if ($this->Users->save($user) && ($this->Wallets->save($current_wallet) && $this->Wallets->save($last_wallet))) {
                $current_wallet->is_current = 1;
                $this->Flash->success(__('The current wallet is changed successfull.'));
                return $this->redirect(['_name' => 'home']);
            } else {
                $this->Flash->error(__("The current wallet isn't changed."));
                return $this->redirect(['_name' => 'home']);
            }
        }
    }

    /**
     *  Transfer money between wallets
     * 
     * return void
     */
    public function transferMoney()
    {
        $id= $this->Auth->user('id');
        $user = $this->Users->find()->where(['id' => $id])->first();
        $transfer_transaction = $this->Transactions->newEntity();
        $receiver_transaction = $this->Transactions->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $receiver_wallet = $this->Wallets->find()->where(['id' => $data['to_wallet']]);
            $transfer_wallet = $this->Wallets->find()->where(['id' => $data['from_wallet']]);
            $transfer_transaction = $this->Users->patchEntity($transfer_transaction, $data, [
                'wallet_id' => $data['from_wallet'],
                'category_id' => $data['category_id'],
                'title' => __('Transfer Money'),
                'balance' => $data['transfer_value'],
                'note' => __('Transfer money to ') . $receiver_wallet->title,
            ]);
//             $receiver_transaction = $this->Users->patchEntity($receiver_transaction, $data, [
//                'wallet_id' => $data['to_wallet'],
//                'category_id' => 6,
//                'title' => __('Transfer Money'),
//                'balance' => $data['transfer_value'],
//                'note' => __('Received money from ') . $transfer_wallet->title,
//            ]);

            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            }
        }
        $categories = $this->Transactions->Categories->find('list', [
            'conditions' => [
                'Categories.wallet_id' => $this->$user['last_wallet']
            ],
            'limit' => 200]);
        $this->set(compact('transaction', 'categories'));
        $this->set('_serialize', ['transaction']);
        $this->set('title', __('Transfer Money Between Wallets'));
    }

    /**
     * Authorization logic for wallets
     * 
     * @param type $user
     * @return boolean
     */
    public function isAuthorized($user)
    {
        $action = $this->request->params['action'];


        // The add and index actions are always allowed.
        if (in_array($action, ['index', 'view', 'add', 'edit', 'changeCurrentWallet', 'transferMoney'])) {
            return true;
        }
        // All other actions require an id.
        if (empty($this->request->params['pass'][0])) {
            return false;
        }

        // Check that the wallet belongs to the current user.
        $id = $this->request->params['pass'][0];
        $wallet = $this->Wallets->get($id);
        if ($wallet->user_id == $user['id']) {
            return true;
        }
        return parent::isAuthorized($user);
    }

}
