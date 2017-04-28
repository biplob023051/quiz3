$(document).on('click', '#preview', function() {
    $.ajax({
        data: {random_id : $(this).attr('random-id'), present : 1},
        type: 'post',
        url: projectBaseUrl + 'quizzes/ajax_preview',
        dataType: 'html',
        success: function (data)
        {
            $('#preview-quiz').html(data).modal('show');
        }
    });
});