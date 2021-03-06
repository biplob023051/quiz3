function checkRow(row) {
    var found = false;
    row.find('input.update-score').each(function(){
        if($(this).attr('value') === undefined)
            found = true;
            return;
    });
    
    if(found) {
        row.addClass('warning');
    } else {
        row.removeClass('warning');
    }
}

var globalOffline = {};
var checkOffline = setInterval(checkOfflineUsers, 10700);
function checkOfflineUsers() {
    if (!$.isEmptyObject(globalOffline)) {
        clearInterval(checkOffline);
        var postedData = globalOffline;
        globalOffline = {};
        $.ajax({
            async: false,
            dataType: 'JSON',
            type: "POST",
            url: projectBaseUrl + 'students/checkComplete',
            data: {std_ids : postedData},
            success: function(response) {
                if (response.ids.length > 0) {
                    $.each(response.ids, function(index, std) {
                        var std_id = std.id;
                        $progress = $("tr#student-" + std_id).find('.progress[data-value="100"]');
                        if ($progress.length > 0) {
                            $progress.attr('data-value', '101');
                            $progress.find('.progress-bar').css({backgroundColor: 'green'});
                        }
                        delete postedData[std_id];
                    })
                } 
                if (!$.isEmptyObject(postedData)) {
                    for (var student in postedData) { globalOffline[student] = postedData[student]; }
                }
                $(".table").trigger("update");
                checkOffline = setInterval(checkOfflineUsers, 10700);
            }
        });
    }
}

var interval;

function getUpdated() {
    var quizId = $("#quizId").text();
    $.ajax({
        async: false,
        type: "POST",
        url: projectBaseUrl + 'quizzes/ajax_latest',
        data: {quizId:quizId},
        async: true,
        success: function(data) {
            var old_data = $.parseJSON($("#prev_data").html());
            var new_data = $.parseJSON(data);
            if (JSON.stringify(old_data.studentIds) == JSON.stringify(new_data.studentIds)) {
                // check offline students
                var offline = false;
                $.grep(old_data.onlineStds, function(el) {
                    if ($.inArray(el, new_data.onlineStds) == -1) {
                        offline = true;
                        $('tr#student-'+el).find('.online').remove();
                        $('tr#student-'+el).find('.question-serial').addClass('small-padding-true');
                        globalOffline[el] = el;
                    } 
                });
                if (offline) {
                    $("#prev_data").html(data);
                }

            } else {
                $("#prev_data").html(data);
                // New code for fixing if student goes offline
                $.grep(old_data.onlineStds, function(el) {
                    if ($.inArray(el, new_data.onlineStds) == -1) {
                        $('tr#student-'+el).find('.online').remove();
                        $('tr#student-'+el).find('.question-serial').addClass('small-padding-true');
                        globalOffline[el] = el;
                    } 
                });
                if (new_data.studentIds.length == 0) {
                    return false;
                }
                // End of new codes
                clearInterval(interval);
                var openTab = getCookie("tabInfo");
                $.ajax({
                    async: false,
                    dataType: 'JSON',
                    type: "POST",
                    url: projectBaseUrl + 'quizzes/ajax_update',
                    data: {quizId:quizId, old_data:old_data.studentIds, new_data:new_data.studentIds},
                    async: true,
                    success: function(data) {
                        $.each(data, function(index, value) {
                            updateIndividulaStudent(value);

                        })
                        interval = setInterval(getUpdated, 5000);
                    }
                });
            }
        }
    });
}

