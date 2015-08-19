<?php

namespace App\Model\Table;

use App\Model\Entity\Transaction;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Database\Exception;
use DateTime;

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
                ->requirePresence('amount', 'create')
                ->notEmpty('amount');

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
    public function getTransactionsOfMonth($wallet_id, $now)
    {
        $transactions = $this->find('all', [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date)' => $now->month,
                'YEAR(Transactions.done_date)' => $now->year,
            ],
            'contain' => ['Categories']
        ]);
        return $transactions;
    }

    /**
     * Get condition to get transaction of a day
     * 
     * @param type $wallet_id
     * @param type $now
     * @param type $current
     * @return array
     */
    public function conditionDay($wallet_id, $now)
    {
        $condition_day = [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'DAY(Transactions.done_date)' => $now->day,
                'MONTH(Transactions.done_date)' => $now->month,
                'YEAR(Transactions.done_date)' => $now->year,
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        return $condition_day;
    }

    /**
     * Condition to get transactions of month
     * 
     * @param type $wallet_id
     * @param type $now
     * @param type $current
     * @return array
     */
    public function conditionMonth($wallet_id, $now)
    {
        $condition_month = [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date)' => $now->month,
                'YEAR(Transactions.done_date)' => $now->year,
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        return $condition_month;
    }

    /**
     * Condition to list transactions of year
     * 
     * @param type $wallet_id
     * @param type $now
     * @param type $current
     * @return array
     */
    public function conditionYear($wallet_id, $now)
    {
        $condition_year = [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'YEAR(Transactions.done_date)' => $now->year,
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        return $condition_year;
    }

    /**
     * Condition to list transactions of week
     * 
     * @param type $wallet_id
     * @param type $now
     * @return array
     */
    public function conditionWeek($wallet_id, $now, $current)
    {
        $day_of_week = $now->dayOfWeek;

        $condition_week = [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status ' => 1,
                'Transactions.done_date >=' => new DateTime((-$day_of_week + 7 * $current + 1) . ' Days'),
                'Transactions.done_date <=' => new DateTime((7 - $day_of_week + 7 * $current) . ' Day'),
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        return $condition_week;
    }

    /**
     * Condition to list transactions of quarter
     * 
     * @param type $wallet_id
     * @param type $now
     * @return type
     */
    public function conditionQuarter($wallet_id, $now)
    {
        $time = $this->getTimeOfQuarter($now);
        $condition_quarter = [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'Transactions.done_date >' => $time[0],
                'Transactions.done_date <=' => $time[1],
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        return $condition_quarter;
    }

    /**
     * Get all transactions before month
     * 
     * @param type $wallet_id
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function getTransactionsBeforeMonth($wallet_id, $now)
    {
        $transactions = $this->find('all', [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date) <' => $now->month,
                'YEAR(Transactions.done_date) <=' => $now->year,
            ],
            'contain' => ['Categories']
        ]);
        return $transactions;
    }

    /**
     * Get all transactions before month
     * 
     * @param type $wallet_id
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function getTransactionsAfterMonth($wallet_id, $now)
    {
        $transactions = $this->find('all', [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date) >' => $now->month,
                'YEAR(Transactions.done_date) >=' => $now->year,
            ],
            'contain' => ['Categories']
        ]);
        return $transactions;
    }

    /**
     * Get all transactions of wallets
     */
    public function getAllTransactionsOfWallet($wallet_id)
    {
        $transactions = $this->find('all', [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
            ]
        ]);
        return $transactions;
    }

    /**
     * Soft delete all transactions of a category
     * 
     * @param type $category_id
     * @return boolean
     */
    public function deleteAllTransactionsOfCategory($category_id, $type_id, $wallet_id)
    {
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            $this->moveTransactionsToDifferentCategory($category_id, $type_id, $wallet_id);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            return false;
        }
        return true;
    }

    /**
     * Save many transactions
     * 
     * @param type $category_id
     * @param type $wallet_id
     * @return boolean
     */
    public function moveTransactionsToDifferentCategory($category_id, $type_id, $wallet_id)
    {
        $transactionsTable = TableRegistry::get('Transactions');
        $transactions = $transactionsTable->find()->where(['category_id' => $category_id])->all();
        $difference_category_id = $this->Categories->getDifferentCategoryId($wallet_id, $type_id);
        $transactionsTable->connection()->transactional(function() use ($transactionsTable, $transactions, $difference_category_id) {
            foreach ($transactions as $transaction) {
                $transaction->category_id = $difference_category_id;
                if ($transactionsTable->save($transaction, ['atomic' => false]) == false) {
                    throw new Exception(__("Can't delete transaction of this category"));
                }
            }
        });
        return true;
    }

    /**
     * Computing income,expense, balance of month
     * 
     * @param type $transactions
     * @return type
     */
    public function computingIncomeAndExpense($transactions)
    {
        $income = (float) 0;
        $expense = (float) 0;
        foreach ($transactions as $transaction) {
            if ($transaction->category->type_id == 1) {
                $income = $income + $transaction->amount;
            } elseif ($transaction->category->type_id == 2) {
                $expense = $expense + $transaction->amount;
            }
        }
        $balance = $income - $expense;
        return [$income, $expense, $balance];
    }

    /**
     * Monthly report method
     * 
     * @param type $wallet
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function monthlyReport($wallet, $list_month, $list_year)
    {
        $before_transactions = $this->getTransactionsBeforeMonth($wallet->id, $list_month, $list_year);
        $current_transactions = $this->getTransactionsOfMonth($wallet->id, $list_month, $list_year);

        $before_report = $this->computingIncomeAndExpense($before_transactions);
        $current_report = $this->computingIncomeAndExpense($current_transactions);

        $opening_balance = $wallet->init_balance + $before_report[2];
        $ending_balance = $wallet->init_balance + $current_report[2];

        return [$opening_balance, $ending_balance, $current_report[0], $current_report[1], $current_report[2]];
    }

    /**
     * Computing total report
     * @param type $wallet_id
     * @return type
     */
    public function totalReport($wallet_id)
    {
        $transactions = $this->getAllTransactionsOfWallet($wallet_id);
        $total_report = $this->computingIncomeAndExpense($transactions);
        return $total_report;
    }

    public function saveAfterDelete($transaction)
    {
        $current_wallet = $this->Wallets->get($transaction->wallet_id);
        $current_categories = $this->Categories->get($transaction->category_id);
        if ($current_categories->type_id == 1) {
            $current_wallet->current_balance = $current_wallet->current_balance - $transaction->balance;
        } else {
            $current_wallet->current_balance = $current_wallet->current_balance + $transaction->balance;
        }
        $transaction->status = 0;
        if ($this->save($transaction) && $this->Wallets->save($current_wallet)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set value for transfer transaction
     * 
     * @param type $data
     * @param type $transfer_wallet_id
     * @param type $receiver_wallet_title
     * @return type
     */
    public function setTransferTransaction($data, $transfer_wallet_id, $receiver_wallet_title)
    {
        $transfer_transaction = $this->newEntity([
            'wallet_id' => $transfer_wallet_id,
            'category_id' => $data['category_id'],
            'title' => __('Transfer Money'),
            'amount' => $data['amount'],
            'note' => __('Transfer money to ') . $receiver_wallet_title,
        ]);
        return $transfer_transaction;
    }

    /**
     * Set value for receiver transaction
     * 
     * @param type $data
     * @param type $receiver_wallet_id
     * @param type $transfer_wallet_title
     * @return type
     */
    public function setReceiverTransaction($data, $receiver_wallet_id, $transfer_wallet_title)
    {
        $receiver_transaction = $this->newEntity([
            'wallet_id' => $receiver_wallet_id,
            'category_id' => $this->Categories->getReceiverCategoryId($receiver_wallet_id),
            'title' => __('Transfer Money'),
            'amount' => $data['amount'],
            'note' => __('Received from ') . $transfer_wallet_title,
        ]);
        return $receiver_transaction;
    }

    public function getTimeOfQuarter($now)
    {
        $start = new DateTime();
        $end = new DateTime();

        $quarter = $now->toQuarter();
        switch ($quarter) {
            case 1:
                $start->setDate($now->year,1,1);
                $end->setDate($now->year, 3, 31);
                break;
            case 2:
                $start->setDate($now->year,4,1);
                $end->setDate($now->year, 6, 30);
                break;
            case 3:
                $start->setDate($now->year,7,1);
                $end->setDate($now->year, 9, 30);
                break;
            case 4:
                $start->setDate($now->year,10,1);
                $end->setDate($now->year, 12, 31);
                break;
        }
        return [$start, $end];
    }

}
