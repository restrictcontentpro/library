<?php
/**
 * Plugin Name: Restrict Content Pro - Add Expiration Grace Period
 * Description: Adds a 2-day grace period to the expiration cron job query.
 * Version: 1.0
 * Author: Restrict Content Pro team
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