function updateIndividulaStudent(student_id) {
    if ($("tr#student-" + student_id).length == 0) {
        var sl = parseInt($('#answer-table table tbody tr').length)+1;
    } else {
        $("tr#student-" + student_id).find(':input').attr('disabled', true);
        $("tr#student-" + student_id).find('.delete-answer').hide();
        $("tr#student-" + student_id).find('.ajax-loader').show();
        var sl = parseInt($("tr#student-" + student_id).find('.question-serial').text());
    }
    var quizId = parseInt($("#quizId").text());
    $.ajax({
        async: false,
        dataType: 'html',
        type: "POST",
        url: projectBaseUrl + 'quizzes/ajax_student_update',
        data: {student_id:student_id, sl:sl, quiz_id: quizId},
        async: true,
        success: function(data) {
            if ($("tr#student-" + student_id).length == 0) {
                // New stduent
                var html = '<tr id="student-'+student_id+'">';
                html+=data;
                html+='</tr>';
                $('#answer-table table tbody').append(html);
            } else {
                $("tr#student-" + student_id).html(data);
                $("tr#student-" + student_id).find('.ajax-loader').hide();
                $("tr#student-" + student_id).find('.delete-answer').show();
                $("tr#student-" + student_id).find(':input').attr('disabled', false);
            }
            testFunc();
            $(".table").trigger("update");
            $("#fixTable").tableHeadFixer({"head" : true, "left" : 2});
        }
    });
}

var headers = { 
    2: { 
        sorter:'submitted' 
    },
    4: { 
        sorter:'points' 
    },
    5: { 
        sorter:'progressbar' 
    } 
};

if (typeof headerCol.STAR !== 'undefined') {
    $.each(headerCol.STAR, function(index, item) {
        // Add in headers
        headers[item] = {sorter : 'star_' + item};    
        // Add parser
        $.tablesorter.addParser({ 
            id: 'star_' + item, 
            is: function(s) { 
                return false; 
            }, 
            format: function(s, t, node) {
                return $(node).find('.score').text();
            }, 
            type: "numeric" 
        });

    });
}

if (typeof headerCol.MCMC !== 'undefined') {
    $.each(headerCol.MCMC, function(index, item) {
        // Add in headers
        headers[item] = {sorter : 'mcmc_' + item};    
        // Add parser
        $.tablesorter.addParser({ 
            id: 'mcmc_' + item, 
            is: function(s) { 
                return false; 
            }, 
            format: function(s, t, node) {
                var score = 0;
                $(node).find('.score').each(function(){
                    score = score + parseInt($(this).text());
                });
                return score;
            }, 
            type: "numeric" 
        });

    });
}

if (typeof headerCol.STMR !== 'undefined') {
    $.each(headerCol.STMR, function(index, item) {
        // Add in headers
        headers[item] = {sorter : 'stmr_' + item};    
        // Add parser
        $.tablesorter.addParser({ 
            id: 'stmr_' + item, 
            is: function(s) { 
                return false; 
            }, 
            format: function(s, t, node) {
                var iv = $(node).find('input').val();
                return iv ? iv : 0;
            }, 
            type: "numeric" 
        });

    });
}

if (typeof headerCol.ESSAY !== 'undefined') {
    $.each(headerCol.ESSAY, function(index, item) {
        // Add in headers
        headers[item] = {sorter : 'essay_' + item};    
        // Add parser
        $.tablesorter.addParser({ 
            id: 'essay_' + item, 
            is: function(s) { 
                return false; 
            }, 
            format: function(s, t, node) {
                return $(node).find('.score').text();
            }, 
            type: "numeric" 
        });

    });
}

$.tablesorter.addParser({ 
    // set a unique id 
    id: 'submitted', 
    is: function(s) { 
        // return false so this parser is not auto detected 
        return false; 
    }, 
    format: function(s) { 
        // format your data for normalization
        if (!s) {
            return 0;
        }
        var dateTimeParts = s.split(', '),
        timeParts = dateTimeParts[1].split(':'),
        dateParts = dateTimeParts[0].split('.'),
        date;
        date = new Date(dateParts[2], parseInt(dateParts[1], 10) - 1, dateParts[0], timeParts[0], timeParts[1]);
        return date.getTime();
    }, 
    // set type, either numeric or text 
    type: 'numeric' 
});

