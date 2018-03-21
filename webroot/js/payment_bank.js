$(document).on('change', 'input:radio[name="upgrade"]', function(){
    var button_text = '', next_buy = '';
    button_text = lang_strings['confirm'];
    next_buy = lang_strings['next_buy'];
    if ($('#confirm-upgrade').attr('data-expire') != 1) {
        $('#confirm-upgrade').html(button_text).show().attr('disabled', false);
    } else {
        $('#confirm-upgrade').html(button_text).show().attr('disabled', true);
    }
    $('#purchase-again').html(next_buy);
});

$(document).on('click', '#confirm-upgrade', function (e) {
    e.preventDefault();
    var utype = 'Cancel';
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
                $('#pay-title').html(lang_strings['cancel_title']);
                $('#pay-body').html(lang_strings['cancel_body']);
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

$(document).on('click', '#confirm-reactivate', function(e) {
    e.preventDefault();
    var utype = 2;
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
                $('#pay-body').html(lang_strings['reactivate_body']);
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