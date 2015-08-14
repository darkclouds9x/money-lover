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
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

$this->layout = false;

if (!Configure::read('debug')):
    throw new NotFoundException();
endif;

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport"    content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author"      content="Sergey Pozhilov (GetTemplate.com)">

        <title><?= __('Money lover: Homepage') ?></title>

        <link rel="shortcut icon" href="/img/gt_favicon.png">

        <!-- Bootstrap itself -->
        <?= $this->Html->css('bootstrap.min.css') ?>

        <!-- Custom styles -->
        <link rel="stylesheet" href="/css/magister.css">
        <?= $this->Html->css('style.css') ?>

        <!-- Fonts -->
        <?= $this->Html->css('font-awesome.min.css') ?>
        <link href='http://fonts.googleapis.com/css?family=Wire+One' rel='stylesheet' type='text/css'>
    </head>

    <!-- use "theme-invert" class on bright backgrounds, also try "text-shadows" class -->
    <body class="theme-invert">

        <!--        <nav class="mainmenu">
                    <div class="container">
                        <div class="dropdown">
                            <button type="button" class="navbar-toggle" data-toggle="dropdown"><span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                             <a data-toggle="dropdown" href="#">Dropdown trigger</a> 
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                <li><a href="<?= Router::url(['_name' => 'home']) ?>" class="active"><?= __('Home') ?></a></li>
        <?php if ($authUser) { ?>
                                            <li><a href="<?= Router::url(['controller' => 'transactions', 'action' => 'index']) ?>"><?= __('Transaction') ?></a></li>
                                            <li><a href="<?= Router::url(['controller' => 'categories', 'action' => 'index']) ?>"><?= __('Category') ?></a></li>
                                            <li><a href="<?= Router::url(['controller' => 'wallets', 'action' => 'index']) ?>"><?= __('Wallets') ?></a></li>
        <?php } ?>
                                <li><a href="#about">About me</a></li>
                                <li><a href="#themes">Themes</a></li>
                                <li><a href="#contact">Get in touch</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>-->

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
        </div> <!-- end #row header-->
        <!-- Main (Home) section -->
        <section class="section" id="head">
            <div class="container">

                <div class="row">
                    <div class="col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1 text-center">	

                        <!-- Site Title, your name, HELLO msg, etc. -->
                        <h1 class="title"><?= __('Money lover') ?></h1>

                        <!-- Short introductory (optional) -->
                        <h3 class="subtitle">
                            <?= __('The simplest way to manage your personal finances.') ?>
                        </h3>

                        <!-- Nice place to describe your site in a sentence or two -->
                        <?php if (empty($authUser)) { ?>
                            <div class="row">
                                <a href="<?= Router::url(['_name' => 'login']) ?>" class="btn btn-default btn-lg"><?= __('Login') ?></a>
                                <a href="<?= Router::url(['_name' => 'signup']) ?>" class="btn btn-success btn-lg"><?= __('Sign Up') ?></a>
                            </div>
                        <?php } else { ?>
                            <a href="<?= Router::url(['controller' => 'transactions', 'action' => 'index']) ?>" class="btn btn-success btn-lg"><?= __('Check Monthly Report') ?></a>
                        <?php } ?>
                    </div> <!-- /col -->
                </div> <!-- /row -->

            </div>
        </section>

        <!-- Second (About) section -->
        <section class="section" id="about">
            <div class="container">

                <h2 class="text-center title">About me</h2>
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-2">    
                        <h5><strong>Where's my lorem ipsum?<br></strong></h5>
                        <p>Well, here it is: Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorum, ullam, ducimus, eaque, ex autem est dolore illo similique quasi unde sint rerum magnam quod amet iste dolorem ad laudantium molestias enim quibusdam inventore totam fugit eum iusto ratione alias deleniti suscipit modi quis nostrum veniam fugiat debitis officiis impedit ipsum natus ipsa. Doloremque, id, at, corporis, libero laborum architecto mollitia molestiae maxime aut deserunt sed perspiciatis quibusdam praesentium consectetur in sint impedit voluptates! Deleniti, sequi voluptate recusandae facere nostrum?</p>    
                    </div>
                    <div class="col-sm-4">
                        <h5><strong>More, more lipsum!<br></strong></h5>    
                        <p>Tempore, eos, voluptatem minus commodi error aut eaque neque consequuntur optio nesciunt quod quibusdam. Ipsum, voluptatibus, totam, modi perspiciatis repudiandae odio ad possimus molestias culpa optio eaque itaque dicta quod cupiditate reiciendis illo illum aspernatur ducimus praesentium quae porro alias repellat quasi cum fugiat accusamus molestiae exercitationem amet fugit sint eligendi omnis adipisci corrupti. Aspernatur.</p>    
                        <h5><strong>Author links<br></strong></h5>    
                        <p><a href="http://be.net/pozhilov9409">Behance</a> / <a href="https://twitter.com/serggg">Twitter</a> / <a href="http://linkedin.com/pozhilov">LinkedIn</a> / <a href="https://www.facebook.com/pozhilov">Facebook</a></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Third (Works) section -->
        <section class="section" id="themes">
            <div class="container">

                <h2 class="text-center title">More Themes</h2>
                <p class="lead text-center">
                    Huge thank you to all people who publish<br>
                    their photos at <a href="http://unsplash.com">Unsplash</a>, thank you guys!
                </p>
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-2">
                        <div class="thumbnail">
                            <img src="/screenshots/sshot1.jpg" alt="">
                            <div class="caption">
                                <h3>Thumbnail label</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque doloribus enim vitae nam cupiditate eius at explicabo eaque facere iste.</p>
                                <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="thumbnail">
                            <img src="/screenshots/sshot4.jpg" alt="">
                            <div class="caption">
                                <h3>Thumbnail label</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque doloribus enim vitae nam cupiditate eius at explicabo eaque facere iste.</p>
                                <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-sm-offset-2">
                        <div class="thumbnail">
                            <img src="/screenshots/sshot5.jpg" alt="">
                            <div class="caption">
                                <h3>Thumbnail label</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque doloribus enim vitae nam cupiditate eius at explicabo eaque facere iste.</p>
                                <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="thumbnail">
                            <img src="/screenshots/sshot3.jpg" alt="">
                            <div class="caption">
                                <h3>Thumbnail label</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque doloribus enim vitae nam cupiditate eius at explicabo eaque facere iste.</p>
                                <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Fourth (Contact) section -->
        <section class="section" id="contact">
            <div class="container">

                <h2 class="text-center title">Get in touch</h2>

                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2 text-center">
                        <p class="lead">Have a question about this template, or want to suggest a new feature?</p>
                        <p>Feel free to email me, or drop me a line in Twitter!</p>
                        <p><b>gt@gettemplate.com</b><br><br></p>
                        <ul class="list-inline list-social">
                            <li><a href="https://twitter.com/serggg" class="btn btn-link"><i class="fa fa-twitter fa-fw"></i> Twitter</a></li>
                            <li><a href="https://github.com/pozhilov" class="btn btn-link"><i class="fa fa-github fa-fw"></i> Github</a></li>
                            <li><a href="http://linkedin/in/pozhilov" class="btn btn-link"><i class="fa fa-linkedin fa-fw"></i> LinkedIn</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </section>


        <!-- Load js libs only when the page is loaded. -->
        <?= $this->Html->script('jquery.min'); ?>
        <?= $this->Html->script('bootstrap.min.js') ?>
        <script src="/js/modernizr.custom.72241.js"></script>
        <!-- Custom template scripts -->
        <script src="/js/magister.js"></script>
    </body>
</html>