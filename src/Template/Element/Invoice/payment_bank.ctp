<script type="text/javascript">
    var account_level = '<?php echo $authUser['account_level']; ?>';
</script>
<!-- Modal -->
<?php
    if (in_array($authUser['account_level'], [1,2]) && !in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) {
        $id_modifier = '_edit';
        $input_name = 'upgrade';
    } else if (!empty($authUser['customer_id']) && in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) {
        $id_modifier = '_activate';
        $input_name = 'activate';
    } else {
        $id_modifier = '';
        $input_name = 'package';
    }

?>
<div class="modal fade" id="invoice-payment" tabindex="-1" role="dialog" aria-labelledby="invoice-dialog-title" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('CLOSE'); ?></span></button>
                <h4 class="modal-title" id="invoice-dialog-title">
                    <?php if (!empty($authUser['customer_id']) && in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) : ?>
                        <?= __('REACTIVATE_SUBSCRIPTION'); ?>
                    <?php else : ?>
                        <?= in_array($authUser['account_level'], [1,2]) ? __('EDIT_PLAN') : __('PURCHASE_YEARLY_LICENCE_49'); ?>
                    <?php endif; ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (in_array($authUser['account_level'], [1,2]) && !empty($authUser['customer_id']) && !in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) : ?>
                    <div class="row m-b-10">
                        <div class="col-md-9">
                            <input name="<?= $input_name; ?>" id="cancel_package_input" value="Cancel" type="radio"  class="package-btn" />
                            <strong><?= __('CANCEL_SUBSCRIPTION'); ?>:</strong> <?= __('SUBSCRITION_CANCEL_TEXT'); ?>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                <?php elseif (in_array($authUser['account_level'], [1,2]) && !empty($authUser['customer_id']) && in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) : ?>
                    <?= __('CONTINUE_SUBSCRIPTION') ?>
                <?php endif; ?>
                <?php if (!in_array($authUser['account_level'], [1,2])) : ?>
                    <div id="payment-details">
                        <?php if (!in_array($authUser['account_level'], [1,2])) : ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <small id="tax-info"><?= __('INCL_TAX'); ?></small>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-12" id="card-portion">
                                <!-- If you're using Stripe for payments -->
                                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
                                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
                                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
                                <script src="https://js.stripe.com/v2/"></script>
                                <script src="https://js.stripe.com/v3/"></script>
                                <form role="form" id="payment-form" method="POST" action="javascript:void(0);">
                                    <div class="row">
                                        <div class="col-xs-12">
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
                                                        id="cardNumberId"
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
                                                    id="cardExpireId"
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
                                                    id="cardCvcId"
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
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <input type="hidden" name="amount" class="amount" value="49" />
                                            <button class="subscribe btn btn-success btn-lg btn-block subscribe-text" type="button"><?= __('49_YEARLY_BTN_TEXT'); ?></button>
                                        </div>
                                    </div>
                                    <div class="row" style="display:none;">
                                        <div class="col-xs-12">
                                            <p class="payment-errors"></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <?php if (in_array($authUser['account_level'], [1,2]) && !in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) : ?>
                    <button type="button" class="btn btn-success" disabled="disabled" id="confirm-upgrade"><?= __('CURRENT_49_PLAN'); ?></button>
                <?php elseif (!empty($authUser['customer_id']) && in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) : ?>
                    <button type="button" class="btn btn-success" id="confirm-reactivate"><?= $lang_strings['reactivate']; ?></button>
                <?php else : ?>
                <?php endif; ?>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('CANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
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
        width: 100% !important;
    }
    .ajax-file-upload-filename {
        width: 100% !important;
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
        font-weight: normal;
    }
