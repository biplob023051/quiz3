<div class="modal fade" id="logout-warn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="logout-warn-header">
                <?php echo __('YOU_ARE_GOING_LOGOUT'); ?>
            </div>
            <div class="modal-body" id="logout-warn-body">
                <?php echo __('INACTIVE_FOR_15') . ' '; ?><span class="text-danger" id="s_timer"></span>
            </div>
            <div class="modal-footer" id="logout-warn-footer">
                <?php echo $this->Html->link(__('LOGOUT'),array('controller'=>'users', 'action'=>'logout'),array('role' => 'button', 'class' => 'btn btn-danger btn-ok'));?>
                <button type="button" id="stay-signin" class="btn btn-success"><?php echo __('KEEP_WORKING'); ?></button>
            </div>
        </div>
    </div>
</div>