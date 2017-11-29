<?php
    $c_action = $this->request->action;
    $c_controller = $this->request->controller;
    $session = $this->request->session();
?>
<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <?php echo empty($eng_domain) ? $this->Html->link('', '/', array('class' => 'navbar-brand fin-logo')) : $this->Html->link('', '/', array('class' => 'navbar-brand eng-logo')); ?>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav navbar-left">
                <?php if ($session->check('Auth.User.name')): ?>
                    <li <?php if ($c_controller == 'quizzes' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('MY_QUIZZES'), '/'); ?></li>
                <?php endif; ?>
                <?php if (!empty($authUser['quiz_bank_access'])) : ?>
                    <li <?php if ($c_controller == 'quizzes' && $c_action == 'bank') : ?>class="active"<?php endif; ?>><?= $this->Html->link(__('QUIZ_BANK'), ['controller' => 'quizzes', 'action' => 'bank', 'prefix' => false], ['escape' => false]); ?></li>
                <?php endif; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if ($authUser): ?>
                    <!--nocache-->
                    <?php if ($authUser['account_level'] == 51): ?>
                        <?php $admin_actions = array('admin_titles', 'admin_add', 'admin_insert', 'admin_index'); ?>
                        <li class="dropdown">
                            <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                <?php echo __('ADMIN_SETTINGS'); ?> 
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li <?php if ($c_controller == 'users' && in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('USERS'), array('controller' => 'users', 'action' => 'index', 'prefix' => 'admin')); ?></li>
                                <li <?php if ($c_controller == 'standards' && in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('CLASSES'), array('controller' => 'standards', 'action' => 'index', 'prefix' => 'admin')); ?></li>
                                <li <?php if ($c_controller == 'subjects' && in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('SUBJECTS'), array('controller' => 'subjects', 'action' => 'index', 'prefix' => 'admin')); ?></li>
                                <li <?php if ($c_controller == 'helps' && in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('CREATE_HELP'), array('controller' => 'helps', 'action' => 'titles', 'prefix' => 'admin')); ?></li>
                                <li <?php if ($c_action == 'admin_import') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('IMPORT_DEMO_QUIZZES'), array('controller' => 'maintenance', 'action' => 'import', 'prefix' => 'admin')); ?></li>
                                <li <?php if (($c_controller == 'maintenance') && ($c_action == 'admin_settings')) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('MAINTENANCE'), array('controller' => 'maintenance', 'action' => 'settings', 'prefix' => 'admin')); ?></li>
                                <?php $video_actions = array('admin_videos', 'admin_create'); ?>
                                <li <?php if (in_array($c_action, $video_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('SITE_VIDEOS'), array('controller' => 'helps', 'action' => 'videos', 'prefix' => 'admin')); ?></li>
                                <li <?php if (in_array($c_action, $video_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('SHARED_QUIZZES'), array('controller' => 'Quizzes', 'action' => 'shared', 'prefix' => 'admin')); ?></li>
                            </ul>
                        </li>
                    <?php endif ?>
                    <li <?php if ($c_controller == 'helps' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('HELP'), '/helps'); ?></li>
                    <!-- <li>
                        <div class="user-image"></div>
                    </li> -->
                    <li><?= $this->Html->link(h($authUser['name']), array('controller' => 'users', 'action' => 'settings', 'prefix' => false)); ?></li>
                    <li><?php echo $this->Html->link(__('LOGOUT'), '/users/logout'); ?></li>
                    <!--/nocache-->
                <?php else: ?>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>