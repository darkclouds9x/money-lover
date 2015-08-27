<?= $this->start('actions') ?>
<ul class="side-nav">
        <li><?= $this->Html->link(__('New Category'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Wallets'), ['controller' => 'Wallets', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Wallet'), ['controller' => 'Wallets', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Transactions'), ['controller' => 'transactions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?></li>
</ul>
<?= $this->end() ?>

<div class="categories index large-10 medium-9 columns">
    <div class=" row change-wallet">
        <?=
        $this->Form->create(null, [
            'url' => ['_name' => 'changeWallet']
        ])
        ?>
        <div class="col-sm-4">
            <?php echo $this->Form->input('wallet_id', ['options' => $wallets, 'default' => $last_wallet]); ?>
            <?= $this->Form->button(__('Change wallet')) ?>
        </div>
        <?= $this->Form->end() ?>
    </div> <!-- end .change-wallet-->
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('title') ?></th>
                <th><?= $this->Paginator->sort('wallet_id') ?></th>
                <th><?= $this->Paginator->sort('type_id') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= $this->Number->format($category->id) ?></td>
                    <td><?= $this->Html->link($category->title, ['controller' => 'Categories', 'action' => 'view', $category->id]) ?></td>
                    <td>
                        <?= $category->has('wallet') ? $this->Html->link($category->wallet->title, ['controller' => 'Wallets', 'action' => 'view', $category->wallet->id]) : '' ?>
                    </td>
                    <td><?= h($category->type->title) ?></td>
                    <td><?= h($category->created) ?></td>
                    <td class="actions">
                        <?php if ($category->is_locked == 1): ?>
                            <?= h('Locked') ?>
                        <?php else : ?>
                            <?= $this->Html->link(__('View'), ['action' => 'view', $category->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $category->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $category->id], ['confirm' => __('Are you sure you want to delete # {0}?', $category->id)]) ?>
                        <?php endif ?>
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
