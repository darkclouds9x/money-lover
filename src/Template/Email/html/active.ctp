Dear <?php echo $user['email']; ?>,
<p>Thank you for registering. Please click the link below to activate your account.</p>
<p>
    <?php
    $url = $this->Url->build(["controller"=>"Users","action"=>"activeAccount",$user['id'],$user['token']],true);
    echo $this->Html->link(__('Active your account'),$url);
    ?>
</p>