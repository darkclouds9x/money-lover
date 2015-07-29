<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Transaction'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
    </ul>
</div>
<div class="transactions index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('category_id') ?></th>
            <th><?= $this->Paginator->sort('title') ?></th>
            <th><?= $this->Paginator->sort('balance') ?></th>
            <th><?= $this->Paginator->sort('parent') ?></th>
            <th><?= $this->Paginator->sort('done_date') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?= $this->Number->format($transaction->id) ?></td>
            <td>
                <?= $transaction->has('category') ? $this->Html->link($transaction->category->title, ['controller' => 'Categories', 'action' => 'view', $transaction->category->id]) : '' ?>
            </td>
            <td><?= h($transaction->title) ?></td>
            <td><?= $this->Number->format($transaction->balance) ?></td>
            <td><?= $this->Number->format($transaction->parent) ?></td>
            <td><?= $this->Number->format($transaction->done_date) ?></td>
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
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
