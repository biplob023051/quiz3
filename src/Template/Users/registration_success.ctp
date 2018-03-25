<?php $this->assign('title', $title_for_layout); ?>
<div class="row">
	<div class="col-md-12">
		<p><?= __('REG_PARA_1') ?></p>
		<p><?= __('REG_PARA_2') ?></p>
		<p>
			<h4><?= __('REG_STEP_TITLE'); ?></h4>
			<ul>
				<li class="list-group-item"><?= __('STEP_1'); ?></li>
				<li class="list-group-item"><?= __('STEP_2'); ?></li>
				<li class="list-group-item"><?= __('STEP_3'); ?></li>
			</ul>
		</p>
		<p><?= __('BEFORE_CONTACT_TEXT') ?> <?= $this->Html->link(__('CONTACT_TEXT'), ['controller' => 'pages', 'action' => 'contact']); ?></p>
		<p><?= __('HAPPY_TEXT'); ?></p>
		<p><?= __('TEAM_TEXT'); ?></p>
		<p class="text-right"><?= $this->Html->link(__('CONTINUE_LINK'), '/'); ?></p>
	</div>
</div>