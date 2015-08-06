<?php

namespace App\Model\Table;

use App\Model\Entity\Category;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Categories Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Wallets
 * @property \Cake\ORM\Association\BelongsTo $Types
 * @property \Cake\ORM\Association\HasMany $Transactions
 */
class CategoriesTable extends Table
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

        $this->table('categories');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Wallets', [
            'foreignKey' => 'wallet_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Types', [
            'foreignKey' => 'type_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Transactions', [
            'foreignKey' => 'category_id'
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
                ->requirePresence('title', 'create')
                ->notEmpty('title');

        $validator
                ->add('parent', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('parent');

        $validator
                ->add('is_locked', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('is_locked');

        $validator
                ->add('deleted', 'valid', ['rule' => 'date'])
                ->allowEmpty('deleted');

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
        $rules->add($rules->existsIn(['wallet_id'], 'Wallets'));
        $rules->add($rules->existsIn(['type_id'], 'Types'));
        return $rules;
    }

    /**
     * Create default categories after creating a wallet
     * 
     * @param type $user
     * @return Category
     */
    public function addDefaultCategories($wallet)
    {

        $difference_income = new Category([
            'title' => __('Diffference'),
            'wallet_id' => $wallet->id,
            'type_id' => 1,
            'is_locked' => 1,
        ]);

        $difference_expense = new Category([
            'title' => __('Diffference'),
            'wallet_id' => $wallet->id,
            'type_id' => 2,
            'is_locked' => 1,
        ]);
        $loan = new Category([
            'title' => __('Loan'),
            'wallet_id' => $wallet->id,
            'type_id' => 1,
            'is_locked' => 1,
        ]);
        $received = new Category([
            'title' => __('Received'),
            'wallet_id' => $wallet->id,
            'type_id' => 2,
            'is_locked' => 1,
        ]);
        $debt = new Category([
            'title' => __('Debt'),
            'wallet_id' => $wallet->id,
            'type_id' => 2,
            'is_locked' => 1,
        ]);
        $default_categories = [$difference_income, $difference_expense, $loan, $received, $debt];
        return $default_categories;
    }

    /**
     * Saving 
     * 
     * @param type $default_categories
     * @return boolean
     */
    public function saveDefaultCategory($default_categories)
    {
        foreach ($default_categories as $default_category) {
            $this->save($default_category);
        }
        return true;
    }

    /**
     * Get list of income categories
     * 
     * @return type
     */
    public function getListIncomeCategories($user)
    {
        $income_categories = $this->find('list', [
            'conditions' => [
                'Categories.wallet_id' => $user->last_wallet,
                'Categories.type_id' => 1,
            ],
            'limit' => 200]);
        return $income_categories;
    }

    /**
     * Get list of expense categories
     * 
     * @return type
     */
    public function getListExpenseCategories($user)
    {
        $expense_categories = $this->find('list', [
            'conditions' => [
                'Categories.wallet_id' => $user->last_wallet,
                'Categories.type_id' => 2,
            ],
            'limit' => 200]);
        return $expense_categories;
    }

    /**
     * Get id of received category method
     * 
     * @param type $receiver_wallet_id
     * @return type
     */
    public function getReceiverCategoryId($receiver_wallet_id)
    {
        $receiver_category = $this->find()->where([
                'Categories.wallet_id' => $receiver_wallet_id,
                'Categories.title' => 'Received',
                'Categories.is_locked' => 1,
            ])->first();
    return $receiver_category->id;
    }
}