$.tablesorter.addParser({ 
    // set a unique id 
    id: 'points', 
    is: function(s) { 
        // return false so this parser is not auto detected 
        return false; 
    }, 
    format: function(s) { 
        // format your data for normalization
        if (!s) {
            return 0;
        }
        var pointParts = s.split('/');
        return pointParts[0];
    }, 
    // set type, either numeric or text 
    type: 'numeric' 
}); 

$.tablesorter.addParser({ 
    id: "progressbar", 
    is: function(s) { 
        return false; 
    }, 
    format: function(s, t, node) {
        return $(node).attr('data-value');
    }, 
    type: "numeric" 
}); 

$(document).ready(function(){ 
    var windowHeight = parseInt($(window).height()) - (parseInt($('.navbar').height())+parseInt($('.page-header').height())+parseInt($('#answer-table-filter').height())+190+parseInt(maintHeight));
    $('#answer-table tbody').css({'height' : windowHeight});
    // end of height calculation
    $("#fixTable").tableHeadFixer({"head" : true, "left" : 2});
    interval = setInterval(getUpdated, 5000);
    testFunc();
    $(".table").tablesorter({ 
        headers: headers 
    }); 
   
}); 


(function ($) {
    $('#maintenance-alert').css({width: $(window).width()});
    $("body").css("overflow", "hidden");
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

    $(document).on('click', '#answer-table-overview', function () {
        $("#ajax-message").hide();
        // tab information insert into cookie to keep tracking
        setCookie("tabInfo", "answer-table-overview", 1);
        if($(this).hasClass('active'))
            return;
        $(this).addClass('active');
        $('#answer-table-show').removeClass('active');
        $('.question-collapse').hide();
        // $("th, td").each(function() {
        //     $(this).addClass("overview-section");
        // });

        $('.progress-section').addClass("overview-section");
    });

    $(document).on('click', '#answer-table-show', function () {
        $("#ajax-message").hide();
        // tab information insert into cookie to keep tracking
        setCookie("tabInfo", "answer-table-show", 1);
        if($(this).hasClass('active'))
            return;
        $(this).addClass('active');
        $('#answer-table-overview').removeClass('active');
        $('.question-collapse').show();
        $('.progress-section').removeClass("overview-section");
    });

    $(document).on('change', '#answer-table-filter select', function () {
        $('form#answer-table-filter').submit();
    });

    $("#answer-table table").find('tr').each(function(){
        checkRow($(this));
    });
    
    // get tab information
    var currentTab = getCookie("tabInfo");
    if (currentTab == 'answer-table-show') {
        $('#answer-table-show').trigger('click');
    } else {
        $('#answer-table-overview').trigger('click');
    }

    // essay pop up modal
    $(document).on('click', 'button.read-essay', function () {
        $(this).next().next().modal('show');
    });

    // delete unwanted answer
    $(document).on('click', 'button.delete-answer', function () {
        var infoModal = $('#confirm-delete');
        var std_id = $(this).attr('id');
        var std_online = $(this).closest('tr').find('.online').length;
        $.ajax({
            async: false,
            dataType: 'json',
            url: projectBaseUrl + 'students/confirmDeleteStudent',
            type: 'post',
            data: {'student_id': std_id},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    var str = '';
                    if (std_online == 1) {
                        str += lang_strings['online_warning'];
                    }
                    str += lang_strings['remove_question'] + response.student_full_name + ' (' + response.student_class + lang_strings['with_points'] + response.student_score + '?';
                    infoModal.find('.modal-body').html(str);
                    infoModal.find('.modal-footer button#confirmed').attr('value', response.student_id);
                    infoModal.modal('show');
                } else {
                    alert('Something went wrong!!! Please try again later');
                }
            }
        });
    });

    $(document).on('click', 'button#confirmed', function () {
       var std_id = $(this).attr('value');
       var infoModal = $('#confirm-delete');
       $.ajax({
            async: false,
            dataType: 'json',
            url: projectBaseUrl + 'students/deleteStudent',
            type: 'post',
            data: {'student_id': std_id},
            success: function (response)
            {
                infoModal.modal('hide');
                if (response.success || response.success === "true")
                {
                    $("#ajax-message").removeClass('alert-danger');
                    $("#ajax-message").addClass('alert-success');
                    $("button#" + std_id).closest('tr').remove();
                    var re_index = 1;
                    $("#answer-table > table > tbody  > tr").each(function() { 
                        $(this).find('.question-serial').html(re_index);
                        re_index++;
                    });
                    $(".table").trigger("update");
                } else {
                    $("#ajax-message").removeClass('alert-success');
                    $("#ajax-message").addClass('alert-danger');
                }
                $("#ajax-message").show();
                $("#ajax-message").html(response.message);
            }
        });
    });

    

    $(document).on('click', 'button#print', function (e) {
        e.preventDefault();
        var quizId = parseInt($("#quizId").text());
        $('#print_div').html('');
        window.frames["print_frame"].document.body.innerHTML='';
        $.ajax({
            async: false,
            dataType: 'html',
            type: "POST",
            url: projectBaseUrl + 'quizzes/ajax_print_answer',
            data: {quizId:quizId},
            async: true,
            success: function(data) {
                $('#print_div').html(data);
                //alert(print_div);
                printDiv();
                // var WindowObject = window.open('about:blank');
                // WindowObject.document.writeln(data);
                // WindowObject.document.close();
                // WindowObject.focus();
                // WindowObject.print();
                // WindowObject.close();
                // var WindowObject = window.open("", "PrintWindow", "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
                // WindowObject.document.writeln(data);
                // WindowObject.document.close();
                // WindowObject.focus();
                // WindowObject.print();
                // WindowObject.close();
            }
        });
    });
    printDivCSS = new String ('<link rel="stylesheet" href="'+projectBaseUrl+'css/print_test.css" type="text/css" media="print">');
    function printDiv() {
        window.frames["print_frame"].document.body.innerHTML=$('#print_div').html();
        window.frames["print_frame"].window.focus();
        window.frames["print_frame"].window.print();
    }

    $(document).on('click', '.automatic_rating_box', function () {
        $('.automatic_rating').each(function(){
            $(this).hide();
            $(this).prev().show();
        });
        $(this).hide();
        $(this).next().show();
        var elem = $(this);
        setTimeout(function(){
            elem.next().hide();
            elem.show();
        }, 7000);
    });

    $(document).on('click', '.std-info', function () {
        $('.update-std').each(function(){
            $(this).hide();
            $(this).prev().show();
        });
        $(this).hide();
        $(this).next().show();
        var elem = $(this);
        setTimeout(function(){
            elem.next().hide();
            elem.show();
        }, 10000);
    });

    $('input.update-std').donetyping(function(){
        updateStudentInfo($(this));
    });

    $("input.update-std").keypress(function (e) {
        var key = e.which;
        if(key == 13)  {
            $(this).blur();
        }
    });

    $('#answer-table input:not(.update-std)').keypress(function (e) {
        var key = e.which;
        if(key == 13)  {
            $(this).blur();
        }
    });
    
})(jQuery);

