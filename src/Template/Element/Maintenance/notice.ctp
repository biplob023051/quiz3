<?= $this->assign('title', $title_for_layout); ?>
<?= $this->Flash->render(); ?>
<div class="row">
	<div class="col-md-12">
		<?= h($setting['offline_message']); ?>
	</div>
</div>