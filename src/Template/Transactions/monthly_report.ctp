<?= $this->assign('title', $title) ?>
<?= $this->start('actions') ?>
<ul class="side-nav">
    <li><?= $this->Html->link(__('New Transaction'), ['action' => 'add']) ?></li>
    <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
    <li><?= $this->Html->link(__('New Wallet'), ['controller' => 'Wallets', 'action' => 'add']) ?></li>
    <li><?= $this->Html->link(__('Transfer Money Between Wallets'), ['_name' => 'transferMoney']) ?></li>
</ul>
<?= $this->end() ?>

<?php if (count($transactions) == 0) : ?>
    <h3><?= h(__('No transaction!')) ?></h3>
<?php else : ?>

<div class="row">
    <div class=" col-sm-6 col-sm-offset-3">

            <h3 class="title"><?= __('Review')?></h3>

            <table class="table table-condensed">
                <thead>
                  <tr>
                    <th><?= __('Opening Balance')?></th>
                    <th><?= __('Ending Balance')?></th>
                    <th><?= __('Net Income')?></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?php echo $monthly_reports[0]?></td>
                    <td><?php echo $monthly_reports[1]?></td>
                    <td><?php echo $monthly_reports[4]?></td>
                  </tr>
                </tbody>
            </table>
    </div>
</div>


    <div class="row transactions-list">
        <div class=" col-sm-12">
            <?php foreach ($types as $type): ?>
                <div class="col-sm-6">
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
<?php endif ?>

<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
    </ul>
    <p><?= $this->Paginator->counter() ?></p>
</div>