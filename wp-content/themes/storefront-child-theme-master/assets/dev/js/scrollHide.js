jQuery(document).ready(function($) {
	if (screen.width < 993) {
		jQuery(window).scroll(function($) {
			const topHeader = document.querySelector("#top-header")
			var sc = jQuery(window).scrollTop()
		
			if (sc > 60) {
				topHeader.classList.add('hide')
			} 
			else {
				topHeader.classList.remove('hide')
			}
		});
	}	
});