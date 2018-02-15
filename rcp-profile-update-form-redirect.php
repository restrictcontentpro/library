<?php
/**
 * Plugin Name: Restrict Content Pro - Profile Update Redirect
 * Description: Changes the URL members are redirected to after updating their profiles.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * This will change the URL members are directed to
 * after updating their profiles.
 */
function jp_redirect_profile_update_form() {

	global $rcp_options;

	if ( empty( $rcp_options['edit_profile'] ) || ! is_page( $rcp_options['edit_profile'] ) ) {
		return;
	}

	add_filter( 'rcp_current_url', function( $url ) {
		return 'http://rcp-dev.dev/test-redirect'; // change this to the desired URL
	});

}
add_action( 'template_redirect', 'jp_redirect_profile_update_form' );