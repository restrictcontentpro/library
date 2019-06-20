<?php
/**
 * Plugin Name: Restrict Content Pro - Custom Payment Gateway
 * Description: Adds support for a custom payment gateway.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Register a custom payment gateway.
 *
 * @param array $gateways
 *
 * @return array
 */
function pw_rcp_register_custom_gateway( $gateways ) {

	$gateways['custom_gateway_slug'] = array(
		'label'       => 'Credit Card', // Displayed on front-end registration form
		'admin_label' => 'Custom Gateway', // Displayed in admin area
		'class'       => 'AG_RCP_Payment_Gateway_Custom' // Name of the custom gateway class
	);

	return $gateways;
}

add_filter( 'rcp_payment_gateways', 'pw_rcp_register_custom_gateway' );

/**
 * Load your class file.
 */
function ag_rcp_load_custom_gateway() {
	require_once plugin_dir_path( __FILE__ ) . 'class-rcp-payment-gateway-custom.php';
}

add_action( 'plugins_loaded', 'ag_rcp_load_custom_gateway' );