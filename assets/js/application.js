/**
 *   All js files will be automatically loaded.
 */
$(document).ready(function() {
	
	// Comment if you don't want to use Pjax
	$(document).pjax('a', 'body', {fragment: '#pjax-response'});
});