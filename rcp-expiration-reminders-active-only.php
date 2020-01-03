<?php
/**
 * Plugin Name: Restrict Content Pro - Expiration Reminders to Active Members Only
 * Description: Ensure expiration reminders are only sent to "active" members.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Modify reminder query arguments to only target memberships with an "active" status.
 *
 * @param array  $args   Query arguments.
 * @param string $period Reminder period.
 * @param string $type   Type of notice to get the subscriptions for (renewal or expiration).
 *
 * @return array
 */
function ag_rcp_expiration_reminders_active_only( $args, $period, $type ) {

	if ( 'expiration' === $type ) {
		unset( $args['status__in'] );

		$args['status'] = 'active';
	}

	return $args;

}
add_filter( 'rcp_reminder_subscription_args', 'ag_rcp_expiration_reminders_active_only', 10, 3 );