<h1><?php echo __('INVOICE'); ?></h1>
<p><?= __('ID'); ?> <?php echo $data['id']; ?></p>
<p><?= __('User Name:'); ?> <?php echo $data['name']; ?></p>
<p><?= __('User Email:'); ?> <?php echo $data['email']; ?></p>
<?php if (!empty($data['package'])) : ?>
	<p><?= __('User Requested Package:'); ?> <?php echo $data['package']; ?></p>
<?php endif; ?>
<?php if (!empty($data['invoice_info'])) : ?>
	<p><?= __('User Invoice Info:'); ?> <?php echo $data['invoice_info']; ?></p>
<?php endif; ?>