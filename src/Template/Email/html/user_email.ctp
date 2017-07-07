<?php 
	use Cake\Routing\Router;
?>
<?php $code = $data->id . 'y-s' . $data->activation; ?>
<p><?php echo __('Hi') . ' ' . $data->name . ','; ?></p>
<p><?php echo __('CLICK_LINK_ACTIVATE'); ?></p>
<p><a href="<?php echo Router::url(array('controller'=>'users', 'action' => 'confirmation', $code),true); ?>"><?php echo Router::url(array('controller'=>'users', 'action' => 'confirmation', $code),true); ?></a></p>
<small><?php echo __('IF_NOT_REGISTER_IGNORE'); ?></small>