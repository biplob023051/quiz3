<script type="text/javascript">
    var account_level = '<?php echo $authUser['account_level']; ?>';
</script>
<!-- Modal -->
<?php if (!in_array($authUser['account_level'], [1,2])) : ?>
    <?= $this->Html->script(['jquery.uploadfile.min'], ['inline' => false]); ?>
    <?= $this->Html->css(['uploadfile'], ['inline' => false]); ?>
<?php endif; ?>
<?php
    if (in_array($authUser['account_level'], [1,2])) {
        $id_modifier = '_edit';
        $input_name = 'upgrade';
    } else if (in_array($authUser['plan_switched'], ['CANCEL_DOWNGRADE', 'CANCEL_UPGRADE'])) {
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
                    <?php if (in_array($authUser['plan_switched'], ['CANCEL_DOWNGRADE', 'CANCEL_UPGRADE'])) : ?>
                        <?= __('REACTIVATE_SUBSCRIPTION'); ?>
                    <?php else : ?>
                        <?= in_array($authUser['account_level'], [1,2]) ? __('EDIT_PLAN') : __('UPGRADE_ACCOUNT'); ?>
                    <?php endif; ?>
                </h4>
            </div>
            <div class="modal-body">
                <p><?= __('UPGRADE_ACCOUNT_WILL_GET_INVOICE'); ?></p>
                <div class="row m-b-10">
                    <div class="col-md-9">
                        <input name="<?= $input_name; ?>" id="29_package_input<?php echo $id_modifier; ?>" value="1"<?php if ($authUser['account_level'] == 1) echo ' checked'; ?> type="radio" />
                        <strong><?= __('BASIC'); ?></strong> <?= __('CREATE_AND_USE_QUIZZES_FREELY'); ?>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn <?php echo ($authUser['account_level'] == 1) ? 'btn-green' : 'btn-yellow'; ?> btn-sm" id="29_package<?php echo $id_modifier; ?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span><?= __('29_EUR'); ?></span></button>
                    </div>
                </div>
                <div class="row m-b-10">
                    <div class="col-md-9">
                        <input name="<?= $input_name; ?>" id="49_package_input<?php echo $id_modifier; ?>" value="2" type="radio"<?php if ($authUser['account_level'] == 2) echo ' checked'; ?> />
                        <strong><?= __('QUIZ_BANK'); ?></strong> <?= __('SHARE_OWN_QUIZZES'); ?>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn <?php echo ($authUser['account_level'] == 2) ? 'btn-green' : 'btn-yellow'; ?> btn-sm" id="49_package<?php echo $id_modifier; ?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span><?= __('49_EUR'); ?></span></button>
                    </div>
                </div>
                <?php if (in_array($authUser['account_level'], [1,2]) && !empty($authUser['customer_id'])) : ?>
                    <div class="row m-b-10">
                        <div class="col-md-12">
                            <input name="<?= $input_name; ?>" id="cancel_package_input" value="Cancel" type="radio" />
                            <strong><?= __('CANCEL_SUBSCRIPTION'); ?>:</strong> <?= __('SUBSCRITION_CANCEL_TEXT'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12">
                        <small><?= __('TAX_CHARGE'); ?></small>
                    </div>
                </div>
                <?php if (!in_array($authUser['account_level'], [1,2])) : ?>
                    <div id="payment-details" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-md-12 text-success" id="chosen-package"></div>
                            <div class="col-md-6">
                                <label for="card-payment">
                                <input type="radio" name="payment_option" id="card-payment" value="card" /><img src="<?= $this->request->webroot ?>img/accepted_c22e0.png" /></label>
                            </div>
                            <div class="col-md-6 m-t-10">
                                <label for="invoice-payment"><input type="radio" name="payment_option" id="invoice-payment" value="invoice" /> Invoice (Institutions only)</label>
                            </div>
                            <div class="col-md-12" id="card-portion" style="display: none;">
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
                                                    placeholder="MM / YY"
                                                    autocomplete="cc-exp"
                                                    required 
                                                />
                                            </div>
                                        </div>
                                        <div class="col-xs-5 col-md-5 pull-right">
                                            <div class="form-group">
                                                <label for="cardCVC">CV Code</label>
                                                <input 
                                                    type="tel" 
                                                    class="form-control"
                                                    name="cardCVC"
                                                    placeholder="CVC"
                                                    autocomplete="cc-csc"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <input type="hidden" name="amount" class="amount" />
                                            <button class="subscribe btn btn-success btn-lg btn-block" type="button"><?= __('START_SUBSCRIPTION'); ?></button>
                                        </div>
                                    </div>
                                    <div class="row" style="display:none;">
                                        <div class="col-xs-12">
                                            <p class="payment-errors"></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-12" id="invoice-portion" style="display: none;">
                                <form role="form" id="invoice-payment-form" method="POST" action="javascript:void(0);">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label for="invoiceInfo"><?= __('INVOICE_INFO') ?></label>
                                                <textarea class="form-control" placeholder="<?= __('INVOICE_INFO_PLACEHOLDER'); ?>" name="invoice_info" id="invoiceInfo"></textarea>
                                            </div>                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label for="cardNumber"><?= __('INVOICE_ATTACHMENT') ?></label>
                                                <div id="fileuploader"><?= __('BROWSE_PHOTO'); ?></div>
                                                <?= $this->Form->input('temp_photo', ['type' => 'hidden', 'id' => 'temp_photo']); ?>
                                            </div>                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <input type="hidden" name="amount" class="amount" />
                                            <button class="invoice-subscribe btn btn-success btn-lg btn-block" type="button"><?= __('START_SUBSCRIPTION'); ?></button>
                                        </div>
                                    </div>
                                    <div class="row" style="display:none;">
                                        <div class="col-xs-12">
                                            <p class="invoice-payment-errors"></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <?php if (in_array($authUser['account_level'], [1,2])) : ?>
                    <?php if (empty($authUser['customer_id'])) : ?>
                        <button type="button" class="btn btn-success" id="purchase-again"><?= $lang_strings['next_buy']; ?></button>
                        <?php if ($userPermissions['days_left'] < 30) : ?>
                            <button type="button" class="btn btn-success" data-expire="1" disabled="disabled" id="confirm-upgrade"><?= __('CURRENT_PLAN'); ?></button>
                        <?php else : ?>
                            <button type="button" class="btn btn-success" data-expire="0" disabled="disabled" id="confirm-upgrade"><?= __('CURRENT_PLAN'); ?></button>
                        <?php endif; ?>
                    <?php else : ?>
                        <button type="button" class="btn btn-success" disabled="disabled" id="confirm-upgrade"><?= __('CURRENT_PLAN'); ?></button>
                    <?php endif; ?>
                <?php elseif (in_array($authUser['plan_switched'], ['CANCEL_DOWNGRADE', 'CANCEL_UPGRADE'])) : ?>
                    <button type="button" class="btn btn-success" disabled="disabled" id="confirm-reactivate"><?= __('SUBSCRITION_CANCELLED'); ?></button>
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
    }
</style>
<?php if ($authUser['plan_switched'] == 'CANCEL_UPGRADE') : ?>
    <script type="text/javascript">var prev_account = 2;</script>
<?php elseif ($authUser['plan_switched'] == 'CANCEL_DOWNGRADE') : ?>
    <script type="text/javascript">var prev_account = 1;</script>
<?php else: ?>
<?php endif; ?>
<?php if (!in_array($authUser['account_level'], [1,2])) : ?>
    <script type="text/javascript">
        $(document).on('click', '.invoice-subscribe', function(e){
            var $this = $(this);
            $this.html(lang_strings['processing'] + ' <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);
            $.post(projectBaseUrl + 'users/invoice_payment', $('#invoice-payment-form').serialize())
            .done(function(data, textStatus, jqXHR) {
                var data = $.parseJSON(data);
                if (data.success == true) {
                    $this.html(data.message + ' <i class="fa fa-check"></i>');
                    $('#invoice-payment').modal('hide');
                    $('#invoice-success-dialog').modal('show');
                } else {
                    $this.html(data.message).removeClass('success').addClass('alert-danger');
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                // alert(lang_strings['try_refresh']);
                // window.location.reload();
            });
        });

        $('input[name=payment_option]').click(function() {
            if ($(this).val() == 'card') {
                $('#card-portion').show();
                $('#invoice-portion').hide();
            } else {
                $('#invoice-portion').show();
                $('#card-portion').hide();
            }
        });

        $("#fileuploader").html('').uploadFile({
            url:projectBaseUrl + 'upload/photo',
            fileName:"myfile",
            allowedTypes: "jpg,png,gif,pdf,jpeg",
            //acceptFiles:"image/*",
            showPreview:false,
            showProgress: false,
            multiple:false,
            maxFileCount:1,
            //previewHeight: "100px",
            //previewWidth: "100px",
            dragDropStr: "<span class='upload-drag-drop'>"+ lang_strings['drag_drop'] +"</span>",
            uploadStr: lang_strings['upload'],
            onSuccess:function(files,data,xhr,pd)
            {
                var data = $.parseJSON(data);
                if (data.success) {
                    $('#temp_photo').val(data.filename);
                } else {
                    window.location.reload();
                }   
            }
        });

       
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

            var PublishableKey = 'pk_test_CIZJK6xZRvtKf2umj9w5sbZ1'; // Replace with your API publishable key
            Stripe.setPublishableKey(PublishableKey);
            
            /* Create token */
            var expiry = $form.find('[name=cardExpiry]').payment('cardExpiryVal');
            var ccData = {
                number: $form.find('[name=cardNumber]').val().replace(/\s/g,''),
                cvc: $form.find('[name=cardCVC]').val(),
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
                                $form.find('.subscribe').html(data.message + ' <i class="fa fa-check"></i>');
                                $('#invoice-payment').modal('hide');
                                $('#invoice-success-dialog').modal('show');
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
            window.location.reload();
        });
        /* Fancy restrictive input formatting via jQuery.payment library*/
        $('input[name=cardNumber]').payment('formatCardNumber');
        $('input[name=cardCVC]').payment('formatCardCVC');
        $('input[name=cardExpiry').payment('formatCardExpiry');

        /* Form validation using Stripe client-side validation helpers */
        jQuery.validator.addMethod("cardNumber", function(value, element) {
            return this.optional(element) || Stripe.card.validateCardNumber(value);
        }, "Please specify a valid credit card number.");

        jQuery.validator.addMethod("cardExpiry", function(value, element) {    
            /* Parsing month/year uses jQuery.payment library */
            value = $.payment.cardExpiryVal(value);
            return this.optional(element) || Stripe.card.validateExpiry(value.month, value.year);
        }, "Invalid expiration date.");

        jQuery.validator.addMethod("cardCVC", function(value, element) {
            return this.optional(element) || Stripe.card.validateCVC(value);
        }, "Invalid CVC.");

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
            if ($form.find('[name=cardNumber]').hasClass("success") &&
                $form.find('[name=cardExpiry]').hasClass("success") &&
                $form.find('[name=cardCVC]').val().length > 1) {
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