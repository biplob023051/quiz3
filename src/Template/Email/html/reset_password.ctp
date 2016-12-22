<?php 
	use Cake\Routing\Router;
?>
<h1><?php echo __('Password Reset'); ?></h1>
<p><?php echo __('To reset your password click on the bellow link or paste on your browser.'); ?></p>
<a href="<?php echo Router::url(array('controller'=>'users', 'action' => 'reset_password', $data->reset_code),true); ?>"><?php echo Router::url(array('controller'=>'users', 'action' => 'reset_password', $data->reset_code),true); ?></a>
<p><?php echo __('If you have not sent this request, just ignore this mail.'); ?></p>