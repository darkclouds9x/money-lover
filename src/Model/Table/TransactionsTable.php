<?php

namespace App\Model\Table;

use App\Model\Entity\Transaction;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Transactions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Categories
 */
class TransactionsTable extends Table
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

        $this->table('transactions');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Wallets', [
            'foreignKey' => 'wallet_id',
            'joinType' => 'INNER'
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
                ->add('balance', 'valid', ['rule' => 'numeric'])
                ->requirePresence('balance', 'create')
                ->notEmpty('balance');

        $validator
                ->allowEmpty('note');

        $validator
                ->add('parent', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('parent');

        $validator
                ->add('done_date', 'valid', ['rule' => 'datetime'])
                ->allowEmpty('done_date');

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
        $rules->add($rules->existsIn(['category_id'], 'Categories'));
        return $rules;
    }

    /**
     * Save transfer money method
     * 
     * @param type $transfer_wallet
     * @param type $receiver_wallet
     * @param type $transfer_transaction
     * @param type $receiver_transaction
     * @return boolean
     */
    public function saveTransfer($transfer_wallet, $receiver_wallet, $transfer_transaction, $receiver_transaction)
    {
        if ($this->save($transfer_transaction) && $this->save($receiver_transaction) && $this->Wallets->save($transfer_wallet) && $this->Wallets->save($receiver_wallet)) {
            return true;
        }
        return false;
    }

    /**
     * Get all transactions of month
     * 
     * @param type $wallet_id
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function getTransactionsOfMonth($wallet_id, $list_month, $list_year)
    {
//        $transactions = $this->find()->where([
//                    'Transactions.wallet_id' => $wallet_id,
//                    'MONTH(Transactions.created)' => $list_month,
//                    'YEAR(Transactions.created)' => $list_year,
//                ])->contain(['Categories'])->all();
        $transactions = $this->find('all', [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'MONTH(Transactions.created)' => $list_month,
                'YEAR(Transactions.created)' => $list_year,
            ],
            'contain' => ['Categories']
        ]);
        return $transactions;
    }

    /**
     * Soft delete all transactions of a category
     * 
     * @param type $category_id
     * @return boolean
     */
    public function deleteAllTransactionsOfCategory($category_id)
    {
        $transactions = $this->find()->where(['category_id' => $category_id])->all();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $this->save($transaction);
        }
        return true;
    }

}
