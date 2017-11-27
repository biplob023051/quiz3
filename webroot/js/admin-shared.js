$(document).on('change', '#quiz-is-approve', function() {
    $('#quiz-filter').submit();
});

$(document).on('click', '.decline-quiz', function(e) {
	e.preventDefault();
	var parent_id = $(this).attr('parent-id'),
        quiz_id = $(this).attr('quiz-id'),
        quiz_name = $(this).attr('quiz-name'),
        body_html = '';
        body_html += '<input type="hidden" name="parent_quiz_id" value="'+parent_id+'">';
        body_html += '<input type="hidden" name="id" value="'+quiz_id+'">';
        body_html += '<input type="hidden" name="is_approve" value="2">';
        body_html += '<div class="form-group required">';
        body_html += '<label for="comment" class="sr-only">Name</label>';
        body_html += '<textarea name="comment" class="form-control" placeholder="'+lang_strings['decline_reason']+'" id="comment"></textarea>';
        body_html += '</div>';

    $('#modal_form').attr('action', projectBaseUrl + 'admin/quizzes/manage_share');
    $('#confirmation-header').html(lang_strings['decline_question'] + ' ' + quiz_name + '?');
    $('#confirmation-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">'+lang_strings['cancel']+'</button><button type="submit" class="btn btn-success">'+lang_strings['submit']+'</button>');
    $('#confirmation-body').html(body_html);
    $('#confirmation').modal('show');
});


$(document).on('click', '.approve-quiz', function(e) {
    e.preventDefault();
    var quiz_id = $(this).attr('quiz-id'),
        quiz_name = $(this).attr('quiz-name'),
        body_html = '';
        body_html += '<input type="hidden" name="id" value="'+quiz_id+'">';
        body_html += '<input type="hidden" name="is_approve" value="1">';
        // body_html += '<div class="form-group required">';
        // body_html += '<label for="comment" class="sr-only">Name</label>';
        // body_html += '<textarea name="data[Quiz][comment]" class="form-control" placeholder="'+lang_strings['decline_reason']+'" id="comment"></textarea>';
        // body_html += '</div>';
        body_html += lang_strings['approve_body'];

    $('#modal_form').attr('action', projectBaseUrl + 'admin/quizzes/manage_share');
    $('#confirmation-header').html(lang_strings['approve_question'] + ' ' + quiz_name + '?');
    $('#confirmation-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">'+lang_strings['cancel']+'</button><button type="submit" class="btn btn-success">'+lang_strings['submit']+'</button>');
    $('#confirmation-body').html(body_html);
    $('#confirmation').modal('show');
});