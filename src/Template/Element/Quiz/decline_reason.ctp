<div class="modal fade" id="reason_<?php echo $quiz->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php echo __('DECLINE_REASON_TITLE'); ?>
            </div>
            <div class="modal-body">
                <?php echo !empty($quiz->comment) ? h($quiz->comment) : __('NO_REASON_BODY'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('CLOSE'); ?></button>
            </div>
        </div>
    </div>
</div>