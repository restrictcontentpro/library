<?php
/**
 * Plugin Name: Restrict Content Pro - Registration Fees on Renewals
 * Description: Charge registration fees on manual renewals.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Always add signup fees, regardless of registration type.
 *
 * By default signup fees are NOT applied to manual renewal payments. This code changes that
 * so signup fees are always added, even if it's a manual renewal.
 *
 * @param bool             $add_fee          Whether or not to add signup fees.
 * @param object           $membership_level Membership level object.
 * @param RCP_Registration $registration     Registration object.
 *
 * @return bool
 */
function ag_rcp_always_apply_fees( $add_fee, $membership_level, $registration ) {
	return true;
}

add_filter( 'rcp_apply_signup_fee_to_registration', 'ag_rcp_always_apply_fees', 10, 3 );