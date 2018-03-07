<?php
/**
 * Plugin Name: Restrict Content Pro - Disable Site-Wide Gateway Scripts
 * Description: Ensures gateway scripts only load on Restrict Content Pro pages. This primarily prevents stripe.js from loading across the whole site.
 * Version: 1.0
 * Author: Restrict Content Pro team
 * License: GPL2
 */

/**
 * Disables gateway scripts like Stripe.js if the current page is not an RCP page.
 *
 * @return void
 */
function ag_rcp_maybe_disable_gateway_scripts() {

	// Bail if RCP isn't installed.
	if ( ! function_exists( 'rcp_is_registration_page' ) ) {
		return;
	}

	// Allow on registration page.
	if ( rcp_is_registration_page() ) {
		return;
	}

	/*
	 * Also allow on other RCP pages. `update_card` is the only one we really need the scripts loaded on, so the others
	 * can be removed from the array if desired.
	 */
	global $rcp_options, $post;
	$pages = array(
		$rcp_options['account_page'],
		$rcp_options['update_card'],
		$rcp_options['edit_profile']
	);

	if ( is_object( $post ) && in_array( $post->ID, $pages ) ) {
		return;
	}

	remove_action( 'wp_enqueue_scripts', 'rcp_load_gateway_scripts', 100 );

}
add_action( 'wp_enqueue_scripts', 'ag_rcp_maybe_disable_gateway_scripts', -1 );