<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$cakeDescription = 'Money Lover';

use Cake\Routing\Router;
?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?= $cakeDescription ?>:
            <?= $this->fetch('title') ?>
        </title>
        <?= $this->Html->meta('icon') ?>

        <?= $this->Html->css('bootstrap.min.css') ?>
        <?= $this->Html->css('font-awesome.css') ?>
        <?= $this->Html->css('base.css') ?>
        <?= $this->Html->css('cake.css') ?>
        <?= $this->Html->css('style.css') ?>

        <?= $this->Html->script('jquery.min'); ?>
        <?= $this->Html->script('bootstrap.min.js') ?>

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
    </head>
    <body>
        <div id="header">
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
                            <li><a href="<?= Router::url(['controller' => 'transactions', 'action' => 'index']) ?>"><?= __('Transacton') ?></a></li>
                            <li><a href="<?= Router::url(['controller' => 'categories', 'action' => 'index']) ?>"><?= __('Category') ?></a></li>
                            <li><a href="<?= Router::url(['controller' => 'wallets', 'action' => 'index']) ?>"><?= __('Wallets') ?></a></li>
                        <?php } ?>
                        <li><a href=""><?= __('About') ?></a></li>
                        <li><a href=""><?= __('Contact') ?></a></li>

                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (!$authUser) { ?>
                            <li><a href="<?= Router::url(['_name' => 'login']) ?>"><?= __('Login') ?></a></li>
                            <li class="active"><a href="<?= Router::url(['_name' => 'signup']) ?>"><?= __('Sign Up') ?></a></li>
                        <?php } else { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo($authUser['email']) ?><span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="<?= Router::url(['_name' => 'logout']) ?>"><?= __('Logout') ?></a></li>
                                    <li><a href="<?= Router::url(['_name' => 'changePass']) ?>"><?= __('Change Password') ?></a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </nav>
        </div> <!-- end .row navbar-->

        <div id="container">
            <div id="content">
                <?= $this->Flash->render() ?>
                <?= $this->Flash->render('auth') ?>
                <?php echo $this->element('errors') ?>
                <div class="row">
                    <?= $this->fetch('content') ?>
                </div>
            </div>
            <footer>
            </footer>
        </div>
    </body>
</html>
