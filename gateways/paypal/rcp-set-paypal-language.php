<?php
/**
 * Plugin Name: Restrict Content Pro - PayPal Language
 * Description: Instructs PayPal Standard to use the specified language instead of the language set in your PayPal account.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * This will instruct PayPal Standard to use the
 * specified language instead of the language
 * set in your PayPal account.
 *
 * See supported locales here: https://developer.paypal.com/docs/classic/api/locale_codes/
 */
add_filter( 'rcp_paypal_args', function( $args ) {
	$args['lc'] = 'es_ES'; // change es_ES to whatever you want
	return $args;
});