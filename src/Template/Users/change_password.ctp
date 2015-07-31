<?= $this->assign('title', $title) ?>
<div class="col-sm-6 col-sm-offset-3">

    <h1 class="text-center"><?= __('Change Password') ?></h1>
    <?php if (!empty($authUser)): ?>

        <?= $this->Form->create('User') ?>

        <!--<h2 class="form-signin-heading">Please sign in</h2>-->
        <div class="control-group">

            <div class="control-group">
                <?php
                echo $this->Form->input('password', array(
                    'type' => 'password',
                    'label' => array('text' => __('New Password'), 'class' => 'sr-only'),
                    'placeholder' => __('New Password'),
                    'class' => 'form-control'
                ));
                ?>
            </div>
            <div class="control-group">
                <?php
                echo $this->Form->input('confirm_password', array(
                    'type' => 'password',
                    'label' => array('text' => __('Confirm Password'), 'class' => 'sr-only'),
                    'placeholder' => __('Confirm Password'),
                    'class' => 'form-control'
                ));
                ?>
            </div>
            <?php echo $this->Form->button(__('Update'), array('type' => 'submit', 'class' => 'btn btn-lg btn-primary btn-block')); ?>
            <?php echo $this->Form->end(); ?>
        <?php else: ?>
            <?php
            __("Don't login! Please");
            echo $this->Html->link(__('Login'), ['_name' => 'changePass']);
            ?>
        <?php endif ?>
    </div>
</div>