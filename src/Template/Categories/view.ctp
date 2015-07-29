<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Category'), ['action' => 'edit', $category->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Category'), ['action' => 'delete', $category->id], ['confirm' => __('Are you sure you want to delete # {0}?', $category->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Categories'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Category'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Wallets'), ['controller' => 'Wallets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Wallet'), ['controller' => 'Wallets', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Transactions'), ['controller' => 'Transactions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="categories view large-10 medium-9 columns">
    <h2><?= h($category->title) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Title') ?></h6>
            <p><?= h($category->title) ?></p>
            <h6 class="subheader"><?= __('Wallet') ?></h6>
            <p><?= $category->has('wallet') ? $this->Html->link($category->wallet->title, ['controller' => 'Wallets', 'action' => 'view', $category->wallet->id]) : '' ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($category->id) ?></p>
            <h6 class="subheader"><?= __('Type Id') ?></h6>
            <p><?= $this->Number->format($category->type_id) ?></p>
            <h6 class="subheader"><?= __('Parent') ?></h6>
            <p><?= $this->Number->format($category->parent) ?></p>
            <h6 class="subheader"><?= __('Is Locked') ?></h6>
            <p><?= $this->Number->format($category->is_locked) ?></p>
            <h6 class="subheader"><?= __('Status') ?></h6>
            <p><?= $this->Number->format($category->status) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($category->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($category->modified) ?></p>
            <h6 class="subheader"><?= __('Deleted') ?></h6>
            <p><?= h($category->deleted) ?></p>
        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related Transactions') ?></h4>
    <?php if (!empty($category->transactions)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Category Id') ?></th>
            <th><?= __('Title') ?></th>
            <th><?= __('Balance') ?></th>
            <th><?= __('Note') ?></th>
            <th><?= __('Parent') ?></th>
            <th><?= __('Done Date') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Modified') ?></th>
            <th><?= __('Deleted') ?></th>
            <th><?= __('Status') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($category->transactions as $transactions): ?>
        <tr>
            <td><?= h($transactions->id) ?></td>
            <td><?= h($transactions->category_id) ?></td>
            <td><?= h($transactions->title) ?></td>
            <td><?= h($transactions->balance) ?></td>
            <td><?= h($transactions->note) ?></td>
            <td><?= h($transactions->parent) ?></td>
            <td><?= h($transactions->done_date) ?></td>
            <td><?= h($transactions->created) ?></td>
            <td><?= h($transactions->modified) ?></td>
            <td><?= h($transactions->deleted) ?></td>
            <td><?= h($transactions->status) ?></td>

            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => 'Transactions', 'action' => 'view', $transactions->id]) ?>

                <?= $this->Html->link(__('Edit'), ['controller' => 'Transactions', 'action' => 'edit', $transactions->id]) ?>

                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Transactions', 'action' => 'delete', $transactions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transactions->id)]) ?>

            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
