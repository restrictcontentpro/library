<?php
/**
 * Plugin Name: Restrict Content Pro - Time-Based Cancel Link
 * Description: Prevents the "Cancel your subscription" link from showing until the member has been subscribed to his or her current subscription for at least 3 months.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Prevents the "Cancel your membership" link from showing until the membership has been
 * active for at least 3 months.
 *
 * @param bool           $can_cancel    Whether or not the membership can be cancelled.
 * @param int            $membership_id ID of the membership being checked.
 * @param RCP_Membership $membership    Membership object.
 *
 * @return bool
 * @throws Exception
 */
function jp_rcp_membership_can_cancel( $can_cancel, $membership_id, $membership ) {

	global $rcp_options;

	// Only do this on the Account Page
	if ( empty( $rcp_options['account_page'] ) || ! is_page( $rcp_options['account_page'] ) ) {
		return $can_cancel;
	}

	// Return early if other conditions aren't already met.
	if ( ! $can_cancel ) {
		return false;
	}

	$timezone = get_option( 'timezone_string' );
	$timezone = ! empty( $timezone ) ? $timezone : 'UTC';

	$cancel_date = new \DateTime( $membership->get_created_date( false ), new \DateTimeZone( $timezone ) );
	$cancel_date->modify( '+3 months'); // change this if you want a different time period

	$now = new \DateTime( 'now', new \DateTimeZone( $timezone ) );

	if ( $can_cancel && ( $now < $cancel_date ) ) {
		$can_cancel = false;
	}

	return $can_cancel;

}
add_filter( 'rcp_membership_can_cancel', 'jp_rcp_membership_can_cancel', 100, 3 );