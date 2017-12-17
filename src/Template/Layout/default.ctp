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
use Cake\Core\Configure;
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

    <title><?= (isset($title_for_layout)) ? $title_for_layout : $this->fetch('title'); ?></title>

    <!-- Google Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css' />
    <?= $this->Html->css([
            /* production */
            'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css',
            /* for local */
            //'bootstrap.min',
            'style'.$minify,
        ]);
    ?>
    <?php
        echo $this->Html->scriptBlock('
            var projectBaseUrl = "'.Router::url('/', true).'";
            ', array('inline' => false)
        );

        if (Configure::read('debug')) {
            echo $this->Html->scriptBlock('
                var PublishableKey = "'.TEST_PUBLIC_KEY.'";
                ', array('inline' => false)
            );
        } else {
            echo $this->Html->scriptBlock('
                var PublishableKey = "'.LIVE_PUBLIC_KEY.'";
                ', array('inline' => false)
            );
        }
    ?>
    <?= $this->Html->script([
        /* production */
        'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.7/bootstrap-notify.min.js',
        /* For local */
        // 'jquery.min.js',
        // 'bootstrap.min.js',
        // 'bootstrap-notify.min',
    ]);
    ?>
    <?= $this->fetch('css'); ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
        $(document).on('click', '.my-language', function(e) {
            e.preventDefault();
            $.post(projectBaseUrl + 'users/switchLanguage',
            {
                lang: $(this).attr('data-value')
            },
            function(data, status){
                window.location.reload();
            });
        });
    </script>
</head>
<body <?php if ($this->request->action == 'login') : ?>style="padding-top:50px;" class="bg-cover"<?php else : ?>style="background:#ffffff; padding-top:50px;"<?php endif; ?>>
    <?php 
        if (!empty($setting['visible']) && empty($setting['offline_status'])) {
            echo $this->element('Maintenance/alert');
            echo $this->Html->scriptBlock('
                var maintHeight = "70";
                ', array('inline' => false)
            );
        } else {
            echo $this->Html->scriptBlock('
                var maintHeight = "0";
                ', array('inline' => false)
            );
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
        <div class="container <?php echo empty($eng_domain) ? 'fin-logo-footer' : 'eng-logo-footer'; ?>"></div>
    </div>
    <?php 
    if ($authUser): // Add these modal if logged in user
        echo $this->element('logout-warning'); 
    endif;
    ?>
    <script type="text/javascript">
        var lang_seconds = '<?= __('SECONDS'); ?>';
    </script>
    <?php
    if ($authUser): // Add these script if logged in user
        echo $this->Html->script(['user-idle' . $minify]);
    endif;
    ?>
    <?= $this->fetch('script'); ?>
    <?= $this->element('google-analytics'); ?>

    <script type="text/javascript">
        $(document).ajaxSend(function(e, xhr, settings) {
            xhr.setRequestHeader('X-CSRF-Token', '<?= $this->request->params['_csrfToken'] ?>');
        });
    </script>
</body>
</html>
