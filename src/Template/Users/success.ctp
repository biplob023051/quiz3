<?php //echo $this->Session->flash('success'); ?>
<?php $this->assign('title', __('Login')); ?>
<div class="row">
	<div class="col-md-12">
		<?php 
			echo __('Thank you for creating a user account!') . '<br>';
			echo __('We\'ve sent a confirmation email to your given email address. Check your email and click the link in it to activate your account.') . '<br>';
			echo __('If you need help, take contact') . ' ';
			echo $this->Html->link(__('here'), array('controller' => 'pages', 'action' => 'display', 'contact')) . '.'; 
		?>
	</div>
</div>
<br>
<div class="row">
	<div class="col-md-12">
		<?php 
			echo $this->Html->link(__('Log in'), array('controller' => 'users', 'action' => 'login')); 
		?>
	</div>
</div>