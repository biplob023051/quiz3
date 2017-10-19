<h1>
	<?php  
	if ($data['request_type'] == 3) {
		echo __('INVOICE_FOR_ACCOUNT_UPGRADED_AND_CONTINUE_NEXT_YEAR');
	} else if ($data['request_type'] == 2) {
		echo __('INVOICE_FOR_ACCOUNT_DOWNGRADED_AND_CONTINUE_NEXT_YEAR');
	} else {
		echo __('INOVICE_FOR_CONTINUE_NEXT_YEAR');
	}
	?>
</h1>
<p><?= __('ID'); ?> <?= $data['id']; ?></p>
<p><?= __('USER_NAME'); ?> <?= $data['name']; ?></p>
<p><?= __('USER_EMAIL'); ?> <?= $data['email']; ?></p>
<p><?= __('USER_PACKAGE'); ?> <?= $data['package']; ?></p>
<p><?= __('CHARGED_AMOUNT'); ?> <?= round($data['amount_to_pay'], 2); ?></p>