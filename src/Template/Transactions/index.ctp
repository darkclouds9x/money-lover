<?= $this->assign('title', $title) ?>
<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Transaction'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('New Wallet'), ['controller' => 'Wallets', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('Transfer Money Between Wallets'), ['_name' => 'transferMoney']) ?></li>
    </ul>
</div>
<div class="transactions index large-10 medium-9 columns">
    <div class=" row change-wallet">
        <div class="col-sm-6">
            <?=
            $this->Form->create(null, [
                'url' => ['_name' => 'changeWallet']
            ])
            ?>
            <?php echo $this->Form->input('wallet_id', ['options' => $wallets, 'label' => 'Select wallet', 'default' => $last_wallet]); ?>
            <?= $this->Form->button(__('Change wallet')) ?>
            <?= $this->Form->end() ?>
        </div>

        <div class="col-sm-6">
            <?=
            $this->Form->create(null, [
                'url' => ['controller' => 'transactions', 'action' => 'index']
            ])
            ?>
            <?php
            $options = [
                'day' => __("Day"),
                'week' => __("Week"),
                'month' => __("Month"),
                'quarter' => __("Quarter"),
                'year' => __("Year"),
                'all' => __("All"),
            ];
            ?>
            <label for="time_range"><?= _('Select parent') ?></label>
            <?php
            echo $this->Form->select('time_range', $options, ['default' => 2]);
            ?>
            <?= $this->Form->button(__('Change time range')) ?>
            <?= $this->Form->end() ?>
        </div>

    </div> <!-- end .change-wallet-->

    <div class="row monthly-report">
        <div class="row date-range">
            <div class="col-sm-2">
                <?php if ($list_month == 1): ?>
                    <?= $this->Html->link(__('Last month'), ['action' => 'index', ($list_month = 12), ($list_year - 1)]) ?>
                <?php else : ?>
                    <?= $this->Html->link(__('Last month'), ['action' => 'index', ($list_month - 1), $list_year]) ?>
                <?php endif ?>
            </div>
            <div class="col-sm-8">
                <?php if (($list_month == $current_month) && ($list_year == $current_year)): ?>
                    <h3 class="text-center"><?= __('Transactions List of This month') ?></h3>
                <?php else: ?>
                    <h3 class="text-center">
                        <?= __('Transactions List of ') ?>
                        <?php echo $list_month . '/ ' . $list_year; ?>
                    </h3>
                <?php endif ?>
            </div>
            <div class="col-sm-2">
                <?php if ($list_month == 12): ?>
                    <?= $this->Html->link(__('Next month'), ['action' => 'index', ($list_month = 1), ($list_year + 1)]) ?>
                <?php else: ?>
                    <?= $this->Html->link(__('Next month'), ['action' => 'index', ($list_month + 1), $list_year]) ?>
                <?php endif ?>
            </div>
        </div>
        <?php if (count($transactions) == 0) : ?>
            <h3><?= h(__('No transaction!')) ?></h3>
        <?php else : ?>


            <div class="row transactions-list">
                <div class=" col-sm-12">
                    <?php foreach ($types as $type): ?>
                        <div class="col-sm-12">
                            <table class="table-bordered table-hover" cellpadding="0" cellspacing="0">
                                <thead> 
                                    <?php if ($type->title == 'income'): ?>
                                    <h4 class="text-center"><?= __('Income'); ?></h4>

                                <?php else: ?>
                                    <h4 class="text-center"><?= __('Expense') ?></h4>
                                <?php endif ?>
                                </thead>
                                <thead>
                                    <tr>
                                        <th><?= $this->Paginator->sort('Time') ?></th>
                                        <th><?= $this->Paginator->sort('title') ?></th>
                                        <th><?= $this->Paginator->sort('category_id') ?></th>
                                        <th><?= $this->Paginator->sort('amount') ?></th>
                                        <th class="actions"><?= __('Actions') ?></th>
                                    </tr>
                                </thead>
                                <?php foreach ($transactions as $transaction): ?>
                                    <?php if ($transaction->category->type->title == $type->title): ?>
                                        <tbody>
                                            <tr>
                                                <td><?= h($transaction->done_date) ?></td>
                                                <td><h4><?= h($transaction->title) ?></h4>
                                                    <h6><?= h($transaction->note) ?></h6></td>
                                                <td>
                                                    <?= $transaction->has('category') ? $this->Html->link($transaction->category->title, ['controller' => 'Categories', 'action' => 'view', $transaction->category->id]) : '' ?>
                                                </td>
                                                <td><?= $this->Number->format($transaction->amount) ?></td>
                                                <td class="actions">
                                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $transaction->id]) ?>
                                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $transaction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    <?php endif ?>
                                <?php endforeach; ?>

                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div> <!--end .transactions-list -->

        </div> <!-- end .montly-report -->
    <?php endif ?>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>