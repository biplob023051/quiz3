<!-- Modal -->
<div class="modal fade" id="invoice-error-dialog" tabindex="-1" role="dialog" aria-labelledby="invoice-error-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('CLOSE'); ?></span></button>
                <h4 class="modal-title" id="invoice-error-dialog-title"><?= __('UPGRADE_ACCOUNT') ?></h4>
            </div>
            <div class="modal-body">
                <?= __('INVOICE_FAILED') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __('CLOSE') ?></button>
            </div>
        </div>
    </div>
</div>