(function ($) {
    $('#maintenance-alert').css({width: $(window).width()});
	
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


    $("input.update-user").blur(function() {
        updateInfo($(this));
    });

    $("input.update-user").keypress(function (e) {
        var key = e.which;
        if(key == 13)  {
            $(this).blur();
        }
    }); 

    var updatedStatus;
    $(document).on('change', '.make-inactive', function(){
        globalElement = $(this);
        if ($(this).is(":checked")) {
            $('#status-body').text(lang_strings['status_active_body']);
        } else {
            $('#status-body').text(lang_strings['status_inactive_body']);
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
                        title: lang_strings['success'],
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
                        title: lang_strings['failed'],
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