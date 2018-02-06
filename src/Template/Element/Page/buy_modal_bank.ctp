<div class="modal fade" id="buy-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modal-title"><?= __('CREATE_ACCOUNT_BUY_49'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger" id="error-message" style="display: none;"></div>  
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="email-exist" style="display: none;" class="row m-b-10">
                        <div class="col-sm-4 col-xs-12"><?= __('ALREADY_REGISTERED'); ?></div>
                        <div class="col-md-4 col-xs-12">
                            <?= $this->Html->link(__('LOG_IN_BUY'), '/users/login'); ?>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <?= $this->Html->link(__('RECOVER_PASSWORD'), '/users/password_recover'); ?>
                        </div>
                    </div>  
                </div>
            </div>
            <?php
                echo $this->Form->create('', [
                    'horizontal' => true,
                    'id' => 'UserCreateForm',
                    'columns' => [ 
                        'sm' => [
                            'label' => 3,
                            'input' => 9,
                            'error' => 9
                        ],
                        'md' => [
                            'label' => 3,
                            'input' => 9,
                            'error' => 9
                        ]
                    ],
                    'novalidate' => 'novalidate',
                    'url' => ['controller'=>'users', 'action'=>'buy_create']
                ]);
            ?>
            <div class="form-group text">
                <label class="control-label col-sm-3 col-md-3" for="name"><?= __('NAME'); ?></label>
                <div class="col-sm-9 col-md-9">
                    <input type="text" name="name" class="form-control" placeholder="<?= __('ENTER_YOUR_NAME'); ?>" id="name">
                    <label id="name-error" class="error" for="name" style="display: none;"></label>
                </div>
            </div>
            <div class="form-group email">
                <label class="control-label col-sm-3 col-md-3" for="email"><?= __('EMAIL'); ?></label>
                <div class="col-sm-9 col-md-9">
                    <input type="text" name="email" class="form-control" placeholder="<?= __('ENTER_VALID_EMAIL'); ?>" id="email">
                    <label id="email-error" class="error" for="email" style="display: none;"></label>
                </div>
            </div>
            <div class="form-group password">
                <label class="control-label col-sm-3 col-md-3" for="password"><?= __('PASSWORD'); ?></label>
                <div class="col-sm-9 col-md-9">
                    <input type="password" name="password" class="form-control" placeholder="<?= __('ENTER_PASSWORD'); ?>" id="password">
                    <label id="password-error" class="error" for="password" style="display: none;"></label>
                </div>
            </div>
            <div class="form-group password">
                <label class="control-label col-sm-3 col-md-3" for="passwordverify"><?= __('PASSWORD_VERIFY'); ?></label>
                <div class="col-sm-9 col-md-9">
                    <input type="password" name="passwordVerify" class="form-control" placeholder="<?= __('ENTER_PASSWORD_AGAIN'); ?>" id="passwordverify">
                    <label id="passwordverify-error" class="error" for="passwordverify" style="display: none;"></label>
                </div>
            </div>
    
            <div id="payment-details">
                <div class="row">
                    <div class="col-md-12">
                        <small><?= __('TOTAL_PRICE_49'); ?></small>
                    </div>
                    <div class="col-md-12">
                        <small id="tax-info"><?= __('INCL_TAX'); ?></small>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12" id="card-portion">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label for="cardNumber"><?= __('CARD_NUMBER'); ?></label>
                                    <div class="input-group">
                                        <input 
                                            type="tel"
                                            class="form-control"
                                            name="cardNumber"
                                            placeholder="<?= __('VALID_CARD_REQUIRED'); ?>"
                                            autocomplete="cc-number"
                                            required autofocus 
                                        />
                                        <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7 col-md-7">
                                <div class="form-group">
                                    <label for="cardExpiry"><span class="hidden-xs"><?= __('EXPIRATION'); ?></span><span class="visible-xs-inline"><?= __('EXP'); ?></span> <?= __('DATE'); ?></label>
                                    <input 
                                        type="tel" 
                                        class="form-control" 
                                        name="cardExpiry"
                                        placeholder="<?= __('MM-YY'); ?>"
                                        autocomplete="cc-exp"
                                        required 
                                    />
                                </div>
                            </div>
                            <div class="col-xs-5 col-md-5 pull-right">
                                <div class="form-group">
                                    <label for="cardCVC"><?= __('CVC'); ?></label>
                                    <input 
                                        type="tel" 
                                        class="form-control"
                                        name="cardCVC"
                                        placeholder="<?= __('CVC_GUIDE'); ?>"
                                        autocomplete="cc-csc"
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-12 m-b-5"><strong><?= __('PURCHASE_BENEFIT'); ?></strong></div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <ul class="list-unstyled">
                                        <li><?= __('AUTO_CHARGE_PER_YEAR'); ?></li>
                                        <li><?= __('CANCEL_ANYTIME_FROM_SETTINGS'); ?></li>
                                        <li><?= __('NO_PAYMENT_AFTER_CANCEL'); ?></li>
                                        <li><?= __('AFTER_CANCEL_STILL_BENEFIT_TILL_END'); ?></li>
                                        <li><?= __('REOPEN_ACCOUNT_ANYTIME'); ?></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xs-12 m-b-20"><?= __('IMMEDIATE_ACCESS_AFTER_PURCHASE'); ?></div>
                        </div>
                        <div class="row" style="display:none;">
                            <div class="col-xs-12">
                                <p class="payment-errors"></p>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('CANCEL'); ?></button>
            <button class="btn btn-success btn-ok" disabled="disabled" id="create-user"><?= __('BUY'); ?></button>
        </div>
        <?= $this->Form->end(); ?>
    </div>
    </div>
</div>

<style>
    .form-horizontal .form-group {
        margin-right: 0px;
        margin-left: 0px; 
    }

    /* CSS for Credit Card Payment form */
    .credit-card-box .panel-title {
        display: inline;
        font-weight: bold;
    }
    .credit-card-box .form-control.error {
        border-color: red;
        outline: 0;
        box-shadow: inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(255,0,0,0.6);
    }
    .credit-card-box label.error {
      font-weight: bold;
      color: red;
      padding: 2px 8px;
      margin-top: 2px;
    }
    .credit-card-box .payment-errors {
      font-weight: bold;
      color: red;
      padding: 2px 8px;
      margin-top: 2px;
    }
    .credit-card-box label {
        display: block;
    }
    /* The old "center div vertically" hack */
    .credit-card-box .display-table {
        display: table;
    }
    .credit-card-box .display-tr {
        display: table-row;
    }
    .credit-card-box .display-td {
        display: table-cell;
        vertical-align: middle;
        width: 50%;
    }
    .ajax-file-upload-progress, .ajax-file-upload-progress, .ajax-file-upload-red {
        display: none !important;
    }
    .ajax-file-upload-statusbar {
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .package-btn {
        height: 1.1em;
        width: 3%;
    }
    .btn-40 {
        padding: 7px 14px;
    }
    .error {
        color: #a94442 !important;
    }
    .form-horizontal label {
        text-transform: none !important;
    }
    .bold-text {
        font-size: 14px !important;
        color: #333 !important;
    }
</style>