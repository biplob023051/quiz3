<?php
    //echo $this->Html->script(array('moment', 'bootstrap-datetimepicker.min', 'settings'), array('inline' => false));
    //echo $this->Html->css(array('bootstrap-datetimepicker.min'), array('inline' => false));
    $this->assign('title', $title_for_layout);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body">
    	<?= $this->Flash->render(); ?>
        <?= $this->Form->create('', ['url' => '/admin/maintenance/settings', 'id' => 'settings']); ?>
        		<h3><?= __('Alert Section'); ?></h3>
	    		<hr>
				<ul class="nav nav-tabs" id="allTabs">
					<li class="active"><a data-toggle="tab" href="#fin-settings"><?= __('FIN_SETTINGS'); ?></a></li>
					<li><a data-toggle="tab" href="#eng-settings"><?= __('ENG_SETTINGS'); ?></a></li>
					<li><a data-toggle="tab" href="#sv-settings"><?= __('SWEDISH_SETTINGS'); ?></a></li>
				</ul>
				<div class="tab-content" style="margin-top: 10px;">
					<div id="fin-settings" class="tab-pane fade in active">
						<?= $this->element('Admin/settings', ['lang' => 'fi']); ?>
					</div>
					<div id="eng-settings" class="tab-pane fade">
						<?= $this->element('Admin/settings', ['lang' => 'en_GB']); ?>
					</div>
					<div id="sv-settings" class="tab-pane fade">
						<?= $this->element('Admin/settings', ['lang' => 'sv_FI']); ?>
					</div>
				</div>
			
			<div class="regSubmit">
				<input type="submit" value="<?= __('Save settings'); ?>" class="btn btn-primary btn-xlarge">
			</div>
		<?= $this->Form->end(); ?>
    </div>
</div>

<script>
	$(document).on('submit', '#settings', function(e){
		var activeTabId = $("ul#allTabs li.active").find('a').attr('href');
		if (activeTabId == '#fin-settings') {
			$('#eng-settings').remove();
			$('#sv-settings').remove();
			$(this).append('<input type="hidden" name="language" value="fi">');
		} else if (activeTabId == '#eng-settings') {
			$('#fin-settings').remove();
			$('#sv-settings').remove();
			$(this).append('<input type="hidden" name="language" value="en_GB">');
		} else if (activeTabId == '#sv-settings') {
			$('#fin-settings').remove();
			$('#eng-settings').remove();
			$(this).append('<input type="hidden" name="language" value="sv_FI">');
		}
	});
</script>