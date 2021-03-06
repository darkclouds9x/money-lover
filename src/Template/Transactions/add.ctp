<?= $this->start('actions') ?>
<ul class="side-nav">
        <li><?= $this->Html->link(__('List Transactions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
</ul>
<?= $this->end() ?>

<div class="transactions form large-10 medium-9 columns">
    <?= $this->Form->create($transaction) ?>

    <?php
    $options = [
        __('Income Category') => $income_categories,
        __('Expense Category') => $expense_categories,
    ];
    ?>
    <fieldset>
        <legend><?php echo $title ?></legend>
        <?php
        echo $this->Form->select('category_id', $options);
        echo $this->Form->input('title');
        echo $this->Form->input('amount');
        echo $this->Form->input('note');
        echo $this->Form->input('done_date', ['type' => 'date']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
