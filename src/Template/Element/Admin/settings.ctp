<div class="form-group">
	<label><?= __('WILDCARD_REPLACE_GUIDE'); ?></label>
	<?php echo $this->Form->text('alert_message', array('class' => 'form-control', 'value' => $site_settings[$lang]['alert_message'])); ?>
</div>
<div class="form-group">
	<label><?= __('MAITENANCE_DATE_TIME'); ?></label>
	<?php echo $this->Form->text('maintenance_time', array('class' => 'form-control datepicker', 'value' => $site_settings[$lang]['maintenance_time'], 'id' => 'maintenance_time')); ?>
</div>
<div class="form-group">
	<label><?= __('ALERT_VISIBLE'); ?></label>
	<?php
		if ($site_settings[$lang]['visible']) {
			echo $this->Form->text('visible', array('type' => 'checkbox', 'checked' => true, 'class' => '')); 
		}
		else {
			echo $this->Form->text('visible', array('type' => 'checkbox', 'class' => ''));
		}
	?>
</div>
<h4><?php echo __('OFFLINE_SECTION'); ?></h4>
<hr>
<div class="form-group">
	<label><?= __('OFFLINE_MSG'); ?></label>
	<?php echo $this->Form->text('offline_message', array('class' => 'form-control', 'value' => $site_settings[$lang]['offline_message'])); ?>
</div>
<div class="form-group">
	<label><?= __('MAKE_OFFLINE'); ?></label>
	<?php
		if ($site_settings[$lang]['offline_status']) {
			echo $this->Form->text('offline_status', array('type' => 'checkbox', 'checked' => true, 'class' => '')); 
		}
		else {
			echo $this->Form->text('offline_status', array('type' => 'checkbox', 'class' => ''));
		}
	?>
</div>