<?php
/**
 * Plugin Name: Restrict Content Pro - Add Expiration Grace Period
 * Description: Adds a 2-day grace period to the expiration cron job query.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Adds a 2-day grace period to the expiration cron job query.
 *
 * @param array $query_args
 *
 * @return array
 */
function ag_rcp_add_expiration_cron_grace_period( $query_args ) {

	$query_args['expiration_date_query']['before'] = date( 'Y-m-d H:i:s', strtotime( '-2 days', current_time( 'timestamp' ) ) );

	return $query_args;

}

add_filter( 'rcp_check_for_expired_memberships_query_args', 'ag_rcp_add_expiration_cron_grace_period' );

/**
 * Adds a 2-day grace period to the expiration date value.
 *
 * @param string         $expiration_date
 * @param bool           $formatted
 * @param int            $membership_id
 * @param RCP_Membership $membership
 *
 * @return string
 */
function ag_rcp_add_expiration_date_grace( $expiration_date, $formatted, $membership_id, $membership ) {

	// Bail if never expires or formatted.
	if ( 'none' === $expiration_date || empty( $expiration_date ) || $formatted ) {
		return $expiration_date;
	}

	try {
		$expiration = new DateTime( $expiration_date );
		$expiration->modify( '+2 days' );

		return $expiration->format( 'Y-m-d H:i:s' );
	} catch ( \Exception $e ) {
		return $expiration_date;
	}

}

add_filter( 'rcp_membership_get_expiration_date', 'ag_rcp_add_expiration_date_grace', 10, 4 );