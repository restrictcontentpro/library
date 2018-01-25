<?php
/**
 * Plugin Name: Restrict Content Pro - Change subscription level after expiration
 * Description: Moves a member to a new subscription level when their membership expires.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Moves a member to a new subscription level when their membership expires.
 * This does not take payment again so it is recommended for moving a member
 * to a free level.
 *
 * @param int        $user_id    ID of the user who expired.
 * @param string     $old_status Previous status (probably 'active' or 'free').
 * @param RCP_Member $member     Member object.
 *
 * @return void
 */
function ag_rcp_change_subscription_level( $user_id, $old_status, $member ) {

	$new_subscription_level = 1; // Change this to the ID number of the level you want to move the member to.
	$new_expiration_date    = 'none'; // New expiration date for the member. Use 'none' or a date in MySQL format, like '2017-12-31 23:59:59'
	$new_status             = 'free'; // Change this to the new status you want to give the member - probably 'free' or 'active'.

	// We don't need to do anything if they're already on the new level.
	if ( $member->get_subscription_id() == $new_subscription_level ) {
		return;
	}

	// Update the member's subscription level, expiration date, and status.
	$member->set_subscription_id( $new_subscription_level );
	$member->set_expiration_date( $new_expiration_date );
	$member->set_status( $new_status );

	// Remove the recurring flag since the user won't have a recurring subscription for this new level.
	$member->set_recurring( false );

	// This updates the user's join date for the new subscription level.
	$join_date = $member->get_joined_date( $new_subscription_level );
	if ( empty( $join_date ) ) {
		$member->set_joined_date( '', $new_subscription_level );
	}

}

add_action( 'rcp_set_status_expired', 'ag_rcp_change_subscription_level', 10, 3 );