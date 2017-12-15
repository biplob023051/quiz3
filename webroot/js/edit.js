var debugVar;
var uploadObj;
var window_width = $(window).width();
setCookie("original_width", window_width, 1);
(function ($) {
    $(document).on('click', '#upload', function(){
        $(this).addClass('active');
        $('#from-web').removeClass('active');
        $('#web-panel').hide();
        $('#upload-panel').show();

        uploadObj = $("#fileuploader").html('').uploadFile({
            url:projectBaseUrl + 'upload/photo',
            fileName:"myfile",
            acceptFiles:"image/*",
            showPreview:true,
            multiple:false,
            previewHeight: "150px",
            previewWidth: "150px",
            showProgress:true,
            dragDropStr: "<span class='upload-drag-drop'>"+ lang_strings['drag_drop'] +"</span>",
            uploadStr: lang_strings['upload'],
            onSelect:function(files)
            {
                $('#fileuploader').hide();
                $('#upload').hide();
                $('#from-web').hide();
                return true; //to allow file submission.
            },
            onSuccess:function(files,data,xhr,pd)
            {
                var data = $.parseJSON(data);
                if (data.success) {
                    $('.ajax-file-upload-progress').hide();
                    // $('.ajax-file-upload-container').show();
                    $('.ajax-file-upload-statusbar').prepend('<button data-img="'+ data.filename +'" type="button" class="btn btn-default" id="file-delete-button" title="Delete image"><i class="glyphicon close"></i></button>');
                    $('#web-panel').find('input').val(projectBaseUrl + 'uploads/questions/' + data.filename);
                } else {
                    window.location.reload();
                }
                //$("#eventsmessage").html($("#eventsmessage").html()+"<br/>Success for: "+JSON.stringify(data));
                
            },
            onError: function(files,status,errMsg,pd)
            {
                $('#fileuploader').show();
                $('#upload').show();
                $('#from-web').show();
            }
        });
    });

    $(document).on('click', '#file-delete-button', function(e) {
        e.preventDefault();
        var image = $(this).attr('data-img');
        $('#fileuploader').show();
        $('#upload').show();
        $('#from-web').show();
        // $('.ajax-file-upload-container').hide();
        $('#web-panel').find('input').val('');
        $('.ajax-file-upload-statusbar').remove();
        $.post(projectBaseUrl + 'upload/delete_photo', {image:image}, function( data ) {});
        uploadObj.reset();
    });

    $(document).on('click', '#file-delete-existing', function(e) {
        e.preventDefault();
        var image = $(this).attr('data-img');
        $('#upload').show();
        $('#from-web').show();
        $('#web-panel').find('input').val('');
        $('#preview-panel').remove();
        $('#upload').trigger('click');
        //$.post(projectBaseUrl + 'upload/delete_photo', {image:image}, function( data ) {});
    });

    $(document).on('click', '#from-web', function(){
        $(this).addClass('active');
        $('#upload').removeClass('active');
        $('#web-panel').show();
        $('#upload-panel').hide();
    });

    var appData = $.parseJSON($("#app-data").text());
    var webQuizConfig = {
        "quizId": appData.quizId,
        "baseUrl": appData.baseUrl,
        "questionTypes": appData.questionTypes
    };

    webQuiz.init(webQuizConfig);
    
    if (initial) { // Add New Question only if its quiz create 
        webQuiz.addNewQuestion(true); // Show Question 
    } else if(no_question) {
        webQuiz.addNewQuestion(true); // Show Question 
    } else {
        webQuiz.addNewQuestion(); // Don't Show Question 
    }
    
    $(document).on('click', '#questions button.add-choice', function () {
        var questionId = $(this).parent().parent().parent().attr('id'),
                choiceContainer = $('#' + questionId).find("div.choices");

        webQuiz.addChoice(
            parseInt(questionId.substr(1, questionId.length - 1)),
            choiceContainer
        );
    });

    // biplob added for adding one more row on page load by default    
    $('#questions button.add-choice').trigger('click');

    $(document).on('change', '#questions select.choice-type-selector', function () {
        $('#QuestionExplanation').closest('.row').children().css({marginTop: '0px'});
        var element = $(this),
                questionId = element.attr('id');

        questionId = questionId.substr(3, questionId.length - 1);

        if ((questionId != -1) && (($(this).val() == 1) || ($(this).val() == 3))) {
            webQuiz.getExistingQuestionData(questionId, element.val());
        } else if ((questionId != -1) && (element.val() != 6) && (editing_q_type == element.val())) {
            webQuiz.getExistingQuestionDataRest(questionId, element.val());
        } else {
            webQuiz.changeChoiceType(
                parseInt(questionId),
                element.val(),
                $("#q" + questionId).find("button.add-choice")
                );
            // biplob added for adding one more row on select change
            if (($(this).val() == 1) || ($(this).val() == 3)) {
               $('#questions button.add-choice').trigger('click'); 
            }
            webQuiz.questionOptions($(this).val());
            webQuiz.additionalSettings(element.val());
        }
        
    });

    $(document).on('click', '#questions tr td div.preview-btn button.edit-question', function () {
        $('#QuestionExplanation').closest('.row').children().css({marginTop: '0px'});
        var blank_question = false;
        if ($('#QuestionText').val() == '') {
            blank_question = true;
        }

        var qidStr = $(this).attr('id'),
                questionId = parseInt($(this).attr('id').substr(6, qidStr.length - 1));
        // alert(qidStr);
        // alert(blank_question);
        if (blank_question == true) {
            webQuiz.setToPreview(
                webQuiz.currentEditQid,
                $("#q" + webQuiz.currentEditQid),
                function (question)
                {
                    webQuiz.setToEdit(
                            questionId,
                            $("#q" + questionId),
                            function (question)
                            {
                                var questionContainer = $("#q" + questionId);
                                questionContainer.find('select.choice-type-selector').val(question.value.QuestionType.id);

                                var isMultipleChoices = webQuiz
                                        .getQuestionType(question.value.QuestionType.id)
                                        .value.QuestionType.multiple_choices;

                                if (!isMultipleChoices)
                                    questionContainer.find("button.add-choice").hide();
                                if (question.value.QuestionType.id == 7 || question.value.QuestionType.id == 8) { // hide question text for youtube and image type question
                                     $('#QuestionText').parent().hide(); 
                                } else {
                                    $('#QuestionText').parent().show(); 
                                }

                                webQuiz.questionOptions(question.value.QuestionType.id); // show hide max_allowed

                                if (question.value.QuestionType.id == 6) { // if header type then hide explanation text
                                    $('#QuestionExplanation').closest('.row').hide();
                                } else {
                                    $('#QuestionExplanation').closest('.row').show();
                                }
                                
                                if (question.value.QuestionType.id == 7) { 
                                    // Change placeholder for explanation text if youtube
                                    $('#QuestionExplanation').attr('placeholder', lang_strings['youtube_exp_text']);
                                    if (window_width > 975) {
                                        $('#QuestionExplanation').closest('.row').children().css({marginTop: '-47px'});
                                    }
                                } else if (question.value.QuestionType.id == 8) { 
                                    // Change placeholder for explanation text if image
                                    $('#QuestionExplanation').attr('placeholder', lang_strings['image_exp_text']);
                                    if (window_width > 975) {
                                        $('#QuestionExplanation').closest('.row').children().css({marginTop: '-47px'});
                                    }
                                    $('#from-web').hide();
                                    $('#upload').hide();
                                } else {
                                    $('#QuestionExplanation').attr('placeholder', lang_strings['other_exp_text']);
                                }

                                // Set the editing question type
                                editing_q_type = question.value.QuestionType.id;
                            }
                    );
                    $("#q" + questionId).show();
                    webQuiz.lastEditQid = question.question_id;
                    webQuiz.currentEditQid = questionId;
                    webQuiz.choiceSortable();
                },
                'questions/setPreview/'
            );    
        } else {
            webQuiz.setToPreview(
                webQuiz.currentEditQid,
                $("#q" + webQuiz.currentEditQid),
                function (question)
                {
                    webQuiz.setToEdit(
                            questionId,
                            $("#q" + questionId),
                            function (question)
                            {
                                var questionContainer = $("#q" + questionId);
                                questionContainer.find('select.choice-type-selector').val(question.value.QuestionType.id);

                                var isMultipleChoices = webQuiz
                                        .getQuestionType(question.value.QuestionType.id)
                                        .value.QuestionType.multiple_choices;

                                if (!isMultipleChoices)
                                    questionContainer.find("button.add-choice").hide();
                                if (question.value.QuestionType.id == 7 || question.value.QuestionType.id == 8) { // hide question text for youtube and image type question
                                     $('#QuestionText').parent().hide(); 
                                } else {
                                    $('#QuestionText').parent().show(); 
                                }

                                webQuiz.questionOptions(question.value.QuestionType.id); // show hide max_allowed

                                if (question.value.QuestionType.id == 6) { // if header type then hide explanation text
                                    $('#QuestionExplanation').closest('.row').hide();
                                } else {
                                    $('#QuestionExplanation').closest('.row').show();
                                }
                                
                                if (question.value.QuestionType.id == 8) { 
                                    $('#QuestionExplanation').attr('placeholder', lang_strings['image_exp_text']);
                                    $('#QuestionExplanation').closest('.row').children().css({marginTop: '-47px'});
                                }
                            }
                    );
                    $("#q" + questionId).show();
                    webQuiz.lastEditQid = question.question_id;
                    webQuiz.currentEditQid = questionId;
                    webQuiz.choiceSortable();
                }
            );
        }
    });

    $(document).on('click', '.cancel-add', function(e) {
        $('#q-1').remove();
        webQuiz.addNewQuestion();
        $('#questions button.add-choice').trigger('click');
    });
    
    $(document).on('click', "#add-question", function (e) {
        if(!$('#q-1').is(':visible')) {
            $('#q-1 .cancel-add').show();
            $('#q-1').show();
            return;
        }
        var question_number = $('#questions tbody').children('tr:not(.others_type)').length;
        var questionTypeId = $('#questions select.choice-type-selector').val();
        // current tr
        var currentTr = $('#q-1');
        if ((questionTypeId != 7) && (questionTypeId != 8)) { // don't validate empty question for youtube or image
            if ($('#QuestionText').val() == '') {       
                var currentEditQid = $("#q" + webQuiz.currentEditQid),
                choiceContainer = currentEditQid.find("div.choices");
                if (questionTypeId == 6) {
                    webQuiz.showAlert(lang_strings['empty_header']);      
                } else {
                    webQuiz.showAlert(lang_strings['empty_question']);
                }
                
                return;        
            }
        }
        
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );

        if (validationError == false) {
            webQuiz.addNewQuestion(true); // Show Question 
            // delete new empty question for add new question
            if ((questionTypeId != 7) && (questionTypeId != 8)) { // don't validate empty question for youtube or image
                if ($('#QuestionText').val() == '') {
                    $("#q-1").remove();
                    // trigger click to add extra one choice more by default
                    $('#questions button.add-choice').trigger('click');
                    return;
                }
            }
            var response = webQuiz.setToPreview(webQuiz.lastEditQid, $("#q" + webQuiz.lastEditQid), 'test', 'questions/save/', question_number);    
            $('.cancel-add').show();
        }
        
    });

    $(document).on('click', '#questions tr td div.preview-btn button.delete-question', function () {
        var qidStr = $(this).attr('id'),
                questionId = parseInt($(this).attr('id').substr(8, qidStr.length - 1));
        webQuiz.deleteQuestion(questionId, $("#q" + questionId));
    });

    $(document).on('click', '#submit-quiz', function (e) {
        if ($('#QuestionText').val() == '') {
            $("#QuizEditForm").submit();
            return;
        }
        var questionTypeId = $('#questions select.choice-type-selector').val();
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );
        if (validationError == false) {
            webQuiz.setToPreview(
                webQuiz.currentEditQid,
                $("#q" + webQuiz.currentEditQid),
                function (question)
                {
                    webQuiz.lastEditQid = question.question_id;
                    //webQuiz.currentEditQid = questionId;
                   $("#QuizEditForm").submit();
                }
            );    
        }
        
    });

    $(document).on('click', '#questions button.remove-choice', function () {
        //alert('hi');
        var questionId = $(this).closest("tr").attr('id'),
                choice = $(this).attr('choice'),
                choiceContainer = $('#' + questionId).find("div.choices");
        // remove new additional choice from new question
        if (questionId == 'q-1') {
            $(this).closest('.well').parent().parent().remove();
            choiceContainer.append('<div class="row" style="display:none;"></div>');
            return;
        }

        // remove new additional choice from edit question
        if (questionId != 'q-1') {
            $(this).closest('.well').parent().parent().remove();
            choiceContainer.append('<div class="row" style="display:none;"></div>');
            return;
        }

        webQuiz.removeChoice(
                questionId.substr(1),
                choice,
                $(this)
                );
    });

    $(document).on('click', '#questions button.edit-done', function (e) {
        e.preventDefault();
        var questionTypeId = $('#questions select.choice-type-selector').val();
        
        if ((questionTypeId != 7) && (questionTypeId != 8)) { // don't validate empty question for youtube or image
            if ($('#QuestionText').val() == '') {
                var currentEditQid = $("#q" + webQuiz.currentEditQid),
                choiceContainer = currentEditQid.find("div.choices");
                if (questionTypeId == 6) {
                    webQuiz.showAlert(lang_strings['empty_header']);    
                } else {
                    webQuiz.showAlert(lang_strings['empty_question']);
                }
                return;
            }
        }
        
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );

        //var question_number = $(this).closest('tr').index()+1;
        var currentTr = $(this).closest('tr');

        var question_number = 1;
        $('#questions > tbody  > tr:not(.others_type)').each(function() {
            if ($(this).index() == currentTr.index()) {
                return false;
            } else {
                question_number++;
            }
        });

        if (validationError == false) {
            $('.edit-done').attr('disabled', true);
            webQuiz.addNewQuestion();
            var response = webQuiz.setToPreview(webQuiz.lastEditQid, $("#q" + webQuiz.lastEditQid), 'test', 'questions/save/', question_number);    
        }

    });

    // settings show hide
    $(document).on('click', '#show-settings', function () {
        $('.caret-down').toggle();
        $('.caret-right').toggle();
        $('.settings-options').toggle();
    });

    // All Subjects
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
    });
    // All Classes
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
    });


    // Return a helper with preserved width of cells
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $("#questions tbody").sortable({
        items: 'tr:not(#q-1)',
        tolerance: 'pointer',
        //revert: 'invalid',
        placeholder: 'well tile',
        forceHelperSize: true,
        helper: fixHelper,
        update: function( ) {
            webQuiz.reArrangeQuestionNumber();
        }
    });

    $(document).on('click', '#questions tr td div.preview-btn button.duplicate-question', function () {
        var qidStr = $(this).attr('id'),
                questionId = parseInt($(this).attr('id').substr(11, qidStr.length - 1));
        webQuiz.duplicateQuestion(questionId, $("#q" + questionId));
    });

    // Show sort button on question hover
    $('#questions').on('mouseenter', 'tr', function(e){
        $(this).find(".shorter-arrow").fadeIn("slow");
    });

    $('#questions').on('mouseleave', 'tr', function(e){
        $(this).find(".shorter-arrow").fadeOut("slow");
    });

    // Show sort button on choice hover
    //$('tr.EditQuestionBorder td form div.choices').on('mouseenter', '.make-sortable', function(e){
    $(document).on('mouseenter', '.make-sortable', function(e){
        $(this).find(".choice-arrow").fadeIn("slow");
    });

    $(document).on('mouseleave', '.make-sortable', function(e){
        $(this).find(".choice-arrow").fadeOut("slow");
    });

    $(document).on('click', '.alert', function(){
        $(this).fadeOut('show');
    });

    // Auto save quiz basic and settings 
    $("input.update-now").blur(function() {
        if (($(this).attr('name') == 'name') && ($(this).val() == '')) {
            return false;
        }
        if ($(this).val() == $(this).attr('data-current-value')) {
            return false;
        }
        updateQuiz($(this).attr('name'), $(this).val(), $(this));
    });

    $("input.update-now").keypress(function (e) {
        var key = e.which;
        if(key == 13)  {
            $(this).blur();
        }
    });

    $(document).on('click', '.settings-options input[type="checkbox"]', function(){
        var element = $(this), field, value;
        if (element.attr('name') == 'show_result' || element.attr('name') == 'anonymous') {
            field = element.attr('name');
            value = element.prop("checked") ? 1 : '';
        } else if (element.attr('name') == 'subjects[]') {
            field = 'subjects';
            value = formatSubjectClass(element, 'subjects');
        } else {
            field = 'classes';
            value = formatSubjectClass(element, 'classes');
        }
        updateQuiz(field, value);
    });
})(jQuery);

