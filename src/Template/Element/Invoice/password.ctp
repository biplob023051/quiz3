<!-- Modal -->
<div class="modal fade" id="change-password" tabindex="-1" role="dialog" aria-labelledby="invoice-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('CLOSE'); ?></span></button>
                <h4 class="modal-title" id="invoice-dialog-title"><?= __('CHANGE_PASSWORD'); ?></h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create($user, ['novalidate' => 'novalidate', 'id' => 'change-form']); ?>
                <div class="row">
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <?php
                            echo $this->Form->input('old_password', array(
                                'label' => __("CURRENT_PASSWORD"),
                                'placeholder' => __("FILL_CURRENT_PASSWORD"),
                                'required' => false,
                                'type' => 'password'
                            ));
                        ?>
                        <span class="text-danger" id="old-password-error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <?php
                            echo $this->Form->input('password1', array(
                                'label' => __("NEW_PASSWORD"),
                                'placeholder' => __("FILL_NEW_PASSWORD"),
                                'required' => false,
                                'type' => 'password',
                            ));
                        ?>
                        <span class="text-danger" id="password1-error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <?php
                            echo $this->Form->input('password2', array(
                                'label' => __("CONFIRM_PASSWORD"),
                                'placeholder' => __("FILL_CONFIRM_PASSWORD"),
                                'required' => false,
                                'type' => 'password'
                            ));
                        ?>
                        <span class="text-danger" id="password2-error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <?= $this->Form->submit(__("SAVE"), ['class' => 'btn btn-info2 btn-block', 'id' => 'submit-change']); ?>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('CANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>