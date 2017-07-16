<div class="modal fade" id="change-role" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?= __('YOU_ARE_GOING_CHANGE_USER_LEVEL'); ?>
            </div>
            <div class="modal-body">
                <?= __('ACCOUNT_LEVEL_CHANGE_TEXT') . ' '; ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirm-role" class="btn btn-success"><?php echo __('CONFIRM'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('CANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>