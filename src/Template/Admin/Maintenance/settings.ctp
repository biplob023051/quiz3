<?php
    echo $this->Html->script(array('moment', 'bootstrap-datetimepicker.min', 'settings'), array('inline' => false));
    echo $this->Html->css(array('bootstrap-datetimepicker.min'), array('inline' => false));
    $this->assign('title', $title_for_layout);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body">
    	<?= $this->Flash->render(); ?>
        <?= $this->Form->create('', ['url' => '/admin/maintenance/settings', 'id' => 'settings']); ?>
	        <h4><?= __('Alert Section'); ?></h4>
	    	<hr>
			<div class="form-group">
				<label><?= __('Offline Alert Message - {datetime} wildcard will be replaced by actual Maintenance date time and {time} will be replace by time left of maintenace'); ?></label>
				<?php echo $this->Form->text('alert_message', array('class' => 'form-control', 'value' => $setting['alert_message'])); ?>
			</div>
			<div class="form-group">
				<label><?= __('Maintenance Date Time'); ?></label>
				<?php echo $this->Form->text('maintenance_time', array('class' => 'form-control datepicker', 'value' => $setting['maintenance_time'], 'id' => 'maintenance_time')); ?>
			</div>
			<div class="form-group">
				<label><?= __('Make It Visible'); ?></label>
				<?php
					if ($setting['visible']) {
						echo $this->Form->text('visible', array('type' => 'checkbox', 'checked' => true, 'class' => '')); 
					}
					else {
						echo $this->Form->text('visible', array('type' => 'checkbox', 'class' => ''));
					}
				?>
			</div>
			<h4><?php echo __('Offline Section'); ?></h4>
	    	<hr>
			<div class="form-group">
				<label><?= __('Offline Message'); ?></label>
				<?php echo $this->Form->text('offline_message', array('class' => 'form-control', 'value' => $setting['offline_message'])); ?>
			</div>
			<div class="form-group">
				<label><?= __('Make Offline'); ?></label>
				<?php
					if ($setting['offline_status']) {
						echo $this->Form->text('offline_status', array('type' => 'checkbox', 'checked' => true, 'class' => '')); 
					}
					else {
						echo $this->Form->text('offline_status', array('type' => 'checkbox', 'class' => ''));
					}
				?>
			</div>
			<div class="regSubmit">
				<input type="submit" value="<?= __('Save settings'); ?>" class="btn btn-primary btn-xlarge">
			</div>
		<?= $this->Form->end(); ?>
    </div>
</div>