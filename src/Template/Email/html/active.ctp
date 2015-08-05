Dear <?php echo $user['username']; ?>,
<p>Thank you for registering. Please click the link below to activate your account.</p>
<p>
    <?php
    echo $this->Html->link('Activate', array(
        'controller' => 'users',
        'action'     => 'activate',
        $user['id'],
        $user['token'],
        'full_base'  => true
    ));
    ?>
</p>