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
    public function getAllWalletsOfUser($user)
    {
        $wallets = $this->find('list', [
            'conditions' => [
                'Wallets.user_id' => $user->id,
                'Wallets.status' => 1,
            ],
            'limit' => 200
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
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            $this->saveAfterDeleleWallet($user, $wallet);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            return false;
        }
        return true;
    }

    /**
     * Save user and wallet after deleting wallet
     * 
     * @param type $user
     * @param type $wallet
     * @return boolean
     */
    public function saveAfterDeleleWallet($user, $wallet)
    {
        $wallet->status = 0;
        $user->total_balance = $user->total_balance - $wallet->current_balance;

        // checking number wallets of user
        if ($this->countWallets($user->id) == 1) {
            $user->last_wallet = 0;
        }

        if ($this->save($wallet) && $this->Users->save($user) && $this->Categories->deleteAllCategoriesOfWallet($wallet->id)) {
            return true;
        } else {
            return false;
        }
    }

}
