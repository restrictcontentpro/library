<?php
/**
 * Plugin Name: Restrict Content Pro - Generic Login Error
 * Description: Makes the login form error more generic to prevent revealing whether or not the account exists.
 * Version: 1.0
 * Author: Restrict Content Pro team
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display one single generic error message.
 *
 * @param string $html     Formatted HTML code.
 * @param array  $errors   Array of error codes.
 * @param string $error_id Form ID for these errors.
 *
 * @return string
 */
function ag_rcp_login_error_message( $html, $errors, $error_id ) {
	if ( 'login' == $error_id && ! empty( $errors ) ) {
		$error_message = __( 'Invalid login' ); // Edit as desired.

		$html = '<div class="rcp_message error" role="list"><p class="rcp_error" role="listitem">' . $error_message . '</p></div>';
	}

	return $html;
}
add_filter( 'rcp_error_messages_html', 'ag_rcp_login_error_message', 10, 3 );