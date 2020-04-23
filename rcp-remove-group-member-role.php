<?php

/**
 * Plugin Name: Restrict Content Pro - Remove Group Member Role
 * Description: Removes role from all group members when owner's membership expired and re-adds the role to all members if this expired membership is later renewed.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */
 
 /**
 * Removes granted user role from all group members when the owner's membership expires.
 *
 * @param string $old_status
 * @param int    $membership_id
 */
function ag_rcpga_remove_role_on_expiration( $old_status, $membership_id ) {

    if ( ! function_exists( 'rcpga_get_group_by' ) || ! class_exists( 'RCPGA_Group' ) ) {
        return;
    }

    $group = rcpga_get_group_by( 'membership_id', $membership_id );

    if ( ! $group instanceof RCPGA_Group ) {
        return;
    }

    /**
     * This query is limited to 300 results for performance reasons. Increase at your own risk.
     */
    foreach ( $group->get_members( array( 'number' => 300 ) ) as $member ) {
        /**
         * @var RCPGA_Group_Member $member
         */

        if ( 'owner' !== $member->get_role() ) {
            $user = get_userdata( $member->get_user_id() );
            $user->remove_role( $group->get_membership_role() );
        }
    }

}

add_action( 'rcp_transition_membership_status_expired', 'ag_rcpga_remove_role_on_expiration', 10, 2 );

/**
 * Re-adds role granted by the membership for all group members when a membership is renewed.
 *
 * @param string         $expiration
 * @param int            $membership_id
 * @param RCP_Membership $membership
 */
function ag_rcpga_readd_role_on_renewal( $expiration, $membership_id, $membership ) {

    if ( ! function_exists( 'rcpga_get_group_by' ) || ! class_exists( 'RCPGA_Group' ) ) {
        return;
    }

    $group = rcpga_get_group_by( 'membership_id', $membership_id );

    if ( ! $group instanceof RCPGA_Group ) {
        return;
    }

    /**
     * This query is limited to 300 results for performance reasons. Increase at your own risk.
     */
    foreach ( $group->get_members( array( 'number' => 300 ) ) as $member ) {
        /**
         * @var RCPGA_Group_Member $member
         */

        if ( 'owner' !== $member->get_role() ) {
            $member->set_membership_role();
        }
    }

}

add_action( 'rcp_membership_post_renew', 'ag_rcpga_readd_role_on_renewal', 10, 3 );
