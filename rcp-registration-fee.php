<?php
/**
 * Plugin Name: Restrict Content Pro - Registration Fee
 * Description: Adds a custom fee to registration.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Adds a custom fee to registration
 *
 * This example adds a $20 fee that applies to the first payment only and is not affected by pro-ration
 *
 * @param RCP_Registration $registration
 *
 * @return void
 */
function pw_rcp_add_registration_fee( $registration ) {
	
	$registration->add_fee( 20, __( 'Custom Registration Fee', 'rcp' ), false, false );

}
add_action( 'rcp_registration_init', 'pw_rcp_add_registration_fee', 20 );

/**
 * Adds a recurring fee to registration
 *
 * This example adds a $20 fee that applies to all payments in the subscription and is not affected by pro-ration
 *
 * @param RCP_Registration $registration
 *
 * @return void
 */
function pw_rcp_add_recurring_registration_fee( $registration ) {
	
	$registration->add_fee( 20, __( 'Custom Registration Fee', 'rcp' ), true, false );

}
add_action( 'rcp_registration_init', 'pw_rcp_add_recurring_registration_fee', 20 );