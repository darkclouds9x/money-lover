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
    <div class="change-wallet">
        <?=
        $this->Form->create(null, [
            'url' => ['_name' => 'changeWallet']
        ])
        ?>
        <?php echo $this->Form->input('wallet_id', ['options' => $wallets, 'default' => $last_wallet]); ?>
        <?= $this->Form->button(__('Change wallet')) ?>
        <?= $this->Form->end() ?>
    </div>

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
                    <h3 class="text-center"><?= __('Monthly Report of This month') ?></h3>
                <?php else: ?>
                    <h3 class="text-center">
                        <?php
                        __('Monthly Report of ');
                        echo $list_month . '/ ' . $list_year;
                        ?></h3>
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
            <div class=" row statistic">
                <div class="col-sm-6 col-sm-offset-3">
                    <table class="table table-hover table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th class="text-center"><?= __('Init Balance') ?></th>
                                <th class="text-center"><?= __('Income') ?></th>
                                <th class="text-center"><?= __('Expense') ?></th>
                                <th class="text-center"><?= __('Curent Balance') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php foreach ($mothly_reports as $monthly_report): ?>
                                    <td class="text-center"><?php echo $monthly_report; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row transactions-list">
                <br><h3 class="text-center"><?= __('Transactions List of This month') ?></h3>
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('id') ?></th>
                            <th><?= $this->Paginator->sort('title') ?></th>
                            <th><?= $this->Paginator->sort('category_id') ?></th>
                            <th><?= $this->Paginator->sort('type_id') ?></th>
                            <th><?= $this->Paginator->sort('balance') ?></th>
                            <th><?= $this->Paginator->sort('created') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= $this->Number->format($transaction->id) ?></td>
                                <td><?= h($transaction->title) ?></td>
                                <td>
                                    <?= $transaction->has('category') ? $this->Html->link($transaction->category->title, ['controller' => 'Categories', 'action' => 'view', $transaction->category->id]) : '' ?>
                                </td>
                                <td><?= h($transaction->category->type->title) ?></td>
                                <td><?= $this->Number->format($transaction->balance) ?></td>
                                <td><?= h($transaction->created) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $transaction->id]) ?>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $transaction->id]) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $transaction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
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