<?php $this->assign('title', $title_for_layout); ?>
<div class="row">
	<div class="col-md-12">
		<p><?= __('PUR_PARA_1') ?></p>
		<p><?= __('PUR_PARA_2') ?></p>
		<p><?= __('BEFORE_CONTACT_TEXT') ?> <?= $this->Html->link(__('CONTACT_TEXT'), ['controller' => 'pages', 'action' => 'contact']); ?></p>
		<p><?= __('HAPPY_TEXT'); ?></p>
		<p><?= __('TEAM_TEXT'); ?></p>
		<p class="text-right"><?= $this->Html->link(__('CONTINUE_LINK'), '/'); ?></p>
	</div>
</div>