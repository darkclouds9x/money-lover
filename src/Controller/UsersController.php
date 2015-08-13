<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Network\Email\Email;
use Cake\Validation\Validator;
use App\Model\Entity\User;

//use Cake\Mailer\Email;

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
        if ($this->Auth->user()) {
            $this->redirect(['controller' => 'transactions', 'action' => 'index']);
        }
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            $user['token'] = $user->createToken($user['email']);
            if ($this->Users->save($user)) {
                $this->_send_activation_email($user);
                $this->Flash->success(__('You have successfully registered! Please check your email to active account!'));
                return $this->redirect(['_name' => 'login']);
            } else {
                $this->Flash->error(__('Registration was failure. Please, try again.'));
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
        if ($this->Auth->user()) {
            $this->redirect(['controller' => 'transactions', 'action' => 'index']);
        }
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if (!empty($user['email']) && ($user['is_actived'] == 0)) {
                $this->_send_activation_email($user);
                $this->Flash->error(__("Your account isn't actived. Please check your email again to active!"));
                return $this->redirect($this->referer());
            } elseif ($user) {
                $this->Auth->setUser($user);
                if (empty($this->Auth->user('last_wallet'))) {
                    return $this->redirect([
                                'controller' => 'Wallets',
                                'action' => 'add',
                    ]);
                } else {
                    return $this->redirect(['controller' => 'transactions', 'action' => 'index']);
                }
            }
            $this->Flash->error(__('Your username or password is incorrect.'));
            return $this->redirect($this->referer());
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
            if ($data['password'] != $data['confirm_password']) {
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
        if (in_array($action, ['changePassword', 'index', 'edit', 'save', 'add'])) {
            return true;
        }

        // All other actions require an id.
        if (empty($this->request->params['pass'][0])) {
            return false;
        }

        return parent::isAuthorized($user);
    }

    /**
     * Send activation email
     * 
     * @param type $user
     */
    private function _send_activation_email($user)
    {
        $email = new Email('default');
        $email->to($user['email'])
                ->subject("Please activate your account")
                ->viewVars(['user' => $user])
                ->template('active')
                ->emailFormat("html")
                ->send();
    }

    /**
     * Send reset password email
     * 
     * @param type $user
     */
    private function _send_reset_password_email($user)
    {
        $email = new Email('default');
        $new_pass = $this->createRandomPassword();
        $user['password'] = $new_pass;
        $this->Users->save($user);
        $email->to($user['email'])
                ->subject("Please activate your account")
                ->viewVars(['user' => $user, 'new_pass' => $new_pass])
                ->template('reset_password')
                ->emailFormat("html")
                ->send();
    }

    /**
     * Active account method
     * @param type $user
     * @param type $token
     * @return type
     */
    public function activeAccount($user, $token)
    {
        if ($this->request->is(['get', 'post'])) {
            $data = $this->request->params['pass'];
            $user = $this->Users->find()->where(['id' => $data[0], 'token' => $data[1]])->first();
            if (!empty($user)) {
                $user->is_actived = 1;
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Your account has been actived.'));

                    $this->Auth->setUser($user->toArray());
                    if (empty($this->Auth->user('last_wallet'))) {
                        return $this->redirect([
                                    'controller' => 'Wallets',
                                    'action' => 'add',
                        ]);
                    } else {
                        return $this->redirect(['_name' => 'index']);
                    }
                }
            } else {
                $this->Flash->error(__("Your account hasn't been actived. Please, try again."));
            }
        }
    }

    /**
     * Reset password method
     * 
     * @return type
     */
    public function resetPassword()
    {
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $user = $this->Users->find('all', [
                        'conditions' => ['email' => $data['email']],
                    ])->first();
            if (!empty($user)) {
                $this->_send_reset_password_email($user);
                $this->Flash->success(__('Please check your email to get new password!'));
                return $this->redirect(['_name' => 'home']);
            } else {
                $this->Flash->error(__("This email isn't exits. Please, try again."));
                return $this->redirect(['_name' => 'resetPass']);
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Enabling Registrations
     * 
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        $this->Auth->allow(['add', 'logout', 'changePassword', 'activeAccount', 'resetPassword']);
    }

    /**
     * Create a random password
     * 
     * @return string
     */
    public function createRandomPassword($length = 7)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
