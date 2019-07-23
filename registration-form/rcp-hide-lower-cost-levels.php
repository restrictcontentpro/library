<?php
/**
 * Plugin Name: Restrict Content Pro - Hide Lower Cost Levels
 * Description: Hides membership levels on the registration form page if their price is lower than the price of the member's current level.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * This will hide membership levels on the registration form page
 * if their price is lower than the price of member's current
 * membership level.
 *
 * If you have multiple memberships enabled you may want to only use the `ag_rcp_prevent_downgrades()` function
 * below. This code will prevent customers from signing up for a *second* membership that has a lower price,
 * whereas the `ag_rcp_prevent_downgrades()` function just prevents downgrades on existing memberships.
 *
 * @param array $levels
 *
 * @return array
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

/**
 * Remove downgrades from the upgrade path.
 *
 * @param array          $levels        Array of membership level objects.
 * @param int            $membership_id ID of the membership.
 * @param RCP_Membership $membership    Membership object.
 *
 * @return array
 */
function ag_rcp_prevent_downgrades( $levels, $membership_id, $membership ) {

	$current_price = $membership->get_recurring_amount();
	if ( empty( $current_price ) ) {
		$current_price = $membership->get_initial_amount();
	}

	foreach ( $levels as $key => $level ) {
		if ( rcp_get_subscription_price( $level->id ) < $current_price ) {
			unset( $levels[$key] );
		}
	}

	return $levels;

}
add_filter( 'rcp_get_membership_upgrade_paths', 'ag_rcp_prevent_downgrades', 10, 3 );