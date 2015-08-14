Dear <?php echo $user->email; ?>,
<?php echo "Your new password is " . $new_pass ?>
<p><?php __('Please change your password after loging!') ?></p>
<p><?php echo __('After login, please change you password immediately.'); ?></p>
<p>Please click the link below to login your account.</p>
<p>
    <?php
    $url = $this->Url->build(["controller"=>"Users","action"=>"login"],true);
    echo $this->Html->link(__('Active your account'),$url);
    ?>
</p>