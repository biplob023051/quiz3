(function ($) {
	//$("#UserCreateForm").submit(function(event) {
        
    //$("#create_acc").click(function(event) {
    $(document).on('click', "#create_acc", function(event) {
        event.preventDefault();
        // not empty name
        if ($('#name').val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['empty_name']);
            return;
        }
        // check characters validation
        var verify = validateName($('#name').val());
        if (verify == false) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['invalid_characters']);
            return;
        }

        // not empty email
        if ($('#email').val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['empty_email']);
            return;
        }
        // validate email address
        verify = validateEmail($('#email').val());
        if (verify == false) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['invalid_email']);
            return;
        }
        
        // password verify
        if ($("#password").val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['empty_password']);
            return;
        }

        if ($("#password").val().length < 8) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['character_count']);
            return;
        }

        if ($("#passwordverify").val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['varify_password']);
            return;
        }

        if ($("#password").val() != $("#passwordverify").val()) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['varify_password']);
            return;
        }

        // check if empty captcha
        if ($("#captcha").val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['empty_captcha']);
            return;
        }

        // check If email registered
        verify = validateEmailExistance($('#email').val());
        if (verify == false) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['unique_email']);
            $("#email-exist").show();
            return;
        }
        $("#email-exist").hide();
        $("#error-message").hide();
        $('#UserCreateForm').unbind('submit').submit();
    });

    $('[data-toggle="tooltip"]').tooltip();

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

    // modal for youtube video
    $(document).on('click', 'a#play_video', function (e) {
        var src = 'https://www.youtube.com/embed/' + url_src + '?autoplay=1';
        $('#video-modal iframe').attr('src', src);
        $('#video-modal').modal('show');
    });

    $(document).on('click', 'button#close', function (e) {
        $('#video-modal iframe').removeAttr('src');
        $('#video-modal').modal('hide');
    });

    $('#video-modal').on('hidden.bs.modal', function (e) {
        $('#video-modal iframe').removeAttr('src');
    });

})(jQuery);
