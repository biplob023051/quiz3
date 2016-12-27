<h1><?php echo __('Invoice'); ?></h1>
<p>User Id: <?php echo $data->id; ?></p>
<p>User Name: <?php echo $data->name; ?></p>
<p>User Email: <?php echo $data->email; ?></p>
<?php if (!empty($data->package)) : ?>
	<p>User Requested Package: <?php echo $data->package; ?></p>
<?php endif; ?>