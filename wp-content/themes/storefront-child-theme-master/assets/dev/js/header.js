jQuery(window).scroll(function($) {
	const topHeader = document.querySelector("#top-header")
	var sc = jQuery(window).scrollTop()

	if (sc > 20) {
		topHeader.classList.add('hide')
	} 
	else {
		topHeader.classList.remove('hide')
	}
});