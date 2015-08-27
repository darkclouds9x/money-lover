<?= $this->start('actions') ?>
<ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Transaction'), ['action' => 'edit', $transaction->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Transaction'), ['action' => 'delete', $transaction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Transactions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Transaction'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?> </li>
</ul>
<?= $this->end() ?>

<div class="transactions view large-10 medium-9 columns">
    <h2><?= h($transaction->title) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Category') ?></h6>
            <p><?= $transaction->has('category') ? $this->Html->link($transaction->category->title, ['controller' => 'Categories', 'action' => 'view', $transaction->category->id]) : '' ?></p>
            <h6 class="subheader"><?= __('Title') ?></h6>
            <p><?= h($transaction->title) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($transaction->id) ?></p>
            <h6 class="subheader"><?= __('Balance') ?></h6>
            <p><?= $this->Number->format($transaction->balance) ?></p>
            <h6 class="subheader"><?= __('Parent') ?></h6>
            <p><?= $this->Number->format($transaction->parent) ?></p>
            <h6 class="subheader"><?= __('Done Date') ?></h6>
            <p><?= h($transaction->done_date) ?></p>
            <h6 class="subheader"><?= __('Status') ?></h6>
            <p><?= $this->Number->format($transaction->status) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($transaction->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($transaction->modified) ?></p>
            <h6 class="subheader"><?= __('Deleted') ?></h6>
            <p><?= h($transaction->deleted) ?></p>
        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('Note') ?></h6>
            <?= $this->Text->autoParagraph(h($transaction->note)) ?>
        </div>
    </div>
</div>
