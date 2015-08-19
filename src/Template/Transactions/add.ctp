<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Transactions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
    </ul>
</div>
<div class="transactions form large-10 medium-9 columns">
    <?= $this->Form->create($transaction) ?>
    <?php
    $options = [
        __('Income Category') => $income_categories,
        __('Expense Category') => $expense_categories,
    ];
    ?>
    <fieldset>
        <legend><?= __('Add Transaction') ?></legend>
        <?php
            echo $this->Form->select('category_id', $options);
            echo $this->Form->input('title');
            echo $this->Form->input('amount');
            echo $this->Form->input('note');
            echo $this->Form->input('done_date');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
