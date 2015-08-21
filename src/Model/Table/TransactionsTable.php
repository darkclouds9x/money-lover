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
                ->add('done_date', 'valid', ['rule' => 'date'])
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
        $transactions = $this->find('all', [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date)' => $list_month,
                'YEAR(Transactions.done_date)' => $list_year,
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
     * Condition to list all transactions of user
     * 
     * @param type $wallet_id
     * @return array
     */
    public function conditionAll($wallet_id)
    {
        $condition_all = [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        return $condition_all;
    }

    /**
     * Get all transactions before month
     * 
     * @param type $wallet_id
     * @param type $list_month
     * @param type $list_year
     * @return type
     */
    public function getTransactionsBeforeMonth($wallet_id, $list_month, $list_year)
    {
        $transactions = $this->find('all', [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date) <' => $list_month,
                'YEAR(Transactions.done_date) <=' => $list_year,
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
            if (!$this->moveTransactionsToDifferentCategory($category_id, $type_id, $wallet_id)) {
                throw new Exception();
            }
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
                    throw new Exception();
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

    /**
     * Save data after delete transaction
     * 
     * @param type $transaction
     * @return boolean
     */
    public function saveAfterDelete($transaction)
    {
        $current_wallet = $this->Wallets->get($transaction->wallet_id);
        $current_categories = $this->Categories->get($transaction->category_id);

        if ($current_categories->type_id == 1) {
            $current_wallet->current_balance = $current_wallet->current_balance - $transaction->amount;
        } else {
            $current_wallet->current_balance = $current_wallet->current_balance + $transaction->amount;
        }
        $transaction->status = 0;
        if ($this->save($transaction) && $this->Wallets->save($current_wallet)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Saving transaction after adding
     * 
     * @param type $transaction
     * @param type $category_id
     * @param type $wallet_id
     * @return boolean
     */
    public function saveAfterAdd($transaction, $category_id, $wallet_id)
    {
        $category = $this->Categories->find()->where(['id' => $category_id])->first();
        $wallet = $this->Wallets->get($wallet_id);
        $transaction->wallet_id = $wallet_id;
        if ($category->type_id == 1) {
            $wallet->current_balance = $wallet->current_balance + $transaction->amount;
        } elseif ($category->type_id == 2) {
            $wallet->current_balance = $wallet->current_balance - $transaction->amount;
        }

        //Saving data with transaction
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            $this->save($transaction);
            $this->Wallets->save($wallet);

            $conn->commit();
            return true;
        } catch (Exception $ex) {
            $conn->rollback();
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
                $start->setDate($now->year, 1, 1);
                $end->setDate($now->year, 3, 31);
                break;
            case 2:
                $start->setDate($now->year, 4, 1);
                $end->setDate($now->year, 6, 30);
                break;
            case 3:
                $start->setDate($now->year, 7, 1);
                $end->setDate($now->year, 9, 30);
                break;
            case 4:
                $start->setDate($now->year, 10, 1);
                $end->setDate($now->year, 12, 31);
                break;
        }
        return [$start, $end];
    }

    /**
     * Change time range to list transactions
     * 
     * @param type $time_range
     * @param type $wallet_id
     * @param type $now
     * @param type $current
     * @return type
     */
    public function changeTimeRange($time_range, $wallet_id, $now, $current)
    {
        switch ($time_range) {
            case 'day' :
                $condition_list = $this->conditionDay($wallet_id, $now);
                break;
            case 'week' :
                $condition_list = $this->conditionWeek($wallet_id, $now, $current);
                break;
            case 'month' :
                $condition_list = $this->conditionMonth($wallet_id, $now);
                break;
            case 'quarter' :
                $condition_list = $this->conditionQuarter($wallet_id, $now);
                break;
            case 'year' :
                $condition_list = $this->conditionYear($wallet_id, $now);
                break;
            default :
                $condition_list = $this->conditionAll($wallet_id);
                break;
        }
        return $condition_list;
    }

    /**
     * Get title of transaction list by time range
     * 
     * @param type $time_range
     * @param type $list_day
     * @param type $list_month
     * @param type $list_year
     * @return string
     */
    public function titleOfTransactionsList($time_range, $time, $current)
    {
        switch ($time_range) {
            case 'day':
                if ($time->isWithinNext(0)) {
                    $titleOfTransactionsList = __('Today');
                } elseif ($time->wasWithinLast(1)) {
                    $titleOfTransactionsList = __('Yesterday');
                } elseif ($time->isWithinNext(1)) {
                    $titleOfTransactionsList = __('Tomorrow');
                } else {
                    $titleOfTransactionsList = $time->day . '/ ' . $time->month . '/ ' . $time->year;
                }
                break;

            case 'month':
                if ($time->isThisMonth()) {
                    $titleOfTransactionsList = __('This Month');
                } elseif ($time->wasWithinLast('1 month')) {
                    $titleOfTransactionsList = __('Last Month');
                } elseif ($time->isWithinNext('1 month')) {
                    $titleOfTransactionsList = __('Next Month');
                } else {
                    $titleOfTransactionsList = $time->month . '/ ' . $time->year;
                }
                break;
            case 'year':
                if ($time->isThisYear()) {
                    $titleOfTransactionsList = __('This Year');
                } elseif ($time->wasWithinLast('1 year')) {
                    $titleOfTransactionsList = __('Last Year');
                } elseif ($time->isWithinNext('1 year')) {
                    $titleOfTransactionsList = __('Next Year');
                } else {
                    $titleOfTransactionsList = $time->year;
                }
                break;
            case 'week':
                $now = Time::now()->weekOfYear;
                $day_of_week = Time::now()->dayOfWeek;
                $start = new DateTime((-$day_of_week + 7 * $current + 1) . ' Days');
                $end = new DateTime((7 - $day_of_week + 7 * $current) . ' Day');
                $current_week = $time->weekOfYear;
                if ($now == $current_week) {
                    $titleOfTransactionsList = __('This Week');
                } elseif ($current_week === ($now - 1)) {
                    $titleOfTransactionsList = __('Last Week');
                } elseif (($current_week === ($now + 1))) {
                    $titleOfTransactionsList = __('Next Week');
                } else {
                    $titleOfTransactionsList = $start->format('d/m/o') . ' => ' . $end->format('d/m/o');
                }
                break;
            case 'quarter' :
                $date = new Time();
                $now_quarter = ceil(( $date->month) / 3);
                $time_quarter = $time->toQuarter();

                if (($now_quarter == $time_quarter) && ($date->year == $time->year)) {
                    $titleOfTransactionsList = __('This Quarter');
                } elseif ((($now_quarter - $time_quarter === 1) && ($now->year === $time->year)) || ( $now_quarter === 1 && $time_quarter === 4 && ( $now->year - $time->year === 1))) {
                    $titleOfTransactionsList = __('Last Quarter');
                } elseif ((($now_quarter - $time_quarter === -1) && ($now->year === $time->year)) || ( $now_quarter === 4 && $time_quarter === 1 && ( $now->year - $time->year === -1))) {
                    $titleOfTransactionsList = __('Next Quarter');
                } else {
                    $titleOfTransactionsList = __('Q') . $time_quarter . '/ ' . $time->year;
                }
                break;
            default :
                $titleOfTransactionsList = __('All');
                break;
        }
        return $titleOfTransactionsList;
    }

    /**
     * Get time to list data
     * 
     * @param type $time_range
     * @param type $now
     * @param type $current
     * @return type
     */
    public function getTimeToList($time_range, $now, $current)
    {
        switch ($time_range) {
            case 'day':
                $now->modify($current . ' days');
                break;
            case 'month':
                $now->modify($current . ' months');
                break;
            case 'year':
                $now->modify($current . ' years');
                break;
            case 'week':
                $now->modify($current . ' weeks');
                break;
            case 'quarter':
                $current = $current * 3;
                $now->modify($current . ' months');
                break;
        }
        return $now;
    }

    /**
     * Get new $current after changing time range
     * 
     * @param type $time_to_list
     * @param type $time_range
     * @return type
     */
    public function getNewCurrentAfterChangeTimeRange($time_to_list, $time_range)
    {

        $now = new DateTime;
        $current_time = new DateTime;
        $current_time->setDate($time_to_list->year, $time_to_list->month, $time_to_list->day);
        $interval = date_diff($now, $current_time);
        switch ($time_range) {
            case 'day':
                $current = $interval->format('%R%a');
                break;
            case 'week':
                $current = floor($interval->format('%R%a') / 7);
                break;
            case 'month':
                $current = $interval->format('%R%m') + $interval->format('%R%y') * 12;
                break;
            case 'year' :
                $current = $interval->format('%R%y');
                break;
            case 'quarter' :
                $current = ceil($interval->format('%R%m') / 3) + $interval->format('%R%y') * 4;
                break;
            default :
                $current = 0;
        }
        return $current;
    }

    /**
     * Get conditions to list transaction for monthly report
     * 
     * @param type $wallet_id
     * @param type $now
     * @param type $current
     * @return array
     */
    public function getListOfMonthlyReport($wallet_id, $now, $current)
    {
        $now->modify($current . ' months');
        $condition_month = [
            'conditions' => [
                'Transactions.wallet_id' => $wallet_id,
                'Transactions.status' => 1,
                'MONTH(Transactions.done_date)' => $now->format('m'),
                'YEAR(Transactions.done_date)' => $now->format('y'),
            ],
            'contain' => ['Categories.Types'],
            'order' => ['created' => 'ASC'],
        ];
        return $condition_month;
    }

}
