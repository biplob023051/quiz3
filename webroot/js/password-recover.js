(function($) {
   	$('[data-toggle="tooltip"]').tooltip();
   
   	$(document).on('click', '#send_request', function(event){
		event.preventDefault();
		if ($("#email").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings['empty_email']);
			return false;
		}

		var verify = validateEmail($('#email').val());
		if (verify == false) {
			$("#error-message").show();
			$("#error-message").html(lang_strings['invalid_email']);
			return false;
		}

		verify = validateEmailExistance($('#email').val());
		
		if (verify == false) {
			$("#error-message").show();
			$("#error-message").html(lang_strings['not_found_email']);
			return false;
		} else {
			$('#UserPasswordRecoverForm').unbind('submit').submit();
		}

	});

	$("#UserResetPasswordForm").submit(function(event) {
		event.preventDefault();
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

		$('#UserResetPasswordForm').unbind('submit').submit();

	});

	function validateEmail(email) {
	    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	    return re.test(email);
	}

	function validateEmailExistance(email) {
		var result;
		$.ajax({
            dataType: 'json',
            url: projectBaseUrl + 'users/ajax_email_checking',
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

})(jQuery);