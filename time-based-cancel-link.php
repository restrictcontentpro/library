<?php
/**
 * Plugin Name: Restrict Content Pro - Time-Based Cancel Link
 * Description: Prevents the "Cancel your subscription" link from showing until the member has been subscribed to his or her current subscription for at least 3 months.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Prevents the "Cancel your subscription" link from showing
 * until the member has been subscribed to his or her current
 * subscription for 3 months.
 */
function jp_rcp_member_can_cancel( $ret, $user_id ) {

	global $rcp_options;

	// Only do this on the Account Page
	if ( empty( $rcp_options['account_page'] ) || ! is_page( $rcp_options['account_page'] ) ) {
		return $ret;
	}

	// Return early if other conditions aren't already met.
	if ( ! $ret ) {
		return false;
	}

	$timezone = get_option( 'timezone_string' );
	$timezone = ! empty( $timezone ) ? $timezone : 'UTC';

	$member   = new RCP_Member( $user_id );

	$cancel_date = new \DateTime( $member->get_joined_date(), new \DateTimeZone( $timezone ) );
	$cancel_date->modify( '+3 months'); // change this if you want a different time period

	$now = new \DateTime( 'now', new \DateTimeZone( $timezone ) );

	if ( $ret && ( $now < $cancel_date ) ) {
		$ret = false;
	}

	return $ret;
}
add_filter( 'rcp_member_can_cancel', 'jp_rcp_member_can_cancel', 100, 2 );