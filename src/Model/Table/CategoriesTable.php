<?php

namespace App\Model\Table;

use App\Model\Entity\Category;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Database\Exception;

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
     * @param type $wallet
     * @return Category
     */
    public function addDefaultCategories($wallet)
    {
        $difference_income = new Category([
            'title' => __('Difference'),
            'wallet_id' => $wallet->id,
            'type_id' => 1,
            'is_locked' => 1,
        ]);
        $difference_expense = new Category([
            'title' => __('Difference'),
            'wallet_id' => $wallet->id,
            'type_id' => 2,
            'is_locked' => 1,
        ]);
        $loan = new Category([
            'title' => __('Loan'),
            'wallet_id' => $wallet->id,
            'type_id' => 2,
            'is_locked' => 1,
        ]);
        $received = new Category([
            'title' => __('Received'),
            'wallet_id' => $wallet->id,
            'type_id' => 1,
            'is_locked' => 1,
        ]);
        $debt = new Category([
            'title' => __('Debt'),
            'wallet_id' => $wallet->id,
            'type_id' => 1,
            'is_locked' => 1,
        ]);
        $default_categories = [$difference_income, $received, $difference_expense, $loan, $debt];
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
                        'Categories.status' => 1,
                    ],
                ])->toArray();
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
                        'Categories.status' => 1,
                    ],
                ])->toArray();
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
                    'Categories.status' => 1,
                ])->first();
        return $receiver_category->id;
    }

    /**
     * Soft delete 
     * 
     * @param type $wallet_id
     * @return boolean
     */
    public function deleteAllCategoriesOfWallet($wallet_id)
    {
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            $this->saveCategories($wallet_id);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            return false;
        }
        return true;
    }

    /**
     * Save many categories
     * 
     * @param type $wallet_id
     * @return boolean
     */
    public function saveCategories($wallet_id)
    {
        $categoriesTable = TableRegistry::get('Categories');
        $categories = $categoriesTable->find()->where(['wallet_id' => $wallet_id])->all();
        $categoriesTable->connection()->transactional(function() use ($categoriesTable, $categories) {
            foreach ($categories as $categorie) {
                $categorie->status = 0;
                if ($categoriesTable->save($categorie, ['atomic' => false]) == false) {
                    throw new Exception(__("Can't delete all categories of this wallet"));
                }
            }
        });
        return true;
    }

    /**
     * Get id of difference category
     * 
     * @param type $wallet_id
     * @return type
     */
    public function getDifferentCategoryId($wallet_id, $type_id)
    {
        $diffrentCategory = $this->find()->where(['wallet_id' => $wallet_id, 'title' => 'Difference', 'type_id' => $type_id])->first();
        return $diffrentCategory->id;
    }

}
