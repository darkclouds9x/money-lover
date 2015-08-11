<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Category'), ['action' => 'edit', $current_category->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Category'), ['action' => 'delete', $current_category->id], ['confirm' => __('Are you sure you want to delete # {0}?', $current_category->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Categories'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Category'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Wallets'), ['controller' => 'Wallets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Wallet'), ['controller' => 'Wallets', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Transactions'), ['controller' => 'Transactions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?> </li>
    </ul>
</div>

<div class="categories index large-10 medium-9 columns">
    <?=
    $this->Form->create(null, [
        'url' => ['controller' => 'categories', 'action' => 'view']
    ])
    ?>
    <?php 
    foreach ($income_categories as $income_category){
        
    }
        
    var_dump($income_categories);
    $options = [
        __('Income Category') => $income_categories,
        __('Expense Category') => $expense_categories,
    ];?>
    <div class="row">
        <div class="col-sm-6">
            <?php echo $this->Form->select(__('Category'), $options) ?>
        </div>
        <div class="text-center">
            <?= $this->Form->button(__('Change category')) ?>
        </div>
    </div>
    <?= $this->Form->end() ?>

    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('title') ?></th>
                <th><?= $this->Paginator->sort('category_id') ?></th>
                <th><?= $this->Paginator->sort('balance') ?></th>
                <th><?= $this->Paginator->sort('done_date') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $this->Number->format($transaction->id) ?></td>
                    <td><?= h($transaction->title) ?></td>
                    <td>
                        <?= $transaction->has('category') ? $this->Html->link($transaction->category->title, ['controller' => 'Categories', 'action' => 'view', $transaction->category->id]) : '' ?>
                    </td>
                    <td><?= $this->Number->format($transaction->balance) ?></td>
                    <td><?= h($transaction->done_date) ?></td>
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
<script>
    
</script>
