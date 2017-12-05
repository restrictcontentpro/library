<?php
/**
 * Automatically applies a recurring discount code when using the Stripe payment gateway.
 * You first need to create your discount code in Restrict > Discount Codes, then replace
 * `your_discount_code_here` below with your discount code value. If you wish to offer a
 * discount for a different gateway, change `stripe` to the ID of your chosen gateway.
 *
 * @param RCP_Registration $registration
 *
 * @return void
 */
function ag_rcp_auto_add_discount_for_gateway( $registration ) {
	if ( isset( $_POST['rcp_gateway'] ) && 'stripe' == $_POST['rcp_gateway'] ) {
		$registration->add_discount( 'your_discount_code_here', true );
	}
}

add_action( 'rcp_registration_init', 'ag_rcp_auto_add_discount_for_gateway', 20 );