function updateStudentInfo($this) {
    var std_info = $this.attr('data-rel');
    var value_info = $this.val();
    clearInterval(interval);
    $.ajax({
        async: false,
        dataType: 'json',
        url: projectBaseUrl + 'students/ajax_std_update',
        type: 'post',
        data: {'std_info' : std_info, 'value_info' : value_info},
        success: function (response)
        {
            console.log(std_info);
            if (response.success || response.success === "true")
            {
                $this.hide();
                if (response.changetext == '') {
                    $this.prev().show();
                } else {
                    $this.prev().html(response.changetext + ' <i class="glyphicon pencil-small"></i>').show();
                }
                $(".table").trigger("update"); 
                // var sorting = (std_info.indexOf('class') !== -1) ? [[3,0]] : [[1,0]];
                // $(".table").trigger("sorton",[sorting]);
            } else {
                alert(response.message);
            }
            clearInterval(interval);
            interval = setInterval(getUpdated, 5000);
        }
    });
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

function checkCookie() {
    var user=getCookie("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
       user = prompt("Please enter your name:","");
       if (user != "" && user != null) {
           setCookie("username", user, 30);
       }
    }
}

function testFunc() {
    $('#answer-table input:not(.update-std)').donetyping(function(){
        $("#ajax-message").hide();
        var current_score = parseFloat($(this).attr("current-score"));
        if (($(this).val() == '' || $(this).val() == null) && isNaN(current_score)) {
            return false;    
        }

        var marks = $(this).val();
        if(isNaN(marks)) {
            marks = 'null';
        }

        var max = parseFloat($(this).attr("max")); 

        if (marks < 0) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").html(lang_strings['positive_number']);
            $("#ajax-message").show();
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks == current_score) {
            return false;
        } else if (marks > max) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").html(lang_strings['more_point_1'] + max + lang_strings['more_point_2']);
            $("#ajax-message").show();
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } 

        $(this).attr("current-score", marks);

        var std_id = parseInt($(this).attr("name"));
        var q_id = parseInt($(this).attr("question"));
        var inputField = $(this);


        clearInterval(interval);

        $.ajax({
            async: false,
            dataType: 'json',
            url: projectBaseUrl + 'students/update_score',
            type: 'post',
            data: {'id': q_id, 'student_id': std_id, 'score': marks, 'current_score' : current_score, 'max' : max},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    $("#studentscr2-" + std_id).text(response.score);
                    $("#studentscr1-" + std_id).text(response.score);
                    if (inputField.hasClass('automatic_rating')) { // if automatic question update
                        inputField.hide();
                        inputField.prev().html('<span class="score automatic">' + marks + '</span> <i class="glyphicon pencil-small"></i>').show();
                    } else {
                        var originalBackgroundColor = inputField.css('background-color'),
                        originalColor = inputField.css('color');
                        inputField.css({ 'background-color' : 'green', 'color' : 'white' });
                        setTimeout(function(){
                          inputField.css({ 'background-color' : originalBackgroundColor, 'color' : originalColor });
                        }, 1000);
                    }
                    if (inputField.parents('.read-essay').first().length > 0) {
                        if (marks == 'null') {
                            inputField.parents('.read-essay').first().prev().children().hide();
                        } else {
                            inputField.parents('.read-essay').first().prev().children().show();
                        }
                        inputField.parents('.read-essay').first().prev().children().text(marks);
                        inputField.parents('.read-essay').modal('hide');
                    }
                    $(".table").trigger("update"); 
                    // var sorting = [[5,0]]; 
                    // $(".table").trigger("sorton",[sorting]); 
                } else {
                    alert('Something went wrong, try again later');
                }
                clearInterval(interval);
                interval = setInterval(getUpdated, 5000);
            }
        });
    });
}


// For read more
var showChar = 40;
var ellipsestext = "";
var moretext = "...";
var lesstext = "...";
$('.more').each(function() {
    var content = $(this).html();
    if(content.length > showChar) {
        var c = content.substr(0, showChar);
        var h = content.substr(showChar-1, content.length - showChar);
        var html = c + '<span class="moreellipses">' + ellipsestext+ '</span><span class="morecontent"><span style="display: none;">' + h + '</span>&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
        $(this).html(html);
    }
});

$(document).on('click', '.morelink', function(e){
    e.preventDefault();
    if($(this).hasClass("less")) {
        $(this).removeClass("less");
        $(this).html(moretext);
    } else {
        $(this).addClass("less");
        $(this).html(lesstext);
    }
    $(this).parent().prev().toggle();
    $(this).prev().toggle();
    return false;
});
// End of read more

// On resize window
$(window).resize(function () {
    var windowHeight = parseInt($(window).height()) - (parseInt($('.navbar').height())+parseInt($('.page-header').height())+parseInt($('#answer-table-filter').height())+190+parseInt(maintHeight));
    $('#answer-table tbody').css({'height' : windowHeight});
    $('#maintenance-alert').css({width: $(window).width()});
});