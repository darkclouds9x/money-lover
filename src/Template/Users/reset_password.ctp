<legend><?= __('Reset password') ?></legend>
<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create() ?>
    <fieldset>
        <?php
            echo $this->Form->input('email');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
