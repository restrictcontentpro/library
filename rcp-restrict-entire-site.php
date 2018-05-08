<?php
/**
 * Plugin Name: Restrict Content Pro - Restrict Entire Site
 * Description: Restricts access to the entire site except for designated RCP pages such as registration, login, etc.
 * Version: 1.0
 * Author: Restrict Content Pro team
 * License: GPL2
 */

/**
 * Redirect non-members to the "Redirect Page". This is set in Restrict > Settings > Misc.
 *
 * @since 1.0
 * @return void
 */
function ag_rcp_restrict_entire_site() {

	// If the current user is an admin, bail.
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	// If current user has an active subscription, bail.
	$allowed_statuses = array( 'free', 'active', 'cancelled' );
	if ( rcp_get_subscription_id() && ! rcp_is_expired() && in_array( rcp_get_status(), $allowed_statuses ) ) {
		return;
	}

	// Otherwise we need to redirect them.

	global $rcp_options;

	// Inactive users will be able to access these pages.
	$whitelisted_post_ids = array(
		$rcp_options['registration_page'],
		$rcp_options['redirect'],
		$rcp_options['account_page'],
		$rcp_options['edit_profile'],
		$rcp_options['update_card'],
		$rcp_options['login_redirect'],
		$rcp_options['redirect_from_premium']
	);

	// Whether or not inactive users should be able to access the homepage.
	$whitelist_home = false; // Change this to TRUE if you want to allow access to the homepage.

	if ( ! empty( $rcp_options['redirect_from_premium'] ) ) {
		$redirect_url = get_permalink( $rcp_options['redirect_from_premium'] );
	} else {
		$redirect_url = home_url();

		$whitelist_home = true;
	}

	// This is a whitelisted ID - bail.
	if ( is_singular() && in_array( get_the_ID(), $whitelisted_post_ids ) ) {
		return;
	}

	// Allow access to homepage if this is whitelisted.
	if ( $whitelist_home && is_front_page() ) {
		return;
	}

	// Otherwise redirect.
	wp_safe_redirect( $redirect_url );

	exit;

}

add_action( 'template_redirect', 'ag_rcp_restrict_entire_site' );