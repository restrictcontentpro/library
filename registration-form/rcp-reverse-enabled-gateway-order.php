<?php
/**
 * Plugin Name: Restrict Content Pro - Reverse Gateways
 * Description: Reverses the order of the enabled gateways on the registration form.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * This will reverse the order of the enabled gateways on the registration form.
 *
 * @param array $enabled   Enabled gateways.
 * @param array $available Available gateways.
 *
 * @return array
 */
function jp_enabled_gateways_ordering( $enabled, $available ) {
	return array_reverse( $enabled );
}
add_filter( 'rcp_enabled_payment_gateways', 'jp_enabled_gateways_ordering', 10, 2 );