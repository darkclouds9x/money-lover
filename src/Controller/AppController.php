<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\I18n;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
        $this->loadModel('Wallets');
        $this->loadModel('Categories');
        $this->loadModel('Types');
        $this->loadModel('Transactions');

        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => '/',
            'logoutRedirect' => '/',
            'unauthorizedRedirect' => $this->referer()
        ]);
        $this->set('authUser', $this->Auth->user());
        $this->set('currentWallet', $this->getCurrentWallet());
        // Allow the display action so our pages controller
        // continues to work.
        $this->Auth->allow(['display']);
    }

    public function afterLogin()
    {
        Time::setToStringFormat('YYYY-MM-dd');
        $time = new Time($this->Auth->user('date_of_birth'));
        if ($time->isToday()) {
            // Greet user with a happy birthday message
            $this->set(compact('time'));
            $this->Flash->success(__('Happy birthday to you...'));
        }
    }

    /**
     * Get current user
     * 
     * @return $userInfo
     */
    public function getCurrentUserInfo()
    {
        $id = $this->Auth->user('id');
        $userInfo = $this->Users->find()->where(['id' => $id])->first();
        return $userInfo;
    }

    /**
     * Get all wallets of users
     * 
     * @return type
     */
    public function getAllWalletsOfUser()
    {
        $id = $this->Auth->user('id');
        $wallets = $this->Wallets->getAllWalletsOfUser($id);
        return $wallets;
    }
    /**
     * Change locale method
     * @return type
     */
    public function changeLocale()
    {
        if ($this->request->is('get')) {
            $data = $this->request->data;
            if ($data['lang'] == 'en') {
                I18n::locale('en_US');
                return $this->redirect($this->referer());
            } elseif ($data['locale'] == 'vi') {
                I18n::locale('vi_VI');
                return $this->redirect($this->referer());
            }
        }
    }

    /**
     * Get current wallet info
     * 
     * @return type
     */
    public function getCurrentWallet()
    {
        $user = $this->getCurrentUserInfo();
        if (!empty($user)) {
            $current_wallet = $this->Wallets->find('all', [
                'conditions' => ['id' => $user->last_wallet],
                'fields' => ['id', 'title', 'current_balance'],
            ])->first();
            return $current_wallet;
        }
    }

    /**
     * Allow display data before loging
     * 
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        $this->Auth->allow(['display']);
    }

}
