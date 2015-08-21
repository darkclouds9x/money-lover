<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Wallets Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\HasMany $Categories
 */
class WalletsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('wallets');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Categories', [
            'foreignKey' => 'wallet_id'
        ]);
        $this->hasMany('Transactions', [
            'foreignKey' => 'wallet_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
                ->add('id', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('id', 'create');

        $validator
                ->notEmpty('title');

        $validator
                ->add('init_balance', 'valid', ['rule' => 'numeric'])
                ->requirePresence('init_balance', 'create')
                ->notEmpty('init_balance');

        $validator
                ->add('is_current', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('is_current');

        $validator
                ->add('deleted', 'valid', ['rule' => 'date'])
                ->allowEmpty('deleted');

        $validator
                ->add('status', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('status');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }

    /**
     * Get all wallets of method
     * 
     * @param type $user
     * @return type
     */
    public function getAllWalletsOfUser($user_id)
    {
        $wallets = $this->find('list', [
            'conditions' => [
                'Wallets.user_id' => $user_id,
                'Wallets.status' => 1,
            ],
        ]);
        return $wallets;
    }

    /**
     * Count wallets of user method
     * 
     * @param type $user_id
     * @return type
     */
    public function countWallets($user_id)
    {
        $count_wallets = $this->find('all', [
                    'conditions' => [
                        'user_id' => $user_id,
                        'status' => 1,
                    ]
                ])->count();
        return$count_wallets;
    }

    /**
     * Delete wallet method
     * 
     * @param type $user
     * @param type $wallet
     * @return boolean
     */
    public function deleteWallet($user, $wallet)
    {
        $wallet->status = 0;
        $user->total_balance = $user->total_balance - $wallet->current_balance;

        //delete current wallet
        if ($wallet->id == $user->last_wallet) {
            $new_current_wallet = $this->find('all', [
                        'fields' => ['id', 'is_current'],
                        'conditions' => ['user_id' => $user->id, 'id !=' => $wallet->id, 'status' => 1]
                    ])->first();
            if (empty($new_current_wallet)) {

                $user->last_wallet = 0;
            } else {
                $user->last_wallet = $new_current_wallet['id'];
                $wallet->is_current = 0;
                $new_current_wallet['is_current'] = 1;
            }
        }
        // checking number wallets of user
        if ($this->countWallets($user->id) == 1) {
            $user->last_wallet = 0;
        }
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            if (!empty($new_current_wallet)) {
                $this->save($new_current_wallet);
            }
            $this->save($wallet);
            $this->Users->save($user);
            $this->Categories->deleteAllCategoriesOfWallet($wallet->id);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            return false;
        }
        return true;
    }

    /**
     * Save after add new wallet
     * 
     * @param type $wallet
     * @param type $user
     * @return boolean
     */
    public function saveAfterAdd($wallet, $user)
    {
        $wallet->user_id = $user->id;
        $wallet->current_balance = $wallet->init_balance;
        $user->total_balance = $user->total_balance + $wallet->init_balance;
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            $this->save($wallet);
            $default_categories = $this->Categories->addCategoriesByDefault($wallet);
            $this->Categories->saveDefaultCategory($default_categories);
            //if don't have any wallet-> add and set current wallet
            if (empty($user->last_wallet)) {
                $wallet->is_current = 1;
                $user->last_wallet = $wallet->id;
                $this->save($wallet);           
            }
            $this->Users->save($user);
            $conn->commit();
            return true;
        } catch (Exception $ex) {
            $conn->rollback();
            return false;
        }
    }

    /**
     * Save after changing current wallet
     * 
     * @param type $user
     * @param type $current_wallet
     * @param type $last_wallet
     * @return boolean
     */
    public function saveAfterChangeCurrent($user, $current_wallet, $last_wallet)
    {
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            $this->Users->save($user);
            $this->save($current_wallet);
            $this->save($last_wallet);
            $conn->commit();

            return true;
        } catch (Exception $ex) {
            $conn->rollback();
            return false;
        }
    }

    /**
     * Total balance of user
     * 
     * @param type $user_id
     * @return type
     */
    public function totalBalanceOfUser($user_id)
    {
        $total = 0;
        $wallets = $this->find('all', [
            'conditions' => ['user_id' => $user_id, 'status' => 1],
            'fields' => ['current_balance'],
        ]);
        foreach ($wallets as $wallet) {
            $total += $wallet['current_balance'];
        }
        return $total;
    }

}
