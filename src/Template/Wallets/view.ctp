<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Wallet'), ['action' => 'edit', $wallet->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Wallet'), ['action' => 'delete', $wallet->id], ['confirm' => __('Are you sure you want to delete # {0}?', $wallet->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Wallets'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Wallet'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="wallets view large-10 medium-9 columns">
    <h2><?= h($wallet->title) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('User') ?></h6>
            <p><?= $wallet->has('user') ? $this->Html->link($wallet->user->id, ['controller' => 'Users', 'action' => 'view', $wallet->user->id]) : '' ?></p>
            <h6 class="subheader"><?= __('Title') ?></h6>
            <p><?= h($wallet->title) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($wallet->id) ?></p>
            <h6 class="subheader"><?= __('Init Balance') ?></h6>
            <p><?= $this->Number->format($wallet->init_balance) ?></p>
            <h6 class="subheader"><?= __('Is Current') ?></h6>
            <p><?= $this->Number->format($wallet->is_current) ?></p>
            <h6 class="subheader"><?= __('Status') ?></h6>
            <p><?= $this->Number->format($wallet->status) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($wallet->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($wallet->modified) ?></p>
            <h6 class="subheader"><?= __('Deleted') ?></h6>
            <p><?= h($wallet->deleted) ?></p>
        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related Categories') ?></h4>
    <?php if (!empty($wallet->categories)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Title') ?></th>
            <th><?= __('Wallet Id') ?></th>
            <th><?= __('Type Id') ?></th>
            <th><?= __('Parent') ?></th>
            <th><?= __('Is Locked') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Modified') ?></th>
            <th><?= __('Deleted') ?></th>
            <th><?= __('Status') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($wallet->categories as $categories): ?>
        <tr>
            <td><?= h($categories->id) ?></td>
            <td><?= h($categories->title) ?></td>
            <td><?= h($categories->wallet_id) ?></td>
            <td><?= h($categories->type_id) ?></td>
            <td><?= h($categories->parent) ?></td>
            <td><?= h($categories->is_locked) ?></td>
            <td><?= h($categories->created) ?></td>
            <td><?= h($categories->modified) ?></td>
            <td><?= h($categories->deleted) ?></td>
            <td><?= h($categories->status) ?></td>

            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => 'Categories', 'action' => 'view', $categories->id]) ?>

                <?= $this->Html->link(__('Edit'), ['controller' => 'Categories', 'action' => 'edit', $categories->id]) ?>

                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Categories', 'action' => 'delete', $categories->id], ['confirm' => __('Are you sure you want to delete # {0}?', $categories->id)]) ?>

            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
