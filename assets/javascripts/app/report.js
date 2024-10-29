;(function($, window) {

	if ( window.pagenow != 'wp-care_page_apiki-wp-care-stats-checker' ) {
		return;
	}

	var url = window.location.href;

	if ( !url.match( 'care-verify' ) ) {
		return;
	}

	window.history.pushState(
		  window.history.state
		, null
		, window.location.href.replace( '\&care-verify=1', '' )
	);

})(jQuery, window);
