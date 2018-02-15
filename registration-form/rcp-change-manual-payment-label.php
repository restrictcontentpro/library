<?php
/**
 * Plugin Name: Restrict Content Pro - Manual Payment Label
 * Description: Changes the Manual Payment label on the registration form.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * This will change the Manual Payment label on the registration
 * form to the text you specify below.
 */
add_filter( 'rcp_payment_gateways', function( $gateways ) {
	$gateways['manual']['label'] = 'Send Check'; // change 'Send Check' to whatever you want.
	return $gateways;
});