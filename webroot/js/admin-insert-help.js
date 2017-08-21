(function ($) {
	tinymce.init({selector:'textarea'});
	jQuery('#select-0').fineUploader({
		request: {
			endpoint: projectBaseUrl + 'upload/ajax_video/' + video_id
		},
		text: {
			uploadButton: lang_strings['upload_button']
		},
		validation: {
			allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
			sizeLimit: 10 * 1024 * 1024
		},
		multiple: false,
		dropZone: true
	}).on('complete', function(event, id, fileName, response) {
		$('#HelpPhoto').val(response.filename);
		$('#item-avatar').attr('src', response.avatar);
	});
})(jQuery);