<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Network\Email\Email;
use Cake\Validation\Validator;
use App\Model\Entity\User;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id)
    {
        if (!$id) {
            throw new NotFoundException(__('Invalid user'));
        }
        $user = $this->Users->get($id, [
            'contain' => ['Wallets']
        ]);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
//                    var_dump($this->request->data); die;
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
//                $email = new Email('gmail');
//                $email->from(['thanhnt@rikkeisoft.com' => 'My Site'])
//                        ->to('thanhnt07.vn@gmail.com')
//                        ->subject('About')
//                        ->send('My message');
                return $this->redirect(['_name' => 'home']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login method
     * 
     * @return void
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('Your username or password is incorrect.'));
        }
    }

    /**
     * Lougout method
     * 
     * @return void
     */
    public function logout()
    {
        $this->Flash->success(__('You are now logged out.'));
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Change password method
     * @return type
     */
    public function changePassword()
    {
        $id = $this->Auth->user('id');
        $data = $this->request->data;
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $data, [
                'password' => $data['password']
            ]);
            if($data['password'] != $data['confirm_password'] )
            {
                $this->Flash->error(__("The confirm pass isn't equal to new password"));
                return $this->redirect($this->referer());
            }
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['_name' => 'home']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
        $this->set('title', __('Change Password'));
    }

    /**
     * Authorization for action.
     * 
     * @param type $user
     * @return boolean
     */
    public function isAuthorized($user)
    {
        $action = $this->request->params['action'];

        // The add and index actions are always allowed.
        if (in_array($action, ['changePassword', 'index', 'edit', 'save'])) {
            return true;
        }

        // All other actions require an id.
        if (empty($this->request->params['pass'][0])) {
            return false;
        }

        return parent::isAuthorized($user);
    }

    public function active($id, $token)
    {
        
    }
    /**
     * Send activation email
     * 
     * @param type $user
     */
    public function send_activation_email($user)
    {
        $email = new Email('default');
        $email->to($user['email'])
                ->subject('Please activate your account')
                ->template('activate')
                ->viewVars(array('user' => $user))
                ->emailFormat('html')
                ->send();
    }

    /**
     * Enabling Registrations
     * 
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        $this->Auth->allow(['add', 'logout', 'changePassword']);
    }

}
