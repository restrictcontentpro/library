<?php
/**
 * Plugin Name: Restrict Content Pro - Remove Expiration Grace Period
 * Description: Removes the 2-day grace period on the expiration cron job query.
 * Version: 1.0
 * Author: Restrict Content Pro team
 * License: GPL2
 */

/**
 * Removes the 2-day grace period on the expiration cron job query.
 *
 * @param array $expired_memberships
 *
 * @return array
 */
function ag_rcp_remove_grace_period( $expired_memberships ) {

	$expired_memberships = rcp_get_memberships( array(
		'expiration_date_query' => array(
			'after'  => '0000-00-00 00:00:00',
			'before' => current_time( 'mysql' )
		),
		'status' => array( 'active', 'cancelled' ),
		'number' => 99999
	) );

	return $expired_memberships;

}
add_filter( 'rcp_check_for_expired_users_members_filter', 'ag_rcp_remove_grace_period' );