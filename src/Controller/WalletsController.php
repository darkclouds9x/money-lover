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

            if ($this->Wallets->saveAfterAdd($wallet, $user)) {
                $this->Flash->success(__('The wallet has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The wallet could not be saved. Please, try again.'));
            }
        }

        $title = __('Add wallet');
        $this->set(compact('wallet', 'title'));
        $this->set('_serialize', ['wallet']);
        $this->render('edit');
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

        $wallet = $this->Wallets->get($id);
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
        $title = __('Edit wallet');
        $this->set(compact('wallet', 'title'));
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
        $user = $this->getCurrentUserInfo();
        $wallet = $this->Wallets->get($id);
        if ($this->Wallets->deleteWallet($user, $wallet)) {
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
        $last_wallet = $this->Wallets->get($user->last_wallet);
        if ($this->request->is(['post'])) {
            $current_wallet = $this->Wallets->find()->where(['id' => $this->request->data['wallet_id']])->first();
            $last_wallet->is_current = 0;
            $current_wallet->is_current = 1;
            $user->last_wallet = $this->request->data['wallet_id'];
            if ($this->Wallets->saveAfterChangeCurrent($user, $current_wallet, $last_wallet)) {
                $this->Flash->success(__('The current wallet is changed successfull.'));
                return $this->redirect($this->referer());
            } else {
                $this->Flash->error(__("The current wallet isn't changed."));
                return $this->redirect($this->referer());
            }
        }
    }

}
