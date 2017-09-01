<div class="modal fade" id="confirm-submit" tabindex="-1" role="dialog" aria-labelledby="confirm-submit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-top: 3px; padding-bottom: 3px;">
                <?php echo __('WANT_TURN_IN_QUIZ'); ?>
            </div>
            <div class="modal-body" style="padding-top: 3px; padding-bottom: 3px;">
                <?php echo __('ALL_QUESTIONS_ANSWERED'); ?>
            </div>
            <div class="modal-footer" style="padding-top: 3px; padding-bottom: 3px;">
                <span class="text-danger no-internet"><?php echo __('SORRY_LOST_CONNECTION'); ?></span>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('CANCEL'); ?></button>
                <button type="button" class="btn btn-success btn-ok" id="confirmed"><?php echo __('CONFIRM'); ?></button>
            </div>
        </div>
    </div>
</div>