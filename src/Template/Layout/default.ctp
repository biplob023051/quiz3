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
use Cake\Routing\Router;
$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" value="notranslate">
    <?= $this->Html->meta('favicon.ico', '/img/favicon.ico', array('type' => 'icon')); ?>
    <?= $this->fetch('meta'); ?>

    <title><?= $this->fetch('title'); ?></title>

    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css' />
    <?= $this->Html->css(array(
            /* production */
            //'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css',
            'bootstrap.min',
            'style',
        ));
    ?>
    <?= $this->Html->script(array(
        /* production */
        //'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
        //'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js'
        'jquery.min.js',
        'bootstrap.min.js',
    ));
    ?>
    <?= $this->fetch('css'); ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body <?php if ($this->request->action == 'login') : ?>style="padding-top:50px;" class="bg-cover"<?php else : ?>style="background:#ffffff; padding-top:50px;"<?php endif; ?>>
    <?php 
        if (!empty($setting['visible']) && empty($setting['offline_status'])) {
            echo $this->element('Maintenance/alert');
        }
    ?>
    <?= $this->Flash->render() ?>
    <?php if ($this->request->controller != 'Pages') : ?>
        <div class="container" id="maintenance-alert">
            <?php if ($authUser): ?>
                <?= $this->element('navbar');?>
            <?php else : ?>
                <?= $this->element('page-navbar');?>
            <?php endif; ?>
            <div class="page-header">
                <h1><?= $this->fetch('title'); ?></h1>
            </div>
            <?= $this->fetch('content'); ?>
        </div>
        <!-- /container -->
    <?php else : ?>
        <?= $this->element('page-navbar');?>
        <?= $this->fetch('content'); ?>
    <?php endif; ?>
    <div id="footer">
        <div class="container"></div>
    </div>
    <?php 
    if ($authUser): // Add these modal if logged in user
        echo $this->element('logout-warning'); 
    endif;
    ?>
    <?php
    echo $this->Html->scriptBlock('
        var projectBaseUrl = "'.Router::url('/', true).'";
        ', array('inline' => false)
    );
    if ($authUser): // Add these script if logged in user
        echo $this->Html->script(array(
            'jquery.countdownTimer.min',
            'user-idle.js',
        ));
    endif;
    ?>
    <?= $this->fetch('script'); ?>
    <?= $this->element('google-analytics'); ?>
</body>
</html>
