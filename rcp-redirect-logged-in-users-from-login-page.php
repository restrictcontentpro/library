<?php
/**
 * Plugin Name: Restrict Content Pro - Redirect Logged In Users from Login Page
 * Description: If a logged in user tries to visit a page containing the [login_form] shortcode they will be redirected to their membership page.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

function ag_rcp_redirect_logged_in_users_from_login_page() {

	global $post, $rcp_options;

	// Bail if user is not logged in.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Bail if not a single post/page.
	if ( ! is_singular() || ! is_object( $post ) ) {
		return;
	}

	// Bail if post/page doesn't contain [login_form] shortcode.
	if ( ! has_shortcode( $post->post_content, 'login_form' ) ) {
		return;
	}

	$redirect_page_id = $rcp_options['account_page'];
	$redirect_url     = ! empty( $redirect_page_id ) ? get_permalink( $redirect_page_id ) : home_url();

	wp_safe_redirect( esc_url_raw( $redirect_url ) );
	exit;

}
add_action( 'template_redirect', 'ag_rcp_redirect_logged_in_users_from_login_page' );