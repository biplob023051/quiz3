$(document).on('click', '.my-language', function(e) {
	e.preventDefault();
	$.post(projectBaseUrl + 'users/switchLanguage',
	{
        lang: $(this).attr('data-value')
    },
    function(data, status){
    	window.location.reload();
    });
});