<!-- Modal -->
<div class="modal fade" id="invoice-dialog" tabindex="-1" role="dialog" aria-labelledby="invoice-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="invoice-dialog-title"><?php echo __('Upgrade Account') ?></h4>
            </div>
            <div class="modal-body">
                <?php echo __('An invoice will be sent to upgrade your account. Proceed?') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                <button type="button" class="btn btn-primary" id="send-invoice"><?php echo __('OK'); ?></button>
            </div>
        </div>
    </div>
</div>