<div class="col-sm-6 col-sm-offset-3">

    <h1 class="text-center"><?= __('Sign up') ?></h1>
    <?= $this->Form->create($user) ?>

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
    <?php echo $this->Form->button(__('Sign up'), array('type' => 'submit', 'class' => 'btn btn-lg btn-primary btn-block')); ?>
    <?php echo $this->Form->end(); ?>
</div>
