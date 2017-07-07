<?php
    $c_action = $this->request->action;
    $c_controller = strtolower($this->request->controller);
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <?php if (($c_controller != 'quiz' && $c_action != 'live') && ($c_controller != 'student' && $c_action != 'success')) : ?>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
                    <span class="sr-only"><?php echo __('NAVIGATION'); ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php echo $this->Html->link('', '/', array('class' => 'navbar-brand')); ?>
            </div>
            <div class="collapse navbar-collapse" id="main-nav">
                <ul class="nav navbar-nav navbar-left">
                    <li <?php if ($c_controller == 'pages' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('OVERVIEW'), '/'); ?></li>
                    <li <?php if (isset($current_page) && ($current_page == 'prices')) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('PRICES'), array('controller' => 'pages', 'action' => 'prices')); ?></li>
                    <li <?php if (isset($current_page) && ($current_page == 'contact')) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('CONTACT'), array('controller' => 'pages', 'action' => 'contact')); ?></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <?php echo ($language != 'en_GB') ? $this->Quiz->getLang($language) : 'International'; ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)" class="my-language" data-value="fi"><i class="glyphicon finish"></i>&nbsp;&nbsp;&nbsp;&nbsp; Suomi</a></li>
                            <li><a href="javascript:void(0)" class="my-language" data-value="en_GB">International</a></li>
                            <li><a href="javascript:void(0)" class="my-language" data-value="sv_FI"><i class="glyphicon spanish"></i> Svenska (FI)</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo $this->request->base; ?>/users/create"><?php echo __('NOT_ACCOUNT') . ' '; ?><span class="text-primary"><?php echo __('REGISTER_NOW'); ?></span></a></li>
                    <li><a href="<?php echo $this->request->base; ?>/users/login" style="padding-top:8px; padding-bottom:0" ><button type="button" class="btn btn-success"><?php echo __('LOGGIN'); ?></button></a></li>
                </ul>
            </div>
        <?php else : ?>
            <div class="navbar-header">
                <?php echo $this->Html->link('', '/', array('class' => 'navbar-brand')); ?>
            </div>
        <?php endif; ?>
    </div>
</nav>