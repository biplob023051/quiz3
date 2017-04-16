<?php 
	use Cake\Routing\Router;
?>
<?php $code = $data->id . 'y-s' . $data->activation; ?>
<p><?php echo __('Hi') . ' ' . $data->name . ','; ?></p>
<p><?php echo __('Thanks for registering with us. To activate your account, please click the link below or simply copy paste the url to your browser.'); ?></p>
<p><a href="<?php echo Router::url(array('controller'=>'users', 'action' => 'confirmation', $code),true); ?>"><?php echo Router::url(array('controller'=>'users', 'action' => 'confirmation', $code),true); ?></a></p>
<small><?php echo __('If you didn\'t intend to register, simply ignore this email.'); ?></small>