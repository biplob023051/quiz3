<!-- Modal -->
<div class="modal fade" id="invoice-payment" tabindex="-1" role="dialog" aria-labelledby="invoice-dialog-title" aria-hidden="true">
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
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <small>(Incl. 24% sales tax)</small>
                    </div>
                </div>
                <div id="payment-details" style="display: none;">
                    <hr>
                    <div class="row">
                        <div class="col-md-12 text-success" id="chosen-package"></div>
                        <div class="col-md-1">
                            <input type="radio" name="payment_option" id="card-payment" value="card" />
                        </div>
                        <div class="col-md-5">
                            <label for="card-payment"><img style="margin: -12px 0 0 -24px;" class="img-responsive" src="http://i76.imgup.net/accepted_c22e0.png" /></label>
                        </div>
                        <div class="col-md-1">
                            <input type="radio" name="payment_option" id="invoice-payment" value="invoice" />
                        </div>
                        <div class="col-md-5">
                            <label for="invoice-payment"><span style="margin: 0 0 0 -24px;">Invoice (Institutions only)</span></label>
                        </div>
                        <div class="col-md-12" id="card-portion" style="display: none;">
                            <!-- If you're using Stripe for payments -->
                            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
                            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
                            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
                            <script src="https://js.stripe.com/v2/"></script>
                            <script src="https://js.stripe.com/v3/"></script>
                            <form role="form" id="payment-form" method="POST" action="javascript:void(0);">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="cardNumber">CARD NUMBER</label>
                                            <div class="input-group">
                                                <input 
                                                    type="tel"
                                                    class="form-control"
                                                    name="cardNumber"
                                                    placeholder="Valid Card Number"
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
                                            <label for="cardExpiry"><span class="hidden-xs">EXPIRATION</span><span class="visible-xs-inline">EXP</span> DATE</label>
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
                                            <label for="cardCVC">CV CODE</label>
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
                                        <button class="subscribe btn btn-success btn-lg btn-block" type="button">Start Subscription</button>
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
                            <link href="https://hayageek.github.io/jQuery-Upload-File/4.0.10/uploadfile.css" rel="stylesheet">
                            <script src="https://hayageek.github.io/jQuery-Upload-File/4.0.10/jquery.uploadfile.min.js"></script>
                            <form role="form" id="payment-form" method="POST" action="javascript:void(0);">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="invoiceInfo"><?= __('INVOICE_INFO') ?></label>
                                            <textarea class="form-control" placeholder="<?= __('INVOICE_INFO_PLACEHOLDER') ?>" name="invoice_info" id="invoiceInfo"></textarea>
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
                                        <button class="invoice-subscribe btn btn-success btn-lg btn-block" type="button" disabled="disabled">Start Subscription</button>
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
            </div>
            <div class="modal-footer">
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
</style>

<script type="text/javascript">
    $('input[name=payment_option]').click(function() {
        if ($(this).val() == 'card') {
            $('#card-portion').show();
            $('#invoice-portion').hide();
        } else {
            $('#invoice-portion').show();
            $('#card-portion').hide();
        }
    });
</script>

<script type="text/javascript">
    $("#fileuploader").html('').uploadFile({
        url:projectBaseUrl + 'upload/photo',
        fileName:"myfile",
        acceptFiles:"image/*",
        showPreview:true,
        multiple:false,
        previewHeight: "100px",
        previewWidth: "100px",
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
</script>

<script type="text/javascript">
    /*
The MIT License (MIT)

Copyright (c) 2015 William Hilton

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
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
    $form.find('.subscribe').html('Validating <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);

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
            $form.find('.subscribe').html('Try again').prop('disabled', false);
            /* Show Stripe errors on the form */
            $form.find('.payment-errors').text(response.error.message);
            $form.find('.payment-errors').closest('.row').show();
        } else {
            /* Visual feedback */
            $form.find('.subscribe').html('Processing <i class="fa fa-spinner fa-pulse"></i>');
            /* Hide Stripe errors on the form */
            $form.find('.payment-errors').closest('.row').hide();
            $form.find('.payment-errors').text("");
            // response contains id and card, which contains additional card details            
            console.log(response.id);
            console.log(response.card);
            var token = response.id;
            // AJAX - you would send 'token' to your server here.
            $.post('/account/stripe_card_token', {
                    token: token
                })
                // Assign handlers immediately after making the request,
                .done(function(data, textStatus, jqXHR) {
                    $form.find('.subscribe').html('Payment successful <i class="fa fa-check"></i>');
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $form.find('.subscribe').html('There was a problem').removeClass('success').addClass('error');
                    /* Show Stripe errors on the form */
                    $form.find('.payment-errors').text('Try refreshing the page and trying again.');
                    $form.find('.payment-errors').closest('.row').show();
                });
        }
    });
}
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