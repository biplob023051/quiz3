// Require Bootstrap.js

(function ($) {
    var subject_list = [];
    var class_list = [];
    var page_no = 1;

    // settings show hide
    $(document).on('click', '#show-settings', function () {
        $('.caret-down').toggle();
        $('.caret-right').toggle();
        $('.settings-options').toggle();
    });

    // Subjects
    $(document).on('change', '#subjects-0', function() { 
        $(".subjects").find('input').prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
    });
    //.subjects change 
    $(document).on('change', '.subjects input', function() { 
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(false == $(this).prop("checked")){ //if this item is unchecked
            $("#subjects-0").prop('checked', false); //change "select all" checked status to false
        }
        //check "select all" if all checkbox items are checked
        if ($('.subjects input:checked').length == ($('.subjects input').length-1) ){
            $("#subjects-0").prop('checked', true);
        }
        // Get each checked
        loadQuizzes();
    });
    // 
    $(document).on('change', '#classes-0', function() {
        $(".classes").find('input').prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
    });
    //.classes change 
    $(document).on('change', '.classes input', function() { 
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(false == $(this).prop("checked")){ //if this item is unchecked
            $("#classes-0").prop('checked', false); //change "select all" checked status to false
        }
        //check "select all" if all checkbox items are checked
        if ($('.classes input:checked').length == ($('.classes input').length-1) ){
            $("#classes-0").prop('checked', true);
        }
        // Get each checked
        loadQuizzes();
    });

    $(document).on('change', '#selectAll', function() {
        $(".chkselect").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
    });
    //.classes change 
    $(document).on('change', '.chkselect', function() { 
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(false == $(this).prop("checked")){ //if this item is unchecked
            $("#selectAll").prop('checked', false); //change "select all" checked status to false
        }
        //check "select all" if all checkbox items are checked
        if ($('.chkselect:checked').length == ($('.chkselect').length) ){
            $("#selectAll").prop('checked', true);
        }
    });

    // pagination
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        // alert($(this).text());
        if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) {
            return false;
        }
        var res = $(this).attr('href').split("page=");
        $.ajax({
            data: {page_no : res[1], subject_list : subject_list, class_list : class_list},
            type: 'post',
            url: projectBaseUrl + 'quizzes/test_link',
            dataType: 'html',
            success: function (data)
            {
                $('#pagination_content').html(data);
            }
        });
    });

    // name-sort
    $(document).on('click', '#name-sort a, #created-sort a', function (e) {
        e.preventDefault();
        var order = $(this).attr('data-rel');

        if ($(this).parent().attr('id') == 'name-sort') {
            var order_field = 'name';
        } 

        if ($(this).parent().attr('id') == 'created-sort') {
            var order_field = 'created';
        } 

        // alert(order_field);
        // return;
        // alert($(this).text());
        $.ajax({
            data: {order_type : order, order_field : order_field, page_no : page_no, subject_list : subject_list, class_list : class_list},
            type: 'post',
            url: projectBaseUrl + 'quizzes/test_link',
            dataType: 'html',
            success: function (data)
            {
                $('#pagination_content').html(data);

            }
        });
    });


    $(document).on('click', '.view-quiz', function() {
        // $(this).addClass('remove-view').removeClass('view-quiz');
        var quiz_id = $(this).attr('quiz-id');
        $.ajax({
            data: {quiz_id : quiz_id},
            type: 'post',
            url: projectBaseUrl + 'quizzes/ajax_preview',
            dataType: 'html',
            success: function (data)
            {
                $('#preview-quiz').html(data).modal('show');
                // $('#preview-quiz').css({'background-color' : '#ffffff'});
            }
        });
    });

    // import-quiz
    $(document).on('click', '.import-quiz' , function(e){
        e.preventDefault();
        var quiz_id = $(this).attr('quiz-id');
        importQuiz(quiz_id, $(this));
    });

    $(document).on('click', '.close', function(e){
        $(this).parent().parent().hide();
    });

    // multiple quiz import
    $(document).on('click', '.multiple-import-quiz' , function(e) {
        var quiz_id = [];
        $('.chkselect:checked').each(function() {
            quiz_id.push($(this).attr('data-id'));
        });
        if (quiz_id.length < 1) {
            $("#alert-box").html('<div class="alert alert-danger"><span class="close">&times;</span>'+lang_strings['check_select']+'</div>').show();
            setTimeout(function() {
                $("#alert-box").fadeOut(3000);
            },3000);
            return false;
        }
        importQuiz(quiz_id);
    });

    function importQuiz(quiz_id, element) {
        $.ajax({
            data: {quiz_id : quiz_id},
            type: 'post',
            url: projectBaseUrl + 'quizzes/ajax_import',
            dataType: 'json',
            success: function (response)
            {
                if (response.result) {
                    if (typeof element !== 'undefined') { // Single
                        element.attr('disabled', true);
                        element.closest('tr').find('.chkselect').attr('disabled', true);
                        element.closest('tr').find('.text-center').css({'color' : '#ddd'});
                        element.closest('tr').find('.chkselect').prop('checked', false);
                    } else { // Multiple
                        $('.chkselect:checked').each(function() {
                            $(this).prop('checked', false);
                            $(this).attr('disabled', true);
                            $(this).closest('tr').find('.text-center').css({'color' : '#ddd'});
                            $(this).closest('tr').find('.import-quiz').attr('disabled', true);
                        });
                    }

                    $("#alert-box").html('<div class="alert alert-success"><span class="close">&times;</span>'+lang_strings['import_success']+'</div>').show();
                    setTimeout(function() {
                        $("#alert-box").fadeOut(3000);
                    },3000);
                    var json = response.Quiz;
                    var html = '';
                    $.each(json, function(key, val) {
                        html += '<tr class="activeQuiz">';
                        html +=  '<td style="vertical-align:middle">';
                        html +=  '<div style="width: 40%; float: left">';
                        html +=  '<a href="'+projectBaseUrl+'quizzes/edit/'+val.id+'" class="quiz-name">'+val.name+'</a></div>';
                        html +=  '<div style="width: 60%; float: left">';
                        html +=  '<a href="'+projectBaseUrl+'quizzes/present/'+val.id+'">Give test!</a>';                                                                        
                        html +=  '<mark><a href="'+projectBaseUrl+'quizzes/table/'+val.id+'">Answers (0)</a></mark>';
                        html +=  '</div>';               
                        html +=  '</td>';
                        html +=  '<td align="right">';
                        html +=  '<ul class="nav navbar-nav navbar-right no-margin">';
                        html +=  '<li class="dropdown">';
                        html +=  '<a href="#" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="false">Actions <b class="caret"></b></a>';
                        html +=  '<ul class="dropdown-menu" role="menu">';
                        html +=  '<li><button type="button" class="btn btn-success btn-sm share-quiz" quiz-id="'+val.id+'" quiz-name="'+val.name+'" title="Share quiz"><i class="glyphicon glyphicon-share"></i> Share quiz</button></li>';
                        html +=  '<li><button type="button" class="btn btn-danger btn-sm delete-quiz" quiz-id="'+val.id+'" title="Remove quiz"><i class="glyphicon trash"></i> Remove quiz</button></li>';
                        html +=  '<li><button type="button" class="btn btn-default btn-sm active-quiz" status="1" id="'+val.id+'" title="Archive quiz"><i class="glyphicon archive"></i> Archive quiz</button></li>';
                        html +=  '<li><button type="button" class="btn btn-success btn-sm duplicate-quiz" quiz-id="'+val.id+'" title="Duplicate quiz"><i class="glyphicon duplicate"></i> Duplicate quiz</button></li>';
                        html +=  '</ul></li></ul></td></tr>'; 
                    });
                    if ($('#demo-data').length > 0) {
                        $('#demo-data').closest('.row').remove();
                        $('#user-quizzes').prepend('<table class="table"><tbody id="quiz-list">' + html + '</tbody></table');
                    } else {
                        $('#quiz-list').append(html);
                    }
                 } else {
                    $("#alert-box").html('<div class="alert alert-danger"><span class="close">&times;</span>'+response.message+'</div>').show();
                    setTimeout(function() {
                        $("#alert-box").fadeOut(3000);
                    },2000);
                 }
            }
        });
    }

    function loadQuizzes() {
        subject_list = [];
        class_list = [];
        $('.subjects input').each(function () {
            if (this.checked) {
                subject_list.push($(this).val());
            }
        });
        $('.classes input').each(function () {
            if (this.checked) {
                class_list.push($(this).val());
            }
        });
        $.ajax({
            data: {subject_list : subject_list, class_list : class_list},
            type: 'post',
            url: projectBaseUrl + 'quizzes/test_link',
            dataType: 'html',
            success: function (data)
            {
                $('#pagination_content').html(data);
            }
        });
    }

})(jQuery);