(function ($) {
    var $form = $('#UserCreateForm'), success_url = '';
    paymentFormReady = function() {
        if (
            $form.find('[name=name]').hasClass("success") &&
            $form.find('[name=email]').hasClass("success") &&
            $form.find('[name=password]').hasClass("success") &&
            $form.find('[name=passwordVerify]').hasClass("success") &&
            (typeof $('input[name=payment_option]:checked').val() != 'undefined') &&
            (
                ($('#card-portion').is(":hidden")) ||
                (
                    !$('#card-portion').is(":hidden") &&
                    $form.find('[name=cardNumber]').hasClass("success") &&
                    $form.find('[name=cardExpiry]').hasClass("success") &&
                    $form.find('[name=cardCVC]').val().length > 1
                ) 
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    var readyInterval = setInterval(function() {
        if (paymentFormReady()) {
            $('#create-user').attr('disabled', false);
        } else {
            $('#create-user').attr('disabled', true);
        }
    }, 250);

    $("#name").focus(function() {
        $(this).removeClass('success');
        $('#name-error').html('').hide();
    }).blur(function() {
        if ($(this).val() == '') {
            $(this).removeClass('success');
            $('#name-error').html(lang_strings['empty_name']).show();
        } else if (!validateName($(this).val())) {
            $(this).removeClass('success');
            $('#name-error').html(lang_strings['invalid_characters']).show();
        } else {
            $(this).addClass('success');
        }
    });

    $("#email").focus(function() {
        $(this).removeClass('success');
        $('#email-error').html('').hide();
    }).blur(function() {
        if ($(this).val() == '') {
            $(this).removeClass('success');
            $('#email-error').html(lang_strings['empty_email']).show();
        } else if (!validateEmail($(this).val())) {
            $(this).removeClass('success');
            $('#email-error').html(lang_strings['invalid_email']).show();
        } else if (!validateEmailExistance($(this).val())) {
            $(this).removeClass('success');
            $('#email-error').html(lang_strings['unique_email']).show();
        } else {
            $(this).addClass('success');
        }
    });

    $("#password").focus(function() {
        $(this).removeClass('success');
        $('#password-error').html('').hide();
    }).blur(function() {
        if ($(this).val() == '') {
            $(this).removeClass('success');
            $('#password-error').html(lang_strings['empty_password']).show();
        } else if ($(this).val().length < 8) {
            $(this).removeClass('success');
            $('#password-error').html(lang_strings['character_count']).show();
        } else {
            $(this).addClass('success');
        }
    });

    $("#passwordverify").focus(function() {
        $(this).removeClass('success');
        $('#passwordverify-error').html('').hide();
    }).blur(function() {
        if (($(this).val() == '') || ($(this).val() != $('#password').val())) {
            $(this).removeClass('success');
            $('#passwordverify-error').html(lang_strings['varify_password']).show();
        } else {
            $(this).addClass('success');   
        }
    });

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', 'button#buy-button-29, #29_package_input, #29_package', function () {
        paymentBasic();
    });

    $(document).on('click', 'button#buy-button-49, #49_package_input, #49_package', function () {
        paymentBank();
    });

    $('input[name=payment_option]').click(function() {
        if ($(this).val() == 'card') {
            $('#tax-info').html(lang_strings['incl_tax']);
            $('#card-portion').show();
            $('#invoice-portion').hide();
        } else {
            $('#tax-info').html(lang_strings['excl_tax']);
            $('#invoice-portion').show();
            $('#card-portion').hide();
        }
        $('#choose-payment').removeClass('text-danger');
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

    //$("#UserCreateForm").submit(function(event) {
    $(document).on('click', "#create-user", function(event){
        event.preventDefault();
        clearInterval(readyInterval);
        // $('input[type=text]:visible, input[type=password]:visible, input[type=tel]:visible').each(function(){
        //     console.log($(this));
        //     if (!$(this).hasClass('success')) {
        //         $("#create-user").attr('disabled', true);
        //         alert('Please check your input and try again');
        //         return false;
        //     }
        // });
        var payment_type = $('input[name=payment_option]:checked').val();
        
        if (payment_type == 'card') {
            // Payment validation
            if (!validator.form()) {
                return false;
            } else {
                /* Visual feedback */
                $('#create-user').html(lang_strings['validating'] + ' <i class="fa fa-spinner fa-pulse"></i>').attr('disabled', true);

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
                        card_success = false;
                    } else {
                        /* Visual feedback */
                        $('#create-user').html(lang_strings['processing'] + ' <i class="fa fa-spinner fa-pulse"></i>');
                        /* Hide Stripe errors on the form */
                        $form.find('.payment-errors').closest('.row').hide();
                        $form.find('.payment-errors').text("");
                        // response contains id and card, which contains additional card details   
                        
                        // AJAX - you would send 'token' to your server here.
                        $.post(projectBaseUrl + 'users/buyCreate', {
                            token: response.id,
                            package: $('#package').val(),
                            name: $('#name').val(),
                            email: $('#email').val(),
                            password: $('#password').val(),
                            passwordVerify: $('#passwordverify').val(),
                            payment_type: payment_type
                        })
                        // Assign handlers immediately after making the request,
                        .done(function(data, textStatus, jqXHR) {
                            var data = $.parseJSON(data);
                            if (data.success == true) {
                                success_url = data.success_url;
                                $('#create-user').html(data.message + ' <i class="fa fa-check"></i>');
                                $('#buy-modal').modal('hide');
                                $('#pay-title').html(lang_strings['stripe_pay_scs_title']);
                                $('#pay-body').html(lang_strings['stripe_pay_scs_body']);
                                $('#invoice-success-dialog').modal('show');
                            } else {
                                $('#create-user').html(data.message).removeClass('success').addClass('alert-danger');
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            $('#create-user').html(lang_strings['pay_failed']).removeClass('success').addClass('error');
                            /* Show Stripe errors on the form */
                            $form.find('.payment-errors').text(lang_strings['try_refresh']);
                            $form.find('.payment-errors').closest('.row').show();
                        });
                    }
                });
            }
        } else {
            /* Visual feedback */
            $('#create-user').html(lang_strings['processing'] + ' <i class="fa fa-spinner fa-pulse"></i>').attr('disabled', true);
            // AJAX - you would send 'token' to your server here.
            $.post(projectBaseUrl + 'users/buyCreate', {
                package: $('#package').val(),
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                passwordVerify: $('#passwordverify').val(),
                payment_type: payment_type,
                temp_photo: $('#temp_photo').val(),
                invoice_info: $('#invoiceInfo').val()
            })
            // Assign handlers immediately after making the request,
            .done(function(data, textStatus, jqXHR) {
                var data = $.parseJSON(data);
                if (data.success == true) {
                    success_url = data.success_url;
                    $('#create-user').html(data.message + ' <i class="fa fa-check"></i>');
                    $('#buy-modal').modal('hide');
                    $('#pay-title').html(lang_strings['pay_scs_title']);
                    $('#pay-body').html(lang_strings['pay_scs_body']);
                    $('#invoice-success-dialog').modal('show');
                } else {
                    $form.find('#create-user').html(data.message).removeClass('success').addClass('alert-danger');
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                $('#create-user').html(lang_strings['pay_failed']).removeClass('success').addClass('error');
                /* Show Stripe errors on the form */
                $form.find('.payment-errors').text(lang_strings['try_refresh']);
                $form.find('.payment-errors').closest('.row').show();
            });
        }

        //$('#UserCreateForm').unbind('submit').submit();
    });

    $('#invoice-success-dialog').on('hidden.bs.modal', function () {
        if (success_url != '') {
            window.location = projectBaseUrl + 'users/' + success_url;
        } else {
            window.location.reload();
        }
    });
    /* Fancy restrictive input formatting via jQuery.payment library*/
    $('input[name=cardNumber]').payment('formatCardNumber');
    $('input[name=cardCVC]').payment('formatCardCVC');
    $('input[name=cardExpiry').payment('formatCardExpiry');

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

    $.extend($.validator.messages, {
        required: lang_strings['required_field']
    });

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
            },
            email: {
                required: false
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

    function validateEmail(email) {
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return re.test(email);
    }

    function validateName(name) {
        var re = /[a-zA-Z0-9]+/;
        return re.test(name);
    }

    function validateEmailExistance(email) {
        var result;
        $.ajax({
            dataType: 'json',
            url: projectBaseUrl + 'users/ajax_user_checking',
            type: 'post',
            async: false,
            data: {'email': email},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    result = true;
                } else {
                    result = false;
                }
            }
        });
        return result;
    }

    function paymentBasic() {
        $('#package').val('29');
        $('#modal-title').html(lang_strings['package_29']);
        $('#29_package_input').prop('checked', true);
        $('#29_package').removeClass('btn-yellow').addClass('btn-green');
        $('#49_package').removeClass('btn-green').addClass('btn-yellow');
        $('#buy-modal').modal('show');
    }

    function paymentBank() {
        $('#package').val('49');
        $('#modal-title').html(lang_strings['package_49']);
        $('#49_package_input').prop('checked', true);
        $('#49_package').removeClass('btn-yellow').addClass('btn-green');
        $('#29_package').removeClass('btn-green').addClass('btn-yellow');
        $('#buy-modal').modal('show');
    }
    
})(jQuery);