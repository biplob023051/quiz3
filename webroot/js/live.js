(function($) {
	// right click disabled
	$(document).on("contextmenu",function(e){
        e.preventDefault();
        alert(lang_strings['right_click_disabled']);
    });
 
	var interval;
	var answered = {};
 	var std_updated = false;
 	var glob_index = 0;
 	interval = setInterval(checkInternetConnection, 1000);
 	function checkInternetConnection() {
 		if (!navigator.onLine) {
 			$('#std_form_submit').text(lang_strings['disabled_submit']).attr('disabled', true);
 			$('#confirmed').attr('disabled', true);
 			$('.no-internet').show();
 		} else {
 			if (std_updated) {
 				updateStudentBasicInfo();
 				std_updated = false;
 			}

 			if (!$.isEmptyObject(answered) && !std_updated) {
 				clearInterval(interval);
 				runAjaxCall(0);
 			}
 		}
 	}

 	function runAjaxCall(index) {
 		var question = answered[index];
 		$.ajax({
            dataType: 'json',
            url: projectBaseUrl + 'students/update_answer',
            type: 'post',
            data: {'question_id': question.question_id, 'text' : question.answer_text, 'checkbox_record' : question.checkbox_record, 'checkBoxDelete' : question.checkBoxDelete, 'checkbox_record_delete' : question.checkbox_record_delete, 'checkBox' : question.checkBox, 'random_id' : question.random_id, 'case_sensitive' : question.case_sensitive},
            success: function (response)
            {
				if (response.success) {
					$('#quest-' + question.question_id).removeClass('glyphicon-refresh spinning').addClass('glyphicon-ok-sign text-success');
					index++;
					if (index < glob_index) {
						runAjaxCall(index);
					} else {
						glob_index = 0;
						answered = {};
						interval = setInterval(checkInternetConnection, 1000);
						updateConnection();
					}
				} else {
					alert('Something went wrong, please try now');
	            	window.location.reload();
				}
            }
        });
 	}

 	function updateConnection() {
 		$('.no-internet').hide();
		$('#std_form_submit').text(lang_strings['enabled_submit']).attr('disabled', false);
		$('#confirmed').attr('disabled', false);
 	}

	$.fn.extend({
        doneTyping: function(callback,timeout){
            timeout = timeout || 2e3; // 10 second default timeout
            var timeoutReference,
                doneTyping = function(el){
                    if (!timeoutReference) return;
                    timeoutReference = null;
                    callback.call(el);
                };
            return this.each(function(i,el){
                var $el = $(el);
                // Chrome Fix (Use keyup over keypress to detect backspace)
                // thank you @palerdot
                $el.is(':input') && $el.on('keyup keypress mouseup',function(e){
                    // This catches the backspace button in chrome, but also prevents
                    // the event from triggering too premptively. Without this line,
                    // using tab/shift+tab will make the focused element fire the callback.
                    //if (e.type=='keyup' && e.keyCode!=8) return;
                    
                    // Check if timeout has been set. If it has, "reset" the clock and
                    // start over again.
                    if (timeoutReference) clearTimeout(timeoutReference);
                    timeoutReference = setTimeout(function(){
                        // if we made it here, our timeout has elapsed. Fire the
                        // callback
                        doneTyping(el);
                    }, timeout);
                }).on('blur',function(){
                	if (timeoutReference) clearTimeout(timeoutReference);
                    // If we can, fire the event since we're leaving the field
                    doneTyping(el);
                });
            });
        }
    });

	$('#fname, #lname, #class').doneTyping(function(){
		var $element = $(this);
		if ($element.val() != '' && $element.val() != $element.attr('data-value')) {
			$element.parent().next().removeClass('glyphicon-ok-sign text-success').addClass('glyphicon-refresh spinning'); // Upload failed indicator
			if (navigator.onLine) {
				$('#std_form_submit').text(lang_strings['disabled_submit']).attr('disabled', true);
				updateStudentBasicInfo();
			} else {
				std_updated = true;
			}
		}
	});

	function updateStudentBasicInfo() {
		var fname = $('#fname').val();
		var lname = $('#lname').val();
		var std_class = $('#class').val();
		if ((fname != '') || (lname != '') || (std_class != '')) { // only save if 3 basic information exist
			// Execute for student information save
			$.ajax({
	            dataType: 'json',
	            url: projectBaseUrl + 'students/update_student',
	            type: 'post',
	            data: {'fname': fname, 'lname' : lname, 'class' : std_class, 'random_id' : random_id},
	            success: function (response)
	            {
	            	if (response.success) {
	                	$('#studentId').attr('value', response.student_id);
	                	if (fname != '') {
	                		$('#std-fname').removeClass('glyphicon-refresh spinning').addClass('glyphicon-ok-sign text-success');
	                		$('#fname').attr('data-value', fname);
	                	}
	                	if (lname != '') {
	                		$('#std-lname').removeClass('glyphicon-refresh spinning').addClass('glyphicon-ok-sign text-success');
	                		$('#lname').attr('data-value', lname);
	                	}
	                	if (std_class != '') {
	                		$('#std-class').removeClass('glyphicon-refresh spinning').addClass('glyphicon-ok-sign text-success');
	                		$('#class').attr('data-value', std_class);
	                	}	    
	                	$('#std_form_submit').text(lang_strings['enabled_submit']).attr('disabled', false);        	
	            	} else {
	            		alert('Something went wrong, please try now');
	            		window.location.reload();
	            	}
	            }
	        });
		}	
	}


	// For checkbox and radio buttons
	$(document).on('change', ".tick-mark", function(){
		answering($(this));
	});

	// For text area and text fields
	$(".typing").doneTyping(function(){
		answering($(this));
	});

	// Common method for all input type change or done typing
	function answering(element) {
		$('#std_form_submit').text(lang_strings['disabled_submit']).attr('disabled', true);
		var question_id = element.closest('tr').prev().val();
		var checkbox_record = [];
		var checkBoxDelete = '';
		var checkbox_record_delete = 1;
		var checkBox = '';
		// temp
		var checkBoxName = '';
		if (element.is(':checkbox')) {
			if (element.is(':checked')) {
				checkbox_record_delete = ''; // New Record
			} 
			checkBoxName = element.attr('name');
			$("input[name='"+checkBoxName+"']").each( function (i) {
				if (element.is(':checked')) {
					checkbox_record[i] = element.val();
				}
			});
			// Check if all unchecked
			if (checkbox_record.length < 1) {
				checkBoxDelete = 1;
			}
			checkBox = 1;
		}
    	$('#quest-' + question_id).removeClass('glyphicon-ok-sign text-success').addClass('glyphicon-refresh spinning');
		answered[glob_index] = {
			'question_id' : question_id,
			'case_sensitive' : element.closest('tr').prev().attr('data-case'),
			'answer_text' : element.val(),
			'checkbox_record' : checkbox_record,
			'checkBoxDelete' : checkBoxDelete,
			'checkbox_record_delete' : checkbox_record_delete,
			'checkBox' : checkBox,
			'random_id' : random_id
		}
		glob_index++;
		maxAllowedCheckBoxControl(element);
	}
	
	// if many correct, then checkbox
	function maxAllowedCheckBoxControl(element) {
		var choiceContainer = element.closest('.choices');
		var max_allowed = parseInt(choiceContainer.prev().find('.max_allowed').text());
		if (!isNaN(max_allowed)) {
			var currentlyChecked = choiceContainer.find('input[type="checkbox"]:checked').length;
			if (currentlyChecked >= max_allowed) {
				choiceContainer.find('input[type="checkbox"]:not(:checked)').attr('disabled', true);
				choiceContainer.find('input[type="checkbox"]:not(:checked)').addClass('max_allowed_disabled');
			} else {
				choiceContainer.find('input[type="checkbox"]:not(:checked)').attr('disabled', false);
				choiceContainer.find('input[type="checkbox"]:not(:checked)').removeClass('max_allowed_disabled');
			}
		} 
	}
   	
   	$("#StudentLiveForm").submit(function(event) {
		event.preventDefault();
		if (!navigator.onLine) {
			alert(lang_strings[0]);
			return;
		}
		$("#error-message").hide();
		var validateStdError = checkValidation();
		if (validateStdError == true) {
			$('html, body').animate({
		        scrollTop: $(".page-header").offset().top
		    }, 500);
		} else {
			// check alpha numeric last name, first name and class
			validateStdError = checkAlphaNumeric();
			if (validateStdError == true) {
				$('html, body').animate({
			        scrollTop: $(".page-header").offset().top
			    }, 500);
			} else {
				var infoModal = $('#confirm-submit');
				var numbers = [];
				var i = 0;
				$("#questions").find('.form-group :input').each(function(){
					i++;
		            if ($(this).val() == '') {
		            	// $(this).closest('tr').attr('id').match(/\d+/)
		            	var j = $(this).closest('tr').attr('id').match(/\d+/);
	            		numbers.push(j);
		            }          
		        });

		        var radioArray = [];
				$("#questions").find('.radio :input').each(function(){
					if(jQuery.inArray($(this).attr('name'), radioArray)!==-1) {

					} else {
						radioArray.push($(this).attr('name'));
					}        
			    });
			    if (radioArray.length > 0) {
			    	$.each( radioArray, function( key, value ) {
					  	if (!$("input[name='"+value+"']:checked").val()) {
						   var j = $("input[name='"+value+"']").closest('tr').attr('id').match(/\d+/);
						   numbers.push(j);
						}
					});
			    }

			    var checkboxArray = [];
				$("#questions").find('.checkbox :input').each(function(){
					if(jQuery.inArray($(this).attr('name'), checkboxArray)!==-1) {

					} else {
						checkboxArray.push($(this).attr('name'));
					}        
			    });
			    if (checkboxArray.length > 0) {
			    	$.each( checkboxArray, function( key, value ) {
					  	if (!$("input[name='"+value+"']:checked").val()) {
						   var j = $("input[name='"+value+"']").closest('tr').attr('id').match(/\d+/);
						   numbers.push(j);
						}
					});
			    }

		        if (numbers.length > 0) {
		        	// array sorting
		        	numbers.sort(function(a,b) {
					  if (isNaN(a) || isNaN(b)) {
					    return a > b ? 1 : -1;
					  }
					  return a - b;
					});
					// array implode by delimiter ','
		        	var str = numbers.join();
		        	str = lang_strings[2] + str + lang_strings[3] + lang_strings[4];
		        } else {
		        	var str = lang_strings[1];
		        } 
		        infoModal.find('.modal-body').html(str);
		        infoModal.modal('show');
		        
		        $(document).on('click', 'button#confirmed', function () {
		        	window.btn_clicked = true;
		        	$('#StudentLiveForm').unbind('submit').submit();
		        });
			}

		}

	});

	function checkValidation() {

		if ($("#fname").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings[5]);
			return true;
		}

		if ($("#lname").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings[6]);
			return true;
		}

		if ($("#class").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings[7]);
			return true;
		}
		return false;
	}

	function checkAlphaNumeric () {
	    var pattern = /[a-zA-Z0-9]+/;

	    if (pattern.test($("#StudentFname").val()) == false) {
	    	$("#error-message").show();
			$("#error-message").html(lang_strings['first_name_invalid']);
	        return true;
	    }
	    
	    if (pattern.test($("#StudentLname").val()) == false) {
	    	$("#error-message").show();
			$("#error-message").html(lang_strings['last_name_invalid']);
	        return true;
	    }

	    if (pattern.test($("#StudentClass").val()) == false) {
	    	$("#error-message").show();
			$("#error-message").html(lang_strings['class_invalid']);
	        return true;
	    }
	    return false;
	}

    //document.getElementById("StudentLiveForm").reset();
})(jQuery);