</style>
<?php if (!in_array($authUser['account_level'], [1,2])) : ?>
    <script type="text/javascript">
        var paySuccess = '';
        var $form = $('#payment-form');
        $form.find('.subscribe').on('click', payWithStripe);

        /* If you're using Stripe for payments */
        function payWithStripe(e) {
            e.preventDefault();
            
            /* Abort if invalid form data */
            if (!validator.form()) {
                return;
            }

            /* Visual feedback */
            $form.find('.subscribe').html(lang_strings['validating'] + ' <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);

            Stripe.setPublishableKey(PublishableKey);
            
            /* Create token */
            var expiry = $('#cardExpireId').payment('cardExpiryVal');
            var ccData = {
                number: $('#cardNumberId').val().replace(/\s/g,''),
                cvc: $('#cardCvcId').val(),
                exp_month: expiry.month, 
                exp_year: expiry.year
            };
            
            Stripe.card.createToken(ccData, function stripeResponseHandler(status, response) {
                if (response.error) {
                    /* Visual feedback */
                    $form.find('.subscribe').html(lang_strings['retry']).prop('disabled', false);
                    /* Show Stripe errors on the form */
                    $form.find('.payment-errors').text(response.error.message);
                    $form.find('.payment-errors').closest('.row').show();
                } else {
                    /* Visual feedback */
                    $form.find('.subscribe').html(lang_strings['processing'] + ' <i class="fa fa-spinner fa-pulse"></i>');
                    /* Hide Stripe errors on the form */
                    $form.find('.payment-errors').closest('.row').hide();
                    $form.find('.payment-errors').text("");
                    // response contains id and card, which contains additional card details
                    var token = response.id;
                    // AJAX - you would send 'token' to your server here.
                    $.post(projectBaseUrl + 'users/payment', {
                            token: token,
                            amount: $('.amount').val()
                        })
                        // Assign handlers immediately after making the request,
                        .done(function(data, textStatus, jqXHR) {
                            var data = $.parseJSON(data);
                            if (data.success == true) {
                                // $form.find('.subscribe').html(data.message + ' <i class="fa fa-check"></i>');
                                // $('#invoice-payment').modal('hide');
                                // $('#pay-title').html(lang_strings['stripe_pay_scs_title']);
                                // $('#pay-body').html(lang_strings['stripe_pay_scs_body']);
                                // paySuccess = 1;
                                // $('#invoice-success-dialog').modal('show');
                                window.location = projectBaseUrl + 'users/paySuccess';
                            } else {
                                $form.find('.subscribe').html(data.message).removeClass('success').addClass('alert-danger');
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            $form.find('.subscribe').html(lang_strings['pay_failed']).removeClass('success').addClass('error');
                            /* Show Stripe errors on the form */
                            $form.find('.payment-errors').text(lang_strings['try_refresh']);
                            $form.find('.payment-errors').closest('.row').show();
                        });
                }
            });
        }
        $('#invoice-success-dialog').on('hidden.bs.modal', function () {
            if (typeof paySuccess !== 'undefined' && paySuccess == '1') {
                window.location = projectBaseUrl + 'users/paySuccess';
            } else {
                window.location.reload();
            }
        });
        /* Fancy restrictive input formatting via jQuery.payment library*/
        $('#cardNumberId').payment('formatCardNumber');
        $('#cardCvcId').payment('formatCardCVC');
        $('#cardExpireId').payment('formatCardExpiry');

        /* Form validation using Stripe client-side validation helpers */
        jQuery.validator.addMethod("cardNumber", function(value, element) {
            return this.optional(element) || Stripe.card.validateCardNumber(value);
        }, lang_strings['invalid_card']);

        jQuery.validator.addMethod("cardExpiry", function(value, element) {    
            /* Parsing month/year uses jQuery.payment library */
            value = $.payment.cardExpiryVal(value);
            return this.optional(element) || Stripe.card.validateExpiry(value.month, value.year);
        }, lang_strings['invalid_expire']);

        jQuery.validator.addMethod("cardCVC", function(value, element) {
            return this.optional(element) || Stripe.card.validateCVC(value);
        }, lang_strings['invalid_cvc']);

        validator = $form.validate({
            rules: {
                cardNumber: {
                    required: true,
                    cardNumber: true            
                },
                cardExpiry: {
                    required: true,
                    cardExpiry: true
                },
                cardCVC: {
                    required: true,
                    cardCVC: true
                }
            },
            highlight: function(element) {
                $(element).closest('.form-control').removeClass('success').addClass('error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-control').removeClass('error').addClass('success');
            },
            errorPlacement: function(error, element) {
                $(element).closest('.form-group').append(error);
            }
        });

        paymentFormReady = function() {
            if ($('#cardNumberId').hasClass("success") &&
                $('#cardExpireId').hasClass("success") &&
                $('#cardCvcId').val().length > 1) {
                return true;
            } else {
                return false;
            }
        }

        $form.find('.subscribe').prop('disabled', true);
        var readyInterval = setInterval(function() {
            if (paymentFormReady()) {
                $form.find('.subscribe').prop('disabled', false);
                clearInterval(readyInterval);
            }
        }, 250);
    </script>
<?php endif; ?>