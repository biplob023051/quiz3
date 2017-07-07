<?php 
	use Cake\Routing\Router;
?>
<h1><?php echo __('PASSWORD_RESET'); ?></h1>
<p><?php echo __('RESET_PASSWORD_CLICK'); ?></p>
<a href="<?php echo Router::url(array('controller'=>'users', 'action' => 'reset_password', $data->reset_code),true); ?>"><?php echo Router::url(array('controller'=>'users', 'action' => 'reset_password', $data->reset_code),true); ?></a>
<p><?php echo __('IF_NOT_SENT_IGNORE'); ?></p>