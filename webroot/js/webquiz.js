// Requires: jQuery, Handlebars
var webQuiz = {
    quizId: null,
    questionData: [],
    containerDOM: null,
    choiceTpl: {},
    cPreviewTpl: {},
    qPreviewTpl: {},
    questionTpl: null,
    choiceTplCache: {},
    currentEditQid: null,
    lastEditQid: null,
    questionTypes: null,
    baseUrl: '',
    init: function (config)
    {
        if (config.questionTypes === undefined)
            throw new Exception("Must define question types!");

        this.questionTypes = config.questionTypes;
        this.previewCallback = config.previewCallback;
        this.quizId = config.quizId;
        this.baseUrl = config.baseUrl;

        this.questionTpl = Handlebars.compile(
                $("#question-edit-template").html()
                );

        this.qPreviewTpl = Handlebars.compile(
                $("#question-preview-template").html()
                );

        $.each(this.questionTypes, function (index, value)
        {
            webQuiz.questionTypes[index].QuestionType.id = parseInt(webQuiz.questionTypes[index].QuestionType.id);

            var tplName = value.QuestionType.template_name;

            webQuiz.choiceTpl[tplName] = Handlebars.compile(
                    $("#choice-" + tplName + "-edit-template").html()
                    );

            webQuiz.cPreviewTpl[tplName] = Handlebars.compile(
                    $("#choice-" + tplName + "-preview-template").html()
                    );

        });

        Handlebars.registerHelper('choice', function (items, config)
        {
            var output = [],
                    root = config.data.root,
                    tplName = root.QuestionType.template_name,
                    tpl;

            if (root.preview === true)
                tpl = webQuiz.cPreviewTpl[tplName];
            else
                tpl = webQuiz.choiceTpl[tplName];

            // If question only has single choice
            if (items.length === undefined)
            {
                output.push(tpl(items));
            }
            else
            {
                for (var i = 0; i < items.length; i++)
                {
                    // @TODO Find better way to inject parent template data
                    // @TODO Is it possible to cache a choice?

                    items[i].id = i;
                    items[i].question_id = root.question_id;

                    output.push(tpl(items[i]));
                }
            }

            return output.join('');
        });

        this.containerDOM = $("#questions tbody");
    },
    addNewQuestion: function (QDisplay)
    {
        this.lastEditQid = this.currentEditQid;
        this.currentEditQid = -1;

        QDisplay = typeof QDisplay !== 'undefined' ? QDisplay : false;

        // @TODO: Find better way to set a default question
        if (QDisplay == true) {
            this.addQuestion({
                id: -1,
                text: '',
                explanation: '',
                Choice: [{}],
                QuestionType: webQuiz.questionTypes[0].QuestionType,
                isNew: true,
                preview: false,
                QDisplay: QDisplay
            });
        } else {
            this.addQuestion({
                id: -1,
                text: '',
                explanation: '',
                Choice: [{}],
                QuestionType: webQuiz.questionTypes[0].QuestionType,
                isNew: true,
                preview: false
            });
        }
        // call sortable function
        webQuiz.choiceSortable();
        return true;
    },
    addQuestion: function (question)
    {
        var html = this.questionTpl(question);
        this.questionData.push(question);
        this.containerDOM.append(html);
        return true;
    },
    deleteQuestion: function (questionId, questionContainer, onSuccessCallback)
    {
        $.ajax({
            data: {id: questionId},
            url: this.baseUrl + 'questions/delete',
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    var question = webQuiz.getQuestion(questionId);
//                    if (question !== null)
//                        delete webQuiz.questionData[question.index];
                    questionContainer.remove();

                    // change all the question number as well
                    //var current_question_number = questionContainer.closest('tr').index()+1;
                    var re_index = 1;
                    $("#questions > tbody  > tr:not('.others_type')").each(function() {
                        $(this).find('.question_number').html(re_index);
                        re_index++;
                    });
                    if (myChoices[questionId]) delete myChoices[questionId]; 
                }
                else
                {
                    alert('Error! More detailed error is soon to be implemented\n\n');
                }

                if (onSuccessCallback !== undefined)
                    onSuccessCallback(response);
            }
        });
    },
    duplicateQuestion: function (questionId, questionContainer)
    {
        $.ajax({
            data: {id: questionId, name: $('#name').val(), description: $('#description').val() },
            url: this.baseUrl + 'questions/duplicate',
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    window.location.reload();
                }
                else
                {
                    alert(response.message);
                }
            }
        });
    },
    getQuestion: function (questionId)
    {
        questionId = parseInt(questionId);
        var question = null;
        $.each(this.questionData, function (index, value) {

            if (value.id === questionId)
            {
                question = {'index': index, 'value': value};
                return;
            }
        });

        return question;
    },
    getQuestionType: function (questionTypeId)
    {
        questionTypeId = parseInt(questionTypeId);
        var questionType = null;
        $.each(this.questionTypes, function (index, value) {

            if (value.QuestionType.id === questionTypeId)
            {
                questionType = {'index': index, 'value': value};
                return;
            }
        });

        return questionType;
    },
    setToPreview: function (questionId, questionContainer, onSuccessCallback, ajax_url, question_number)
    {

        questionId = parseInt(questionId);

        var question = webQuiz.getQuestion(questionId),
                _questionData = questionContainer.find('form').serializeJSON();
        
        if (question.value.preview === true)
            return;

        _questionData.data.isNew = question.value.isNew;
        _questionData.data.Question.quiz_id = webQuiz.quizId;

        if (_questionData.data.isNew === true)
            delete _questionData['question_id'];

        ajax_url = typeof ajax_url !== 'undefined' ? ajax_url : 'questions/save/';
        question_number = typeof question_number !== 'undefined' ? question_number : 1;

        $.ajax({
            data: _questionData.data,
            url: webQuiz.baseUrl + ajax_url + questionId,
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    var tmp;
                    if (response.Question.case_sensitive == 0) {
                        delete response.Question.case_sensitive;
                    }
                    tmp = response.Question;
                    tmp.Choice = response.Choice;
                    tmp.QuestionType = webQuiz.getQuestionType(tmp.question_type_id).value.QuestionType;
                    tmp.id = parseInt(response.Question.id);
                    tmp.isNew = false;
                    tmp.preview = true;

                    if ((tmp.question_type_id == 2) && (response.Question.case_sensitive == 1)) {
                        tmp.Choice[0].case_sensitive = true;
                    }

                    webQuiz.questionData[question.index] = tmp;
                    
                    if((questionId == -1) && $("#q-1").hasClass('warn')) {
                        tmp.warn_message = true;
                    } else if ($("#q" + questionId).hasClass('warn')) {
                        tmp.warn_message = true;
                    }

                    // question numbering
                    tmp.question_number = question_number;

                    // for others type add related class with question title
                    // this logic for question number
                    // this logic for table#question tr class
                    // if class exist then no question number
                    if (tmp.question_type_id == 6) {
                        tmp.relatedClass = 'header';
                        tmp.showQuestionText = true; // Question text false for youtube and image url
                    } else if(tmp.question_type_id == 7) {
                        tmp.relatedClass = 'youtube';
                    } else if(tmp.question_type_id == 8) {
                        tmp.relatedClass = 'image-url';
                    } else { // if regular questions
                        // do nothing right now
                    }

                    if ((tmp.question_type_id != 3)) delete tmp.max_allowed;

                    // remove last question if not save
                    // for question edit view
                    if(typeof(response.dummy) != "undefined" && response.dummy !== null) {
                        if($("#q-1").length >= 0) {
                            $("#q-1").remove();
                        }
                    }

                    $(webQuiz.qPreviewTpl(tmp)).insertAfter(questionContainer);
                    questionContainer.remove();

                    if (onSuccessCallback !== undefined) {
                        if (onSuccessCallback == 'test') {
                            $('#questions button.add-choice').trigger('click');
                        } else {
                            onSuccessCallback(tmp);
                        }
                    }
                    // Update existing questions
                    if ((tmp.id != -1) && ((tmp.question_type_id != 6))) {
                        myChoices[tmp.id] = tmp.Choice;
                    }
                    var re_index = 1;
                    $("#questions > tbody  > tr:not('.others_type')").each(function() { 
                        if ($(this).attr('id') != 'q-1') { // check if new question tr id
                            $(this).find('.question_number').html(re_index);
                            re_index++;
                        }
                    });
                }
                else
                {
                    if (response.message != 'undefined' || response.message != null ) {
                        alert(response.message);
                        $('.edit-done').attr('disabled', false);
                    } else {
                        alert('Error! More detailed error is soon to be implemented\n\n');
                        window.location.reload();
                    }
                }
            }
        });
    },
    setToEdit: function (questionId, questionContainer, callback)
    {
        var question = this.getQuestion(questionId);

        // Try to lazy load question data from html
        //@TODO: Set the correct choice selector
        if (question === null)
        {
            var _question = $.parseJSON(questionContainer.find("script").text().trim());

            if (_question === null)
                return;

            _question.preview = false;
            _question.isNew = false;
            _question.id = parseInt(_question.id);

            webQuiz.questionData.push(_question);

            question = {
                index: this.questionData.length - 1,
                value: _question
            };

        }
        else
        {
            // Return if question is not exists or already in preview mode
            if (question.value.preview === false)
                return;
            this.questionData[question.index].preview = false;
        }

        var html = webQuiz.questionTpl(question.value);
        $(html).insertAfter(questionContainer);

        questionContainer.remove();

        if (callback !== undefined)
            callback(question);

    },
    addChoice: function (questionId, choicesContainer)
    {
        var question = this.getQuestion(questionId);
        if (question === null)
            return;

        var question_value = question.value;

        if (question_value.QuestionType.multiple_choices === false) {
            var temp_template = question_value.QuestionType.template_name;
            question_value.QuestionType.template_name = 'multiple_one';
        }
        //return false;

        var html = this.choiceTpl[question_value.QuestionType.template_name]({
                id : choicesContainer.children().length
            });

        choicesContainer.append(html);

        if (question_value.QuestionType.multiple_choices === false) {
            question_value.QuestionType.template_name = temp_template;
        }
    },
    removeChoice: function (question_id, choice, containerDOM)
    {
        $.ajax({
            data: {question_id : question_id, choice : choice},
            url: webQuiz.baseUrl + 'questions/removeChoice',
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    containerDOM.closest('.choice-' + choice).remove();
                }
            }
        });    
    },
    dataValidation: function (questionTypeId) {
        var validationError = false;

        var currentEditQid = $("#q" + webQuiz.currentEditQid),
        choiceContainer = currentEditQid.find("div.choices");
        
        if ((questionTypeId == 1) || (questionTypeId == 3)) {
            // choice validation
            validationError = webQuiz.choiceValidation(
                choiceContainer, questionTypeId
            );

            // point validation for one correct
            if ((validationError == false) && (questionTypeId == 1)) {
                validationError = webQuiz.singlePointValidation(
                    choiceContainer
                );
            }

            // point validation for multi correct
            if ((validationError == false) && (questionTypeId == 3)) {
                validationError = webQuiz.multiPointValidation(
                    choiceContainer
                );
            }
        } else if (questionTypeId == 4) {
            validationError = webQuiz.manualRatingValidation(
                choiceContainer
            );
        } else if (questionTypeId == 5) {
            validationError = webQuiz.essayValidation(
                choiceContainer
            );
        } else if (questionTypeId == 2) {
            validationError = webQuiz.automaticRatingValidation(
                choiceContainer
            );
        } else if (questionTypeId == 7) {
            validationError = webQuiz.youtubeValidation(
                choiceContainer
            );
        } else if (questionTypeId == 8) {
            validationError = webQuiz.imageUrlValidation(
                choiceContainer
            );
        } else { // header type
            // do nothing
        } 

        return validationError;
    },
    youtubeValidation: function (choiceContainer) 
    {
        var validationError = false;
        // Youtube url validation
        choiceContainer.find(':input[type="text"]').each(function(){
            if ($(this).val() == '') {
                $('.alert-danger').remove();
                validationError = true;
                choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['youtube_url'] + '</div>');
            }
            
        });
        return validationError;
    },
    imageUrlValidation: function (choiceContainer) 
    {
        var validationError = false;
        // Youtube url validation
        choiceContainer.find(':input[type="text"]').each(function(){
            if ($(this).val() == '') {
                $('.alert-danger').remove();
                validationError = true;
                choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['image_url'] + '</div>');
            }
            
        });
        return validationError;
    },
    automaticRatingValidation: function (choiceContainer) 
    {
        var validationError = false;

        // correct answer validation
        choiceContainer.find(':input[type="text"]').each(function(){
            if ($(this).val() == '') {
                $('.alert-danger').remove();
                validationError = true;
                choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['correct_answer'] + '</div>');
            }
            
        });

        // point validation
        if (validationError == false) {
            choiceContainer.find(':input[type="number"]').each(function(){
                if ($(this).val() > 0) {
                    validationError = false;
                    return false;
                } else {
                    validationError = true;
                }
            });
            if (validationError == true) {
                validationError = false;
                $('.alert-danger').remove();
                var currentEditQid = $("#q" + webQuiz.currentEditQid);
                $(currentEditQid.selector).addClass('warn');
            }
        }
        return validationError;
    },
    essayValidation: function (choiceContainer) 
    {
        var validationError = false;
        choiceContainer.find(':input[type="text"]').each(function(){
            if ($(this).val() == '') {
                $('.alert-danger').remove();
                var currentEditQid = $("#q" + webQuiz.currentEditQid);
                $(currentEditQid.selector).addClass('warn');
            }
            
        });
        return validationError;
    },
    manualRatingValidation: function (choiceContainer) 
    {
        var validationError = false;
        choiceContainer.find(':input[type="number"]').each(function(){
            if ($(this).val() > 0) {
                validationError = false;
                return false;
            } else {
                validationError = true;
            }
        });
        if (validationError == true) {
            validationError = false;
            $('.alert-danger').remove();
            var currentEditQid = $("#q" + webQuiz.currentEditQid);
            $(currentEditQid.selector).addClass('warn');
        }
        return validationError;
    },
    choiceValidation: function (choiceContainer, questionTypeId)
    {
        var minLength = 2;
        if ((questionTypeId == 3) && ($('#QuestionMaxAllowed').val() > 2)) {
            minLength = $('#QuestionMaxAllowed').val();
        }
        if (choiceContainer.find(':input[type="text"]').length < minLength) {
            $('.alert-danger').remove();
            choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['no_choice_1'] + ' ' + minLength + ' ' + lang_strings['no_choice_2'] + '</div>');
            return;   
        }
        var choiceArray = new Array();
        var validationError = false;   
        choiceContainer.find(':input[type="text"]').each(function(){
            // same choice not permit
            if ($(this).val() == '') {
                $('.alert-danger').remove();
                validationError = true;
                choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['same_choice'] + '</div>');
            } else {
                if (jQuery.inArray($(this).val(),choiceArray) == -1){
                    choiceArray.push($(this).val());
                } else {
                    $('.alert-danger').remove();
                    validationError = true;
                    choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['same_choice'] + '</div>');
                }
            }
            
        });
        return validationError;
    },
    singlePointValidation: function (choiceContainer)
    {
        var validationError = false;
        choiceContainer.find(':input[type="number"]').each(function(){
            if ($(this).val() > 0) {
                validationError = false;
                return false;
            } else {
                validationError = true;
            }
        });
        if (validationError == true) {
            validationError = false;
            $('.alert-danger').remove();
            var currentEditQid = $("#q" + webQuiz.currentEditQid);
            $(currentEditQid.selector).addClass('warn');
        }
        return validationError;
    },
    multiPointValidation: function (choiceContainer)
    {
        var validationError = false;
        var count = 0;
        choiceContainer.find(':input[type="number"]').each(function(){
            if ($(this).val() > 0) {
                count = count+1;
            }
        });
        if (count == 0) {
            if ($('.alert-danger').length){
                $('.alert-danger').remove();
            }
            var currentEditQid = $("#q" + webQuiz.currentEditQid);
            $(currentEditQid.selector).addClass('warn');
        }

        return validationError;
    },
    changeChoiceType: function (questionId, questionTypeId, addChoiceBtnDOM)
    {
        var questionType = webQuiz.getQuestionType(questionTypeId).value.QuestionType,
                tplName = questionType.template_name;
        
        if (webQuiz.choiceTpl[tplName] === undefined)
            return;

        if (questionType.multiple_choices) {
            // If the tplName is multiple, enable the add button choice
            addChoiceBtnDOM.show();
        } else {
            addChoiceBtnDOM.hide();
        }
        
        // @TODO Not sure cache a question's choice is the best idea
        if (this.choiceTplCache[tplName + questionId] === undefined) {
            this.choiceTplCache[tplName + questionId] = webQuiz.choiceTpl[tplName]();
        }
        
        this.containerDOM.find("#q" + questionId + " div.choices").html(this.choiceTplCache[tplName + questionId]);

        $.each(this.questionData, function (index, value) {

            if (value.id === questionId) {
                webQuiz.questionData[index].QuestionType = questionType;
                webQuiz.questionData[index].Choice = [];    
                return;
            }
        });

    },
    getExistingQuestionData: function (questionId, questionTypeId)
    {
        var questionType = webQuiz.getQuestionType(questionTypeId).value.QuestionType,
                tplName = questionType.template_name, existingChoice = '',
                template, data, existing_options = {};
        if (!myChoices[questionId]) {
            existing_options[0] = {};
            existing_options[1] = {};
        } else {
            if ((editing_q_type != 1) && (editing_q_type != 3)) {
                console.log(editing_q_type)
                existing_options[0] = {};
                existing_options[1] = {};
            } else {
                existing_options = myChoices[questionId];
            }
        }
        $.each(existing_options, function (key, val) {
            data = {
                id: key,
                text: val.text,
                points: val.points,
                weight: val.weight
            };
            template = Handlebars.compile(
                  $("#choice-" + tplName + "-edit-template").html()
                    );
            existingChoice += template(data);
        });
        this.containerDOM.find("#q" + questionId + " div.choices").html(existingChoice);
        this.questionOptions(questionTypeId);
        $('#QuestionText').parent().show();
        
        $("#q" + questionId).find("button.add-choice").show();
        this.additionalSettings(questionTypeId);

    },
    getExistingQuestionDataRest: function (questionId, questionTypeId)
    {
        var questionType = webQuiz.getQuestionType(questionTypeId).value.QuestionType,
                tplName = questionType.template_name, existingChoice = '',
                template, data, existing_options = {};
        if (editing_q_type == questionTypeId) {
            existing_options = myChoices[questionId];
            $.each(existing_options, function (key, val) {
                data = {
                    id: key,
                    text: val.text,
                    points: val.points,
                    weight: val.weight,
                    case_sensitive: val.case_sensitive
                };
                template = Handlebars.compile(
                      $("#choice-" + tplName + "-edit-template").html()
                        );
                existingChoice += template(data);
            });
        } else {
            template = Handlebars.compile(
                  $("#choice-" + tplName + "-edit-template").html()
                    );
            existingChoice += template();
        }
        this.containerDOM.find("#q" + questionId + " div.choices").html(existingChoice);
        this.questionOptions(questionTypeId);
        this.additionalSettings(questionTypeId);
    },
    additionalSettings: function (questionTypeId)
    {
        if (questionTypeId == 6) { // hide explanation input for header type question
            $('#QuestionText').attr('placeholder', lang_strings['header_q_title']); // change placeholder
            $('#QuestionExplanation').closest('.row').hide(); 
        } else {
            $('#QuestionText').attr('placeholder', lang_strings['other_q_title']); // change placeholder
            $('#QuestionExplanation').closest('.row').show();
        }

        if (questionTypeId == 7) { 
            // Change placeholder for explanation text if youtube
            $('#QuestionExplanation').attr('placeholder', lang_strings['youtube_exp_text']);
            $('#QuestionExplanation').closest('.row').children().css({marginTop: '-47px'});
        } else if (questionTypeId == 8) { 
            // Change placeholder for explanation text if image
            $('#QuestionExplanation').attr('placeholder', lang_strings['image_exp_text']);
            $('#QuestionExplanation').closest('.row').children().css({marginTop: '-47px'});
        } else {
            $('#QuestionExplanation').attr('placeholder', lang_strings['other_exp_text']);
        }

        if ((questionTypeId == 7) || (questionTypeId == 8)) { // hide question for youtube or image type question
           $('#QuestionText').parent().hide(); 
        } else {
            $('#QuestionText').parent().show(); 
        }
    },
    choiceSortable: function ()
    {
        $(".choices").sortable({
            tolerance: 'pointer',
            revert: 'invalid',
            placeholder: 'row well placeholder tile',
            forceHelperSize: true,
            update: function( ) {
                webQuiz.changeChoiceWeightValue();
            }
        });
    },
    changeChoiceWeightValue: function ()
    {
        if ( $( "#is_sort" ).length ) { // add a input type to the form that sorting is true
            // do nothing
        } else {
            // add the input type
            $("form#QuestionEditForm").append('<input type="hidden" id="is_sort" name="data[is_sort]" value="1" >');
        }
        var key = $(".number").length;
        $(".number").each(function() {
            var choice_no = $(this).children().attr('id').match(/\d+/);
            $('#Choice' + choice_no + 'Weight').val(key);
            key--;
        });
    },
    reArrangeQuestionNumber: function ()
    {
        // for question numbering on front end
        // skip others type question
        var re_index = 1;
        $("#questions > tbody  > tr:not('.others_type')").each(function() { 
            if ($(this).attr('id') != 'q-1') { // check if new question tr id
                $(this).find('.question_number').html(re_index);
                re_index++;
            }
        });

        // for getting questions id array
        var question_ids = [];
        $("#questions > tbody  > tr").each(function() { 
            if ($(this).attr('id') != 'q-1') { // check if new question tr id
                question_ids.push(parseInt($(this).attr('id').match(/\d+/)));
            }
        });

        $.ajax({
            data: {question_ids: question_ids},
            url: this.baseUrl + 'questions/ajax_sort',
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    // do nothing
                }
                else
                {
                    alert('Something went wrong, please try again later\n\n');
                }
            }
        });
    },
    questionOptions: function(question_type_id) {
        // if multiple choice many correct show max allowed input field
        // otherwise it will be hidden always
        (question_type_id == 3) ? $('#max_allowed').show() : $('#max_allowed').hide();
        (question_type_id == 8) ? $('#image-section').show() : $('#image-section').hide();
    }
};