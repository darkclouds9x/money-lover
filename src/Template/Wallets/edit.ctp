<?= $this->start('actions') ?>
<ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $wallet->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $wallet->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Wallets'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
</ul>
<?= $this->end() ?>

<div class="wallets form large-10 medium-9 columns">
    <?= $this->Form->create($wallet) ?>
    <fieldset>
        <legend><?php echo $title ?></legend>
        <?php
            echo $this->Form->input('title');
            echo $this->Form->input('init_balance');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
