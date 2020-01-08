<?php
/**
 * Plugin Name: Restrict Content Pro - Prevent Payment Plan Cancellations
 * Description: Prevents customers from cancelling recurring memberships that have a payment plan.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Prevents the "Cancel your membership" link from showing if the membership is a payment plan.
 *
 * @param bool           $can_cancel    Whether or not the membership can be cancelled.
 * @param int            $membership_id ID of the membership being checked.
 * @param RCP_Membership $membership    Membership object.
 *
 * @return bool
 * @throws Exception
 */
function ag_rcp_prevent_payment_plan_cancellations( $can_cancel, $membership_id, $membership ) {

	global $rcp_options;

	// Only do this on the Account Page
	if ( empty( $rcp_options['account_page'] ) || ! is_page( $rcp_options['account_page'] ) ) {
		return $can_cancel;
	}

	// Return early if other conditions aren't already met.
	if ( ! $can_cancel ) {
		return false;
	}

	if ( $membership->has_payment_plan() && ! $membership->is_payment_plan_complete() ) {
		$can_cancel = false;
	}

	return $can_cancel;

}
add_filter( 'rcp_membership_can_cancel', 'ag_rcp_prevent_payment_plan_cancellations', 100, 3 );