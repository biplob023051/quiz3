// Require Bootstrap.js
(function ($) {
    $(document).on('click', '#29_package, #49_package', function () {
        if (this.id == '29_package') {
            $("#29_package_input").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#49_package').removeClass('btn-green').addClass('btn-yellow');
            $('.subscribe-text').html(lang_strings['basic_btn_txt']);
        } else {
            $("#49_package_input").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#29_package').removeClass('btn-green').addClass('btn-yellow');
            $('.subscribe-text').html(lang_strings['bank_btn_txt']);
        }
        $('.amount').val(parseInt(this.id));
        $('#payment-details').show();
    });

    $(document).on('change', 'input:radio[name="package"]', function(){
        if ($(this).val() == '1') {
            $('#29_package').removeClass('btn-yellow').addClass('btn-green');
            $('#49_package').removeClass('btn-green').addClass('btn-yellow');
            $('.amount').val('29');
            $('.subscribe-text').html(lang_strings['basic_btn_txt']);
            $('#payment-details').show();
        } else if ($(this).val() == '2') {
            $('#49_package').removeClass('btn-yellow').addClass('btn-green');
            $('#29_package').removeClass('btn-green').addClass('btn-yellow');
            $('.amount').val('49');
            $('.subscribe-text').html(lang_strings['bank_btn_txt']);
            $('#payment-details').show();
        } else {
            // Do nothing       
        }
    });

    $(document).on('click', '#29_package_edit, #49_package_edit', function () {
        var button_text, chosen_account;
        if (this.id == '29_package_edit') {
            chosen_account = 1;
            $("#29_package_input_edit").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
        } else {
            chosen_account = 2;
            $("#49_package_input_edit").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
        }
        if (chosen_account != account_level) {
            button_text = (chosen_account == 1) ? lang_strings['downgrade'] : lang_strings['upgrade'];
            if ($('#confirm-upgrade').attr('data-expire') != 1) {
                $('#confirm-upgrade').html(button_text).show().attr('disabled', false);
            } else {
                $('#confirm-upgrade').html(button_text).show().attr('disabled', true);
            }
            (chosen_account == 1) ? $('#purchase-again').html(lang_strings['downgrade_next_buy']) : $('#purchase-again').html(lang_strings['upgrade_next_buy']);
        } else {
            $('#confirm-upgrade').html(lang_strings['current_plan']).show().attr('disabled', true); 
            $('#purchase-again').html(lang_strings['next_buy']);   
        }
    });

    $(document).on('change', 'input:radio[name="upgrade"]', function(){
        if ($(this).val() != account_level) {
            var button_text = '', next_buy = '';
            switch($(this).val()) {
                case '1':
                    $('#29_package_edit').removeClass('btn-yellow').addClass('btn-green');
                    $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    button_text = lang_strings['downgrade'];
                    next_buy = lang_strings['downgrade_next_buy'];
                    break;
                case '2':
                    $('#49_package_edit').removeClass('btn-yellow').addClass('btn-green');
                    $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    button_text = lang_strings['upgrade'];
                    next_buy = lang_strings['upgrade_next_buy'];
                    break;
                default:
                    $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    button_text = lang_strings['confirm'];
                    next_buy = lang_strings['next_buy'];
            }

            if ($('#confirm-upgrade').attr('data-expire') != 1) {
                $('#confirm-upgrade').html(button_text).show().attr('disabled', false);
            } else {
                $('#confirm-upgrade').html(button_text).show().attr('disabled', true);
            }
            $('#purchase-again').html(next_buy);
        } else {
            if (account_level == 1) {
                $('#29_package_edit').removeClass('btn-yellow').addClass('btn-green');
                $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
            } else {
                $('#49_package_edit').removeClass('btn-yellow').addClass('btn-green');
                $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
            }
            $('#confirm-upgrade').html(lang_strings['current_plan']).show().attr('disabled', true);
            $('#purchase-again').html(lang_strings['next_buy']);
        }
    });

    $(document).on('click', '#29_package_activate, #49_package_activate', function () {
        var chosen_account, button_text = '';
        if (this.id == '29_package_activate') {
            chosen_account = 1;
            $("#29_package_input_activate").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#49_package_activate').removeClass('btn-green').addClass('btn-yellow');
        } else {
            chosen_account = 2;
            $("#49_package_input_activate").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#29_package_activate').removeClass('btn-green').addClass('btn-yellow');
        }
        if (account_level == chosen_account) {
            button_text = lang_strings['reactivate'];
        } else {
            button_text = (account_level == 2) ? lang_strings['reactivate_downgrade'] : lang_strings['reactivate_upgrade'];
        }
        $('#confirm-reactivate').html(button_text).show().attr('disabled', false);
    });

    $(document).on('change', 'input:radio[name="activate"]', function(){
        var button_text = '';
        if (account_level == $(this).val()) {
            button_text = lang_strings['reactivate'];
        } else {
            button_text = (account_level == 2) ? lang_strings['reactivate_downgrade'] : lang_strings['reactivate_upgrade'];
        }
        switch($(this).val()) {
            case '1':
                $('#29_package_activate').removeClass('btn-yellow').addClass('btn-green');
                $('#49_package_activate').removeClass('btn-green').addClass('btn-yellow');
                break;
            case '2':
                $('#49_package_activate').removeClass('btn-yellow').addClass('btn-green');
                $('#29_package_activate').removeClass('btn-green').addClass('btn-yellow');
                break;
            default:
                window.location.reload();
                break;
        }
        $('#confirm-reactivate').html(button_text).show().attr('disabled', false);
    });

    $(document).on('click', '#confirm-reactivate', function(e) {
        e.preventDefault();
        var utype = $('input:radio[name="activate"]:checked').val();
        $(this).html(lang_strings['processing'] + ' <i class="fa fa-spinner fa-pulse"></i>').attr('disabled', true);
        $.ajax({
            type: 'post',
            url: projectBaseUrl + 'users/reactivatePlan',
            data: {utype: utype},
            dataType: 'json',
            success: function (response)
            {
                $('#invoice-payment').modal('hide');
                if (response.success == true) {
                    $('#pay-title').html(lang_strings['reactivate_title']);
                    if (response.type == 'UPGRADE') {
                        $('#pay-body').html(lang_strings['reactivate_upgraded_body']);
                    } else if (response.type == 'DOWNGRADE') {
                        $('#pay-body').html(lang_strings['reactivate_downgraded_body']);
                    } else {
                        $('#pay-body').html(lang_strings['reactivate_body']);
                    }
                    $('#invoice-success-dialog').modal('show');
                } else {
                    $('#invoice-error-dialog').modal('show');
                }
            },
            error: function()
            {
                alert('Something went wrong, please try again later');
                window.location.reload();
            }
        });
    });

    $(document).on('click', '#confirm-upgrade', function (e) {
        e.preventDefault();
        var utype = $('input:radio[name="upgrade"]:checked').val();
        if (utype == account_level) {
            alert('Something went wrong, please try again later');
            window.location.reload();
            return false;
        }
        $(this).html(lang_strings['processing'] + ' <i class="fa fa-spinner fa-pulse"></i>').attr('disabled', true);
        $.ajax({
            type: 'post',
            url: projectBaseUrl + 'users/changePlan',
            data: {utype: utype},
            dataType: 'json',
            success: function (response)
            {
                $('#invoice-payment').modal('hide');
                if (response.success == true) {
                    if (utype == 'Cancel') {
                        $('#pay-title').html(lang_strings['cancel_title']);
                        $('#pay-body').html(lang_strings['cancel_body']);
                    } else if (utype == 1) {
                        $('#pay-title').html(lang_strings['downgrade_title']);
                        $('#pay-body').html(lang_strings['downgrade_body']);
                    } else {
                        $('#pay-title').html(lang_strings['upgrade_title']);
                        $('#pay-body').html(lang_strings['upgrade_body']);
                    }
                    $('#invoice-success-dialog').modal('show');
                } else {
                    alert('Something went wrong, please try again later');
                    window.location.reload();
                }
            },
            error: function()
            {
                alert('Something went wrong, please try again later');
                window.location.reload();
            }
        });
    });

    // Invoice paid users next subscription
    $(document).on('click', '#purchase-again', function(e){
        e.preventDefault();
        var utype = $('input:radio[name="upgrade"]:checked').val();
        $(this).html(lang_strings['processing'] + ' <i class="fa fa-spinner fa-pulse"></i>').attr('disabled', true);
        $.ajax({
            type: 'post',
            url: projectBaseUrl + 'users/nextYearSubscription',
            data: {utype: utype},
            dataType: 'json',
            success: function (response)
            {
                $('#invoice-payment').modal('hide');
                if (response.success == true) {
                    $('#pay-title').html(lang_strings['next_year_title']);
                    if (response.type == 3) {
                        $('#pay-body').html(lang_strings['upgrade_next_year_body']);
                    } else if (response.type == 2) {
                        $('#pay-body').html(lang_strings['downgrade_next_year_body']);
                    } else {
                        $('#pay-body').html(lang_strings['next_year_body']);
                    }
                    $('#invoice-success-dialog').modal('show');
                } else {
                    $('#invoice-error-dialog').modal('show');
                }
            },
            error: function()
            {
                alert('Something went wrong, please try again later');
                window.location.reload();
            }
        });    
    });

    $(document).on('submit', '#change-form', function(e) {
        e.preventDefault();
        $('#old-password-error').hide();
        $('#password1-error').hide();
        $('#password2-error').hide();
        $('#submit-change').attr('disabled', true);
        $.ajax({
            type: 'post',
            url: projectBaseUrl + 'users/changePassword',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response)
            {
                $('#submit-change').attr('disabled', false);
                if (response.success) {
                    $('#change-password').modal('hide');
                    $.notify({
                        icon: "",
                        title: "",
                        message: response.message
                    },{
                        type: 'success',
                        delay: 2000,
                        z_index: 10000,
                    });
                } else {
                    $.notify({
                        icon: "",
                        title: "",
                        message: response.message
                    },{
                        type: 'danger',
                        delay: 1000,
                        z_index: 10000,
                    });
                    $.each(response.errors, function( index, value ) {
                        if (index == 'old_password') {
                            if (value._empty) {
                                $('#old-password-error').text(value._empty).show();
                            } else {
                                $('#old-password-error').text(value.custom).show();
                            }
                        } else {
                            if (value._empty) {
                                $('#'+index+'-error').text(value._empty).show();
                            } else if (value.length) {
                                $('#'+index+'-error').text(value.length).show();
                            } else {
                                $('#'+index+'-error').text(value.match).show();
                            }
                        }
                    });
                }
            },
            error: function()
            {
                alert('Something went wrong, please try again later');
                window.location.reload();
            }
        });
    });

    $('#invoice-success-dialog').on('hidden.bs.modal', function () {
        if (typeof paySuccess !== 'undefined' && paySuccess == '1') {
            window.location = projectBaseUrl + 'users/paySuccess';
        } else {
            window.location.reload();
        }
    });

    $(document).on('change', '#quiz-filter select', function () {
        $('form#quiz-filter').submit();
    });

    $('#change-password').on('shown.bs.modal', function (e) {
        $('#change-form')[0].reset();
        $('#old-password-error').hide();
        $('#password1-error').hide();
        $('#password2-error').hide();
    });

    // On language change from user settings page
    $(document).on('change', '#language', function(){
        $('.allsubject').hide();
        $('#subjects-' + $(this).val()).show();
    });

})(jQuery);