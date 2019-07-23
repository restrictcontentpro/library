<?php
/**
 * Plugin Name: Restrict Content Pro - Keep Default User Role
 * Description: Ensures that your specified default user role is never removed from members, even if their membership expires.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whenever a role is removed from a user, check to make sure they still have the "default role". If not, re-add it.
 *
 * @param int    $user_id      ID of the user the role was removed from.
 * @param string $removed_role The removed role.
 *
 * @since 1.0
 * @return void
 */
function ag_rcp_always_keep_default_role( $user_id, $removed_role ) {

	$default_role = get_option( 'default_role', 'subscriber' );
	$user         = get_userdata( $user_id );

	if ( ! in_array( $default_role, $user->roles ) ) {
		$user->add_role( $default_role );
	}

}
add_action( 'remove_user_role', 'ag_rcp_always_keep_default_role', 10, 2 );