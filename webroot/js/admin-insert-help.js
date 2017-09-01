(function ($) {
	tinymce.init({selector:'textarea'});
	$("#fileuploader").html('').uploadFile({
        url:projectBaseUrl + 'upload/photo',
        fileName:"myfile",
        acceptFiles:"image/*",
        showPreview:true,
        multiple:false,
        previewHeight: "100px",
        previewWidth: "100px",
        onSuccess:function(files,data,xhr,pd)
        {
            var data = $.parseJSON(data);
            if (data.success) {
                $('input[name=temp_photo]').val(data.filename);
            } else {
                window.location.reload();
            }
        },
    });
})(jQuery);