<?php
/**
 * This will hide subscription levels on the registration form page
 * if their price is lower than the price of member's current
 * subscription level.
 */
function jp_hide_lower_cost_levels( $levels ) {

	if ( ! rcp_is_registration_page() || ! is_user_logged_in() ) {
		return $levels;
	}

	$existing_sub = rcp_get_subscription_id( wp_get_current_user()->ID );

	if ( empty( $existing_sub ) ) {
		return $levels;
	}

	foreach( $levels as $key => $level ) {
		if ( rcp_get_subscription_price( $level->id ) < rcp_get_subscription_price( $existing_sub ) ) {
			unset( $levels[$key] );
		}
	}

	return $levels;
}
add_filter( 'rcp_get_levels', 'jp_hide_lower_cost_levels' );