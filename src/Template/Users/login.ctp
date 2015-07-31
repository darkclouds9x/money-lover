<div class="col-sm-6 col-sm-offset-3">

    <h1 class="text-center"><?= __('Login') ?></h1>
    <?= $this->Form->create() ?>

    <div class="control-group">
        <?php
        echo $this->Form->input('email', array(
            'type' => 'text',
            'label' => array('text' => 'Email address', 'class' => 'sr-only'),
            'placeholder' => 'Enter email address',
            'class' => 'form-control'
        ));
        ?>
    </div>
    <div class="control-group">
        <?php
        echo $this->Form->input('password', array(
            'type' => 'password',
            'label' => array('text' => 'Password', 'class' => 'sr-only'),
            'placeholder' => 'Password',
            'class' => 'form-control'
        ));
        ?>
    </div>
    <div class="control-group">
        <label class="text-center">
            <?php echo $this->Form->checkbox('rememberMe', array('hiddenField' => false, 'value' => '1')); ?>Remember me
        </label>
    </div> 
    <?php echo $this->Form->button('Sign in', array('type' => 'submit', 'class' => 'btn btn-lg btn-primary btn-block')); ?>
    <div class="control-group">
        <label class="text-center">
            <?php echo $this->Html->link(__('Forgot your password'), ['_name' => 'resetPass']); ?>
        </label>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
