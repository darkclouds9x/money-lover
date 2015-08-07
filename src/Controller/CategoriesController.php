<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Categories Controller
 *
 * @property \App\Model\Table\CategoriesTable $Categories
 */
class CategoriesController extends AppController
{

    /**
     * Load Model
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
        $this->loadModel('Transactions');
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
                'Categories.wallet_id' => $user->last_wallet,
                'Categories.status' => 1,
            ],
            'order' => [
                'Categories.id' => 'asc'
            ],
            'contain' => ['Types', 'Wallets'],
        ];
        $wallets = $this->Categories->Wallets->find('list', [
            'conditions' => [
                'Wallets.id' => $user->last_wallet,
            ],
        ]);
        $this->set('categories', $this->paginate($this->Categories));
        $this->set(compact('wallets'));
        $this->set('_serialize', ['categories']);
    }

    /**
     * View method
     *
     * @param string|null $id Category id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $category = $this->Categories->get($id, [
            'contain' => ['Wallets', 'Types', 'Transactions']
        ]);
        $user = $this->getCurrentUserInfo();
        $this->paginate = [
            'conditions' => [
                'Transactions.wallet_id' => $user->last_wallet,
                'Transactions.category_id' => $id
            ],
            'contain' => ['Categories']
        ];
        $this->set('transactions', $this->paginate($this->Transactions));
        $categories = $this->Categories->find('list', [
            'conditions' => [
                'Categories.wallet_id' => $user->id
            ],
            'limit' => 200
        ]);
        $this->set(compact('categories'));
        $this->set('category', $category);
        $this->set('_serialize', ['category']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->getCurrentUserInfo();
        $category = $this->Categories->newEntity();
        if ($this->request->is('post')) {
            $category = $this->Categories->patchEntity($category, $this->request->data);
            $category->user_id = $this->Auth->user('id');
            if ($this->Categories->save($category)) {
                $this->Flash->success(__('The category has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The category could not be saved. Please, try again.'));
            }
        }
        $wallets = $this->Categories->Wallets->find('list', [
            'conditions' => [
                'Wallets.user_id' => $this->Auth->user('id')
            ],
            'limit' => 200]);
        $categories = $this->Categories->find('list')->where([
                    'wallet_id' => $user->last_wallet
                ])->all();
        $types = $this->Categories->Types->find('list', ['limit' => 2]);
        $this->set(compact('category', 'wallets', 'types','categories'));
        $this->set('_serialize', ['category']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Category id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $category = $this->Categories->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $category = $this->Categories->patchEntity($category, $this->request->data);
            $category->user_id = $this->Auth->user('id');
            if ($this->Categories->save($category)) {
                $this->Flash->success(__('The category has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The category could not be saved. Please, try again.'));
            }
        }
        $wallets = $this->Categories->Wallets->find('list', ['limit' => 200]);
        $types = $this->Categories->Types->find('list', ['limit' => 200]);
        $this->set(compact('category', 'wallets', 'types'));
        $this->set('_serialize', ['category']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Category id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $category = $this->Categories->get($id);
        $category->status =0;
        if ($this->Categories->save($category) && $this->Transactions->deleteAllTransactionsOfCategory($category->id)) {
            $this->Flash->success(__('The category has been deleted.'));
        } else {
            $this->Flash->error(__('The category could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Authorization logic for categories
     * 
     * @param type $user
     * @return boolean
     */
    public function isAuthorized($user)
    {
        $action = $this->request->params['action'];


        // The add and index actions are always allowed.
        if (in_array($action, ['index', 'view', 'add', 'edit', 'delete'])) {
            return true;
        }
        // All other actions require an id.
        if (empty($this->request->params['pass'][0])) {
            return false;
        }

        // Check that the wallet belongs to the current user.
        $id = $this->request->params['pass'][0];
        $category = $this->Categories->get($id);
        if ($category->user_id == $user['id']) {
            return true;
        }
        return parent::isAuthorized($user);
    }

}
