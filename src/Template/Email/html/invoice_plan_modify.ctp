<h1>
	<?php  
	if ($data['request_type'] == 2) {
		echo __('INVOICE_FOR_ACCOUNT_UPGRADE');
	} else {
		echo __('ACCOUNT_DOWNGRADE_NOTIFICATION');
	}
	?>
</h1>
<p><?= __('ID'); ?> <?= $data['id']; ?></p>
<p><?= __('USER_NAME'); ?> <?= $data['name']; ?></p>
<p><?= __('USER_EMAIL'); ?> <?= $data['email']; ?></p>
<?php if (!empty($data['amount_to_pay'])) : ?>
<p><?= __('CHARGED_AMOUNT'); ?> <?= round($data['amount_to_pay'], 2) . ' Euro'; ?></p>
<?php endif; ?>