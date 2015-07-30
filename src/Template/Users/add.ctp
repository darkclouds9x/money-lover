<legend><?= __('Sign up') ?></legend>
<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <?php
            echo $this->Form->input('email');
            echo $this->Form->input('password');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Sign up')) ?>
    <?= $this->Form->end() ?>
</div>
