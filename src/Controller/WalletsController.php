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
        $this->loadModel('Categories');
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
                'Wallets.status' => 1,
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
        $user = $this->getCurrentUserInfo();
        $wallet = $this->Wallets->newEntity();
        if ($this->request->is('post')) {
            $wallet = $this->Wallets->patchEntity($wallet, $this->request->data);
            $wallet->user_id = $user->id;
            $wallet->current_balance = $wallet->init_balance;
            if ($this->Wallets->save($wallet)) {

                //Add wallet after first
                if (empty($user->last_wallet)) {
                    $wallet->is_current = 1;
                    $user->last_wallet = $wallet->id;
                    $default_categories = $this->Categories->addDefaultCategories($wallet);
                    $this->Categories->saveDefaultCategory($default_categories);
                    if ($this->Wallets->save($wallet) && $this->Categories->saveDefaultCategory($default_categories) && $this->Users->save($user)) {
                        $this->Flash->success(__('The wallet has been saved.'));
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $this->Flash->error(__('The wallet could not be saved. Please, try again.'));
                    }
                }

                //Add default categories
                $default_categories = $this->Categories->addDefaultCategories($wallet);
                $this->Categories->saveDefaultCategory($default_categories);
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
     * Soft Delete method
     *
     * @param string|null $id Wallet id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $wallet = $this->Wallets->get($id);
        $wallet->status = 0;

        if ($this->Wallets->save($wallet) && $this->Categories->deleteAllCategoriesOfWallet($wallet->id)) {
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
        $user = $this->getCurrentUserInfo();
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
     * Authorization logic for wallets
     * 
     * @param type $user
     * @return boolean
     */
    public function isAuthorized($user)
    {
        $action = $this->request->params['action'];


        // The add and index actions are always allowed.
        if (in_array($action, ['index', 'view', 'add', 'edit', 'changeCurrentWallet'])) {
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
