<?php
/**
 * Plugin Name: Restrict Content Pro - Change membership level after expiration
 * Description: Moves a member to a new membership level when their membership expires.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Moves a customer to a new membership level when their membership expires.
 * This does not take payment again so it is recommended for moving a member
 * to a free level.
 *
 * @param string $old_status
 * @param int    $membership_id
 *
 * @return void
 */
function ag_rcp_change_membership_level( $old_status, $membership_id ) {

	$new_membership_level = 2; // Change this to the ID number of the level you want to move the member to.
	$new_status           = 'active'; // Change this to the new status you want to give the member - probably 'free' or 'active'.

	/*
	 * By default this expiration date value is empty. This means the expiration date will be
	 * auto calculated based on the membership level you've chosen. If you want to use a different
	 * date you can set one in this value. You can use "none" for the membership to never expire,
	 * or you can specify a MySQL-formatted date like so: "2020-12-31 23:59:59"
	 */
	$new_expiration_date = '';

	$membership = rcp_get_membership( $membership_id );

	if ( empty( $membership ) ) {
		return;
	}

	// We don't need to do anything if they're already on the new level.
	if ( $membership->get_object_id() == $new_membership_level ) {
		return;
	}

	// Disable this membership.
	$membership->disable();

	// Add a new one.
	$membership->get_customer()->add_membership( array(
		'object_id'       => $new_membership_level,
		'status'          => $new_status,
		'expiration_date' => $new_expiration_date,
		'upgraded_from'   => $membership_id
	) );

}

add_action( 'rcp_transition_membership_status_expired', 'ag_rcp_change_membership_level', 10, 2 );