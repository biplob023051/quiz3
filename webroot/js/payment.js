// Require Bootstrap.js

(function ($) {

    $(document).on('click', '#29_package, #49_package', function () {
        if (this.id == '29_package') {
            $("#29_package_input").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#49_package').removeClass('btn-green').addClass('btn-yellow');
            $('#chosen-package').html('<p><strong>You have chosen 29 package</strong></p>');
        } else {
            $("#49_package_input").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#29_package').removeClass('btn-green').addClass('btn-yellow');
            $('#chosen-package').html('<p><strong>You have chosen 49 package</strong></p>');
        }
        $('.amount').val(parseInt(this.id));
        $('#payment-details').show();
    });

    $(document).on('change', 'input:radio[name="package"]', function(){
        if ($(this).val() == '1') {
            $('#29_package').removeClass('btn-yellow').addClass('btn-green');
            $('#49_package').removeClass('btn-green').addClass('btn-yellow');
            $('#chosen-package').html('<p><strong>You have chosen 29 package</strong></p>');
            $('.amount').val('29');
            $('#payment-details').show();
        } else if ($(this).val() == '2') {
            $('#49_package').removeClass('btn-yellow').addClass('btn-green');
            $('#29_package').removeClass('btn-green').addClass('btn-yellow');
            $('#chosen-package').html('<p><strong>You have chosen 49 package</strong></p>');
            $('.amount').val('49');
            $('#payment-details').show();
        } else {
            // Do nothing       
        }
    });

    $(document).on('click', '#29_package_edit, #49_package_edit', function () {
        if (this.id == '29_package_edit') {
            $("#29_package_input_edit").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
        } else {
            $("#49_package_input_edit").prop("checked", true);
            $(this).removeClass('btn-yellow').addClass('btn-green');
            $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
        }
    });

    $(document).on('change', 'input:radio[name="upgrade"]', function(){
        if ($(this).val() != account_level) {
            var button_text = '';
            switch($(this).val()) {
                case '1':
                    $('#29_package_edit').removeClass('btn-yellow').addClass('btn-green');
                    $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    button_text = lang_strings['downgrade'];
                    break;
                case '2':
                    $('#49_package_edit').removeClass('btn-yellow').addClass('btn-green');
                    $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    button_text = lang_strings['upgrade'];
                    break;
                default:
                    $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
                    button_text = lang_strings['confirm'];
            }
            $('#confirm-upgrade').html(button_text).show().attr('disabled', false);
        } else {
            if (account_level == 1) {
                $('#29_package_edit').removeClass('btn-yellow').addClass('btn-green');
                $('#49_package_edit').removeClass('btn-green').addClass('btn-yellow');
            } else {
                $('#49_package_edit').removeClass('btn-yellow').addClass('btn-green');
                $('#29_package_edit').removeClass('btn-green').addClass('btn-yellow');
            }
            $('#confirm-upgrade').html(lang_strings['current_plan']).show().attr('disabled', true);
        }
    });

    $(document).on('click', '#confirm-upgrade', function (e) {
        e.preventDefault();
        var utype = $('input:radio[name="upgrade"]:checked').val();
        if (utype == account_level) {
            alert('Something went wrong, please try again later');
            window.location.reload();
            return false;
        }
        var $this = $(this);
        $this.attr('disabled', true);
        $.ajax({
            type: 'post',
            url: projectBaseUrl + 'users/changePlan',
            data: {utype: utype},
            dataType: 'json',
            success: function (response)
            {
                $('#invoice-payment').modal('hide');
                var json = $.parseJSON(response);
                if (json.success == true) {
                    account_level = response.account_level;
                    $this.attr('disabled', false);
                    $('#invoice-success-dialog').modal('show');
                } else {
                    $('#invoice-error-dialog').modal('show');
                }
            },
            error: function()
            {
                // alert('Something went wrong, please try again later');
                // window.location.reload();
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
                        icon: 'glyphicon glyphicon-saved',
                        title: "SUCCESS:",
                        message: response.message
                    },{
                        type: 'success',
                        delay: 2000,
                        z_index: 10000,
                    });
                } else {
                    $.notify({
                        icon: 'glyphicon glyphicon-ban-circle',
                        title: "FAILED:",
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

    $(document).on('click', '#send-invoice', function () {
        //console.profile("Sending invoice");
        $.ajax({
            url: projectBaseUrl + 'invoice/create',
            dataType: 'json',
            success: function (response)
            {
                if(response.success)
                {
                    $('#invoice-dialog').modal('hide');
                    $('#invoice-success-dialog').modal('show');
                    $('#upgrade_account').attr('disabled', true);
                    $('span#btn_text').html(lang_strings['request_sent']);
                }
            },
            error: function()
            {
                $('#invoice-dialog').modal('hide');
                $('#invoice-error-dialog').modal('show');
            }
        });
        //console.profileEnd();
    });

    $('#invoice-success-dialog').on('hidden.bs.modal', function () {
        window.location.reload();
    });

    $(document).on('change', '#quiz-filter select', function () {
        $('form#quiz-filter').submit();
    });

    $(document).on('click', 'button.delete-quiz', function () {
        var quiz_id = $(this).attr('quiz-id'),
            button_box = $(this); 
        var infoModal = $('#confirm-delete');
        $.ajax({
            data: {'quiz_id': quiz_id},
            type: 'post',
            url: projectBaseUrl + 'quizzes/single',
            dataType: 'json',
            success: function (response)
            {
                if (response.success == 1) {
                    var bodyData = lang_strings['delete_quiz_1'] + response.no_of_answers + lang_strings['delete_quiz_2'] + response.no_of_students +
                    lang_strings['delete_quiz_3'] + response.no_of_questions + lang_strings['delete_quiz_4'];
                    var headerData = lang_strings['delete_quiz_5'] + response.quiz_name + '?';
                    var link = projectBaseUrl + 'quizzes/quizDelete/' + response.id;
                    infoModal.find('.modal-body').html(bodyData);
                    infoModal.find('.modal-header').html(headerData);
                    infoModal.find('.modal-footer a').attr('href', link);
                    infoModal.modal('show');
                } else {
                    window.location.reload();
                }
            }
        });   
        
    });

    $(document).on('click', 'button.active-quiz', function () {
        var quiz_id = $(this).attr('id'),
            status = $(this).attr('status'),
            button_box = $(this);
        $.ajax({
            data: {'quiz_id': quiz_id, 'status': status},
            type: 'post',
            url: projectBaseUrl + 'quizzes/changeStatus',
            dataType: 'json',
            success: function (response)
            {
                if (response.result === 1)
                {
                    if ((response.filter == "1") || (response.filter == "0")) {
                        button_box.closest('tr').remove();
                    } else {
                        location.reload();
                    }
                    
                } else {
                    alert(response.message);
                }
            }
        });    
    });

    $(document).on('click', 'button.duplicate-quiz', function () {
        var quiz_id = $(this).attr('quiz-id'),
            button_box = $(this);
        $.ajax({
            data: {'quiz_id': quiz_id},
            type: 'post',
            url: projectBaseUrl + 'quizzes/duplicate',
            dataType: 'json',
            success: function (response)
            {
                if (response.result === 1)
                {
                    if (response.id != '') {
                        window.location.href = projectBaseUrl + 'quizzes/edit/' + response.id;
                    } else {
                        location.reload();
                    } 
                } else {
                    alert(response.message);
                }
            }
        });    
    });

    // Share quiz
    $(document).on('click', 'button.share-quiz', function () {
        var quiz_id = $(this).attr('quiz-id'),
            quiz_name = $(this).attr('quiz-name');
        $('#confirm-delete').find('.modal-body').html(lang_strings['share_quiz_question']);
        $('#confirm-delete').find('.modal-header').html(lang_strings['share_quiz'] + ': <b>' + quiz_name + '</b>?');
        $('#confirm-delete').find('.modal-footer a').attr('href', projectBaseUrl + 'quizzes/share/' + quiz_id).removeClass('btn-danger').addClass('btn-success').html(lang_strings['share_quiz']);
        $('#confirm-delete').modal('show');
    });

    // Remove shared quiz
    $(document).on('click', 'button.remove-share', function () {
        var quiz_id = $(this).attr('quiz-id'),
            quiz_name = $(this).attr('quiz-name');
        $('#confirm-delete').find('.modal-body').html(lang_strings['remove_share_question']);
        $('#confirm-delete').find('.modal-header').html(lang_strings['remove_share'] + ' ' + quiz_name + '?');
        $('#confirm-delete').find('.modal-footer a').attr('href', projectBaseUrl + 'quizzes/share/' + quiz_id + '/1').removeClass('btn-danger').addClass('btn-success').html(lang_strings['remove_shared_quiz']);
        $('#confirm-delete').modal('show');
    });


    // Quiz bank
    $(document).on('click', 'button.quiz-bank', function () {
        $.ajax({
            data: {},
            type: 'get',
            url: projectBaseUrl + 'quizzes/ajax_bank',
            dataType: 'html',
            success: function (data)
            {
                $('#public-quiz').html(data).modal('show');
            }
        });
    });

    $(document).on('click', '#import', function () {
        window.location.assign(projectBaseUrl + "maintenance/load_dummy_data");  
    });

    // Open sharing decline reason modal
    $(document).on('click', '.view-reason', function() {
        var quiz_id = $(this).attr('quiz-id');
        $('#reason_' + quiz_id).modal('show');
    });


})(jQuery);