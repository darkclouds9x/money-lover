<?= $this->assign('title', $title) ?>
<?php var_dump($categories);die; ?>
<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Wallets'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
    </ul>
</div>
<div class="wallets form large-10 medium-9 columns">
    <?= $this->Form->create($transaction) ?>
    <fieldset>
        <legend><?= __('Transfer money between wallets') ?></legend>
        <?php
        echo $this->Form->input('from_wallet', ['options' => $wallets, 'default' => $last_wallet]);
        echo $this->Form->input('to_wallet', ['options' => $wallets, 'default' => __('Select wallet')]);
        echo $this->Form->input('transfer_value');
        echo $this->Form->input('note');
        echo $this->Form->input('category_id', ['options' => $categories, 'default' => __('Select wallet')]);
        echo $this->Form->input('done_date');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
