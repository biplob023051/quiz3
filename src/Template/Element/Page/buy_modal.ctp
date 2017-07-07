<div class="modal fade" id="buy-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?= __('CREATE_ACCOUNT_BUY'); ?>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="error-message" style="display: none;"></div>
                <div id="email-exist" style="display: none;">
                    <div class="col-sm-4 col-xs-12"><?= __('ALREADY_REGISTERED'); ?></div>
                    <div class="col-md-4 col-xs-12">
                        <?= $this->Html->link(__('LOG_IN_BUY'), '/users/login'); ?>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <?= $this->Html->link(__('RECOVER_PASSWORD'), '/users/password_recover'); ?>
                    </div>
                </div>
                <br />
                <?php
                    echo $this->Form->create('', [
                        'horizontal' => true,
                        'id' => 'UserCreateForm',
                        'columns' => [ 
                            'sm' => [
                                'label' => 4,
                                'input' => 7,
                                'error' => 7
                            ],
                            'md' => [
                                'label' => 4,
                                'input' => 7,
                                'error' => 7
                            ]
                        ],
                        'novalidate' => 'novalidate',
                        'url' => ['controller'=>'users', 'action'=>'buy_create']
                    ]);

                    echo $this->Form->input('name', array(
                        'placeholder' => __('ENTER_YOUR_NAME')
                    ));

                    echo $this->Form->input('email', array(
                        'placeholder' => __('ENTER_VALID_EMAIL')
                    ));

                    echo $this->Form->input('password', array(
                        'type' => 'password',
                        'placeholder' => __('ENTER_PASSWORD'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'bottom',
                        'data-original-title' => __('PASSWORD_MUST_BE_LONGER')
                    ));

                    echo $this->Form->input('passwordVerify', array(
                        'type' => 'password',
                        'placeholder' => __('PASSWORD_VERIFY')
                    ));

                    echo $this->Form->hidden('package', ['id' => 'package']);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('CANCEL'); ?></button>
                <input class="btn btn-success btn-ok" type="submit" id="create-user" value="<?= __('SUBMIT'); ?>">
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>