<?php //echo $this->Session->flash('success'); ?>
<?php $this->assign('title', __('THANK_YOU_REGISTERING')); ?>
<div class="row">
	<div class="col-md-12">
		<?php 
			echo '<br>';
			echo __('CONFIRMATION_SENT') . '<br><br>';
			echo __('IF_NEED_HELP_CONTACT') . ' ';
			echo $this->Html->link(__('HERE'), array('controller' => 'pages', 'action' => 'display', 'contact')) . '.'; 
		?>
	</div>
</div>
<br>
<div class="row">
	<div class="col-md-12">
		<?= $this->Html->link(__('LOG_IN'), array('controller' => 'users', 'action' => 'login')); ?>
	</div>
</div>