function formatSubjectClass(element, type) {
    if (element.val() == 0) {
        if (element.is(':checked')) {
            var result = $('input[type="checkbox"][name="'+type+'\\[\\]"]').map(function() { return this.value; }).get();        
        } else {
            var result = Array();
        }
    } else {
        var result = $('input[type="checkbox"][name="'+type+'\\[\\]"]:checked').map(function() { return this.value; }).get();
        if (result.length > 0 && result[0] == 0) {
            result.splice(0, 1);
        }
    }
    return result;
}

function updateQuiz(field, value, element) {
    $.ajax({
        // async: false,
        dataType: 'json',
        url: projectBaseUrl + 'quizzes/ajaxQuizUpdate/' + c_quiz_id,
        type: 'post',
        data: {field: field, value: value},
        success: function (response)
        {
            if (response.success && element) {
                element.attr('data-current-value', value);
                var originalBackgroundColor = element.css('background-color'),
                originalColor = element.css('color');
                element.animate({ 'background-color' : '#5cb85c', 'color' : 'white' });
                setTimeout(function(){
                  element.animate({ 'background-color' : originalBackgroundColor, 'color' : originalColor });
                }, 1000);
            }
        }
    });
}

// On resize window
$(window).resize(function () {
    // waitForFinalEvent(function(){
    //     checkSize();
    // }, 500, "some unique string");
    checkSize();
});

var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (timers[uniqueId]) {
      clearTimeout (timers[uniqueId]);
    }
    timers[uniqueId] = setTimeout(callback, ms);
  };
})();

function checkSize() {
    var win = $(window);
    console.log('dynamic width', win.width());
    console.log('static width', screen.width);
    if ((win.width() < 975) || (screen.width > 1920) && (win.width() < 977)) {
        $('#QuestionExplanation').closest('.row').children().css({marginTop: '0px'});
    } else {
        $('#QuestionExplanation').closest('.row').children().css({marginTop: '-47px'});
    }
}

// javascript cookie functions
function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname+"="+cvalue+"; "+expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}