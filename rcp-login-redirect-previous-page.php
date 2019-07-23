<?php
/**
 * Plugin Name: Restrict Content Pro - Redirect to Previous Page After Login
 * Description: Changes the default login redirect URL to the previous page instead of the current page.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Changes the default login redirect URL to the previous page instead of the current page.
 *
 * @param array  $output    The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 * @param string $shortcode The shortcode name.
 *
 * @return array Modified default shortcode attributes.
 */
function ag_rcp_login_form_redirect_to_previous( $output, $pairs, $atts, $shortcode ) {

	$referer = wp_get_referer();

	if ( ! empty( $referer ) ) {
		$output['redirect'] = $referer;
	}

	return $output;

}
add_filter( 'shortcode_atts_login_form', 'ag_rcp_login_form_redirect_to_previous', 10, 4 );