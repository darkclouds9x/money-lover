<?= $this->start('actions') ?>
<ul class="side-nav">
        <li><?= $this->Html->link(__('New Wallet'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('Transfer Money Between Wallets'), ['_name' => 'transferMoney']) ?></li>
</ul>
<?= $this->end() ?>

<div class="wallets index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('init_balance') ?></th>
            <th><?= $this->Paginator->sort('current_balance') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th><?= $this->Paginator->sort('modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($wallets as $wallet): ?>
        <tr>
            <td><?= $this->Number->format($wallet->id) ?></td>
            <td><?= h($wallet->title) ?></td>
            <td><?= $this->Number->format($wallet->init_balance) ?></td>
            <td><?= $this->Number->format($wallet->current_balance) ?></td>
            <td><?= h($wallet->created) ?></td>
            <td><?= h($wallet->modified) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $wallet->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $wallet->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $wallet->id], ['confirm' => __('Are you sure you want to delete # {0}?', $wallet->id)]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
