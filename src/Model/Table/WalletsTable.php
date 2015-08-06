<?php

namespace App\Model\Table;

use App\Model\Entity\Wallet;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
                ->allowEmpty('title');

        $validator
                ->add('init_balance', 'valid', ['rule' => 'numeric'])
                ->requirePresence('init_balance', 'create')
                ->notEmpty('init_balance');

        $validator
                ->add('is_current', 'valid', ['rule' => 'numeric'])
                ->requirePresence('is_current', 'create')
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
     * Get Id of current wallet
     * 
     * @param type $user
     * @return type
     */
    public function getCurrentWalletId($user)
    {
        return $user->last_wallet;
    }

    /**
     * Get receiver wallet method
     * 
     * @param type $to_wallet
     * @return type
     */
    public function getReceiverWallet($to_wallet)
    {
        $receiver_wallet = $this->find()->where(['id' => $to_wallet])->first();
        return $receiver_wallet;
    }
    public function getTransferWallet($from_wallet)
    {
        $transfer_wallet = $this->find()->where(['id' => $from_wallet])->first();
        return $transfer_wallet;
    }

}
