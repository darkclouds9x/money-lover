<?php
use Cake\Routing\Router;
?>

 <nav class="navbar navbar-inverse navbar-static-top navbar-fixed-top" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="">Money Lover</a>
                </div>
                <div class="navbar-collapse collapse" id="main-menu">
                    <ul class="nav navbar-nav"> 
                        <li class="active"><a href="<?= Router::url(['_name' => 'home']) ?>"><?= __('Home') ?></a></li>
                        <?php if ($authUser) { ?>
                            <li><a href="<?= Router::url(['controller' => 'transactions', 'action' => 'index']) ?>"><?= __('Transaction') ?></a></li>
                            <li><a href="<?= Router::url(['controller' => 'categories', 'action' => 'index']) ?>"><?= __('Category') ?></a></li>
                            <li><a href="<?= Router::url(['controller' => 'wallets', 'action' => 'index']) ?>"><?= __('Wallets') ?></a></li>
                        <?php } ?>
                        <li><a href=""><?= __('About') ?></a></li>
                        <li><a href=""><?= __('Contact') ?></a></li>

                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (!$authUser) { ?>
                            <li><a href="<?= Router::url(['_name' => 'login']) ?>"><?= __('Login') ?></a></li>
                            <li class=><a href="<?= Router::url(['_name' => 'signup']) ?>"><?= __('Sign Up') ?></a></li>
                        <?php } else { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <?php
                                    if (!empty($current_wallet)) {
                                        echo($current_wallet['title'] . ':' . $current_wallet['current_balance'] );
                                        echo ' | ';
                                    }
                                    echo($authUser['email']);
                                    ?>
                                    <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="<?= Router::url(['_name' => 'logout']) ?>"><?= __('Logout') ?></a></li>
                                    <li><a href="<?= Router::url(['_name' => 'changePass']) ?>"><?= __('Change Password') ?></a></li>
                                </ul>
                            </li>
<?php } ?>
                    </ul>
                </div>
            </nav>
        </div> <!-- end #row header-->