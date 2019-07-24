<?php
/**
 * Plugin Name: Restrict Content Pro - Group Accounts Members to MailChimp
 * Description: Subscribe users to your MailChimp list when they join a group.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Subscribe users to your MailChimp list when they join a group.
 *
 * @param int   $user_id User ID of the group member.
 * @param array $args    Arguments used when adding the member to the group.
 */
function ag_rcp_add_group_members_to_mailchimp( $user_id, $args ) {

	// Bail if MailChimp Pro is not installed.
	if ( ! function_exists( 'rcp_mailchimp_pro' ) || ! function_exists( 'rcpga_group_accounts' ) ) {
		return;
	}

	$user = get_userdata( $user_id );

	if ( empty( $user ) ) {
		return;
	}

	$membership_id = ! empty( $args['group_id'] ) ? rcpga_group_accounts()->groups->get_membership_id( absint( $args['group_id'] ) ) : 0;

	// Bail if there's no membership associated with this group.
	if ( empty( $membership_id ) || ! $membership = rcp_get_membership( $membership_id ) ) {
		return;
	}

	$subscribe = rcp_mailchimp_pro()->api_helper->subscribe( $user->user_email, $user_id, $membership );

	if ( $subscribe ) {
		rcp_log( sprintf( 'Successfully added group member user #%d to MailChimp list.', $user_id ) );
	} else {
		rcp_log( sprintf( 'Failed to add group member user ID #%d to MailChimp List.', $user_id ) );
	}

}

add_action( 'rcpga_add_member_to_group_after', 'ag_rcp_add_group_members_to_mailchimp', 10, 2 );