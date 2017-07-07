<!-- Modal -->
<div class="modal fade" id="invoice-dialog" tabindex="-1" role="dialog" aria-labelledby="invoice-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('CLOSE'); ?></span></button>
                <h4 class="modal-title" id="invoice-dialog-title"><?= __('UPGRADE_ACCOUNT'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __('UPGRADE_ACCOUNT_WILL_GET_INVOICE'); ?></p>
                <br>
                <div class="row">
                    <div class="col-md-9">
                        <strong><?= __('BASIC'); ?></strong> <?= __('CREATE_AND_USE_QUIZZES_FREELY'); ?>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-yellow btn-sm" id="29_package"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span><?= __('29_EUR'); ?></span></button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-9">
                        <strong><?= __('QUIZ_BANK'); ?></strong> <?= __('SHARE_OWN_QUIZZES'); ?>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-yellow btn-sm" id="49_package"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span><?= __('49_EUR'); ?></span></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('CANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>