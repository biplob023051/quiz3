(function ($) {
    $('#maintenance-alert').css({width: $(window).width()});
	$.fn.extend({
        donetyping: function(callback,timeout){
            timeout = timeout || 3e3; // 1 second default timeout
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
                    if (e.type=='keyup' && e.keyCode!=8) return;
                    
                    // Check if timeout has been set. If it has, "reset" the clock and
                    // start over again.
                    if (timeoutReference) clearTimeout(timeoutReference);
                    timeoutReference = setTimeout(function(){
                        // if we made it here, our timeout has elapsed. Fire the
                        // callback
                        doneTyping(el);
                    }, timeout);
                }).on('blur',function(){
                    // If we can, fire the event since we're leaving the field
                    doneTyping(el);
                });
            });
        }
    });

    $(document).on('click', '.user-info', function () {
        $('.update-user').each(function(){
            $(this).hide();
            $(this).prev().show();
        });
        $(this).hide();
        $(this).next().show();
        var elem = $(this);
        setTimeout(function(){
            elem.next().hide();
            elem.show();
        }, 15000);
    });

    var globalElement;
    var updatedRole;
    $(document).on('change', 'select.change-level', function(){
        globalElement = $(this);
        $('#change-role').modal('show');
    });

    $(document).on('click', '#confirm-role', function(e){
        e.preventDefault();
        $(this).attr('disabled', true);
        updateInfo(globalElement);
        $('#change-role').modal('hide');
    });

    $('#change-role').on('hidden.bs.modal', function () {
        $('#confirm-role').attr('disabled', false);
        $('.update-user').each(function(){
            $(this).hide();
            $(this).prev().show();
        });
        if (!updatedRole) globalElement.val(globalElement.attr('data-value'));
        globalElement = '';
        updatedRole = '';
    });

	$('input.update-user').donetyping(function(){
        updateInfo($(this));
    });

    var updatedStatus;
    $(document).on('change', '.make-inactive', function(){
        globalElement = $(this);
        if ($(this).is(":checked")) {
            $('#status-body').text('YOU_ARE_GOING_TO_ACTIVATE_THE_SELECTED_USER?');
        } else {
            $('#status-body').text('YOU_ARE_GOING_TO_DEACTIVATE_THE_SELECTED_USER?');
        }
        $('#change-status').modal('show');
    });

    $(document).on('click', '#confirm-status', function(e){
        e.preventDefault();
        $('#confirm-status').attr('disabled', true);
        updateInfo(globalElement);
        $('#change-status').modal('hide');
    });

    $('#change-status').on('hidden.bs.modal', function () {
        $('#confirm-status').attr('disabled', false);
        if (!updatedStatus) globalElement.prop('checked', (globalElement.attr('data-value')) ? true : false);
        globalElement = '';
        updatedStatus = '';
    });

    $(document).on('change', '.on-select', function(){
        $(this).closest('form').submit();
    });

    function updateInfo(element) {
        var user_info = element.attr('data-rel');
        // console.log(user_info);
        // return;
        var value_info = element.val();
        if (value_info == '') return false;
        var isActive = false;
        if (element.hasClass('make-inactive')) {
            isActive = true;
            value_info = element.is(":checked") ? 1 : null;
        }
        var selected = element.find('option:selected').text();
        $.ajax({
            async: false,
            dataType: 'json',
            url: projectBaseUrl + 'admin/users/ajax_update',
            type: 'post',
            data: {user_info : user_info, value_info : value_info},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    $.notify({
                        icon: 'glyphicon glyphicon-saved',
                        title: "SUCCESS:",
                        message: response.message
                    },{
                        type: 'success',
                        delay: 2000
                    });
                    if (isActive) {
                        updatedStatus = true;
                        if (value_info == null) {
                            element.closest('tr').addClass('inactive-row');
                        } else {
                            element.closest('tr').removeClass('inactive-row');
                        }
                    } else {
                        element.hide();
                        var toDisplay = (selected) ? selected : value_info;
                        element.prev().html(toDisplay + ' <i class="glyphicon pencil-small"></i>').show();
                        element.attr('data-value', value_info);
                        if (selected) {
                            updatedRole = true;
                        }
                    } 
                } else {
                    element.attr('data-value', element.attr('data-value'));
                    $.notify({
                        icon: 'glyphicon glyphicon-ban-circle',
                        title: "FAILED:",
                        message: response.message
                    },{
                        type: 'danger',
                        delay: 2000
                    });
                }
            }
        });
    }
})(jQuery);