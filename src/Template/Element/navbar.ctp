<?php
    $c_action = $this->request->action;
    $c_controller = $this->request->controller;
    $session = $this->request->session();
?>
<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <?php echo $this->Html->link('', '/', array('class' => 'navbar-brand')); ?>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav navbar-left">
                <?php if ($session->check('Auth.User.name')): ?>
                    <li <?php if ($c_controller == 'quiz' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('My Quizzes'), '/'); ?></li>
                <?php endif; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if ($session->check('Auth.User.name')): ?>
                    <!--nocache-->
                    <?php if ($session->read('Auth.User.account_level') == 51): ?>
                        <?php $admin_actions = array('admin_titles', 'admin_add', 'admin_insert', 'admin_index'); ?>
                        <li class="dropdown">
                            <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                <?php echo __('Admin Settings'); ?> 
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li <?php if ($c_controller == 'standards' && in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Classes'), array('controller' => 'standards', 'action' => 'index', 'admin' => true)); ?></li>
                                <li <?php if ($c_controller == 'subjects' && in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Subjects'), array('controller' => 'subjects', 'action' => 'index', 'admin' => true)); ?></li>
                                <li <?php if ($c_controller == 'helps' && in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Create Help'), array('controller' => 'helps', 'action' => 'titles', 'admin' => true)); ?></li>
                                <li <?php if ($c_action == 'admin_import') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Demo Quiz'), array('controller' => 'maintenance', 'action' => 'import', 'admin' => true)); ?></li>
                                <li <?php if (($c_controller == 'maintenance') && ($c_action == 'admin_settings')) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Maintenance'), array('controller' => 'maintenance', 'action' => 'settings', 'admin' => true)); ?></li>
                                <?php $video_actions = array('admin_videos', 'admin_create'); ?>
                                <li <?php if (in_array($c_action, $video_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Site Videos'), array('controller' => 'helps', 'action' => 'videos', 'admin' => true)); ?></li>
                                <li <?php if (in_array($c_action, $video_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Shared Quizzes'), array('controller' => 'quiz', 'action' => 'shared', 'admin' => true)); ?></li>
                            </ul>
                        </li>
                    <?php endif ?>
                    <li <?php if ($c_controller == 'helps' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Help'), '/helps'); ?></li>
                    <li>
                        <div class="user-image"></div>
                    </li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <?php echo h($session->read('Auth.User.name')); ?> 
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('Settings'), '/users/settings'); ?></li>
                        </ul>
                    </li>
                    <li><?php echo $this->Html->link(__('Logout'), '/users/logout'); ?></li>
                    <!--/nocache-->
                <?php else: ?>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>