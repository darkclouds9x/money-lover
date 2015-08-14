<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?=
            $this->Form->postLink(
                    __('Delete'), ['action' => 'delete', $category->id], ['confirm' => __('Are you sure you want to delete # {0}?', $category->id)]
            )
            ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Wallets'), ['controller' => 'Wallets', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Wallet'), ['controller' => 'Wallets', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Transactions'), ['controller' => 'Transactions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?></li>
    </ul>
</div>
<div class="categories form large-10 medium-9 columns">
    <?= $this->Form->create($category) ?>
    <?php
    $options = [
        0 => __("Don't belong to any parent category"),
        __('Income Category') => $income_categories,
        __('Expense Category') => $expense_categories,
    ];
    ?>
    <fieldset>
        <legend><?= __('Edit Category') ?></legend>
        <?php
        echo $this->Form->input('title');
        echo $this->Form->input('wallet_id', ['options' => $wallets]);
        echo $this->Form->input('type_id');
        ?>
        <label for="parent"><?= _('Select parent') ?></label>
        <?php
        echo $this->Form->select('parent', $options);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
