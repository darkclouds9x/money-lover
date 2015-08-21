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
        <?= $this->Html->css('font-awesome.min.css') ?>
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
            <?php echo $this->element('navbar'); ?>
        </div> <!-- end #row header-->

        <div id="container">
            <div class="col-sm-2 left sidenav-actions" id="actions">
                <nav class="navbar">
                    <h3 class="text-success text-center"><?= __('Actions') ?></h3>
                    <?= $this->fetch('actions') ?>
                </nav>
            </div>
            <div class="col-sm-10 right" id="content">
                <?= $this->Flash->render() ?>
                <?= $this->Flash->render('auth') ?>
                <?php echo $this->element('errors') ?>
                <div class="row">
                    <?= $this->fetch('content') ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </body>
</html>
