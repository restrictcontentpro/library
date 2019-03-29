<?php
/**
 * Plugin Name: Restrict Content Pro - Reminders for One Level Only
 * Description: Only send expiration/renewal reminders for one membership level.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Normally expiration and renewal reminders are sent to members of all membership levels.
 * This snippet limits the emails to one specific membership level only. There are two
 * functions shown below: the first limits expiration reminders to your chosen level
 * and the other limits renewal reminders to your chosen level. You may use both functions
 * or just choose one.
 */

/**
 * Limits expiration reminders to your chosen membership level only. Only users on this
 * membership level will receive expiration reminder emails.
 *
 * @param array  $args   Query arguments.
 * @param string $period Reminder period.
 * @param string $type   Type of notice to get the memberships for (renewal or expiration).
 *
 * @return array Modified query arguments.
 */
function ag_rcp_limit_expiration_reminders_to_level( $args, $period, $type ) {

	if ( 'expiration' != $type ) {
		return $args;
	}

	$args['object_id'] = 3; // ID of your membership level here

	return $args;

}

add_filter( 'rcp_reminder_subscription_args', 'ag_rcp_limit_expiration_reminders_to_level', 10, 3 );

/**
 * Limits renewal reminders to your chosen membership level only. Only users on this
 * membership level will receive renewal reminder emails.
 *
 * @param array  $args   Query arguments.
 * @param string $period Reminder period.
 * @param string $type   Type of notice to get the memberships for (renewal or expiration).
 *
 * @return array Modified query arguments.
 */
function ag_rcp_limit_renewal_reminders_to_level( $args, $period, $type ) {

	if ( 'renewal' != $type ) {
		return $args;
	}

	$args['object_id'] = 3; // ID of your membership level here

	return $args;

}

add_filter( 'rcp_reminder_subscription_args', 'ag_rcp_limit_renewal_reminders_to_level', 10, 3 );