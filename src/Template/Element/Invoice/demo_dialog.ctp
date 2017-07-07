<!-- Modal -->
<div class="modal fade" id="demo-dialog" tabindex="-1" role="dialog" aria-labelledby="demo-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('CLOSE'); ?></span></button>
                <h4 class="modal-title" id="invoice-dialog-title"><?= __('DO_YOU_LOAD_DEMO_QUIZ') ?></h4>
            </div>
            <div class="modal-body">
                <?= __('CHOOSE_OK_LOAD_QUIZZES') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('CANCEL'); ?></button>
                <button type="button" class="btn btn-primary" id="import"><?php echo __('OK'); ?></button>
            </div>
        </div>
    </div>
</div>