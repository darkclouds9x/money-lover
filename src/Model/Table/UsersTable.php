<?php

namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $Wallets
 */
class UsersTable extends Table
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

        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->hasMany('Wallets', [
            'foreignKey' => 'user_id'
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
                ->add('email', 'valid', ['rule' => 'email'])
                ->requirePresence('email', 'create')
                ->notEmpty('email');

        $validator
                ->requirePresence('password', 'true')
                ->add('password', [
                    'minLength' => [
                        'rule' => ['minLength', 5],
                        'message' => __('Password must have at least 5 characters')
                    ]
                ])
                ->notEmpty('password');
         $validator
                ->requirePresence('confirm_password', 'update')
                ->add('confirm_password', [
                    'minLength' => [
                        'rule' => ['minLength', 5],
                        'message' => __('Confirm Password must have at least 5 characters')
                    ]
                ])
                ->notEmpty('confirm_password');

        $validator
                ->add('is_actived', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('is_actived');

        $validator
                ->add('last_wallet', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('last_wallet');

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
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }

}
