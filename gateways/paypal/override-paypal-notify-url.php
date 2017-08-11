<?php
/**
 * This will change the notify_url sent to PayPal during PayPal Standard payments.
 *
 * By default, RCP sends the local site URL. If you're developing on localhost
 * or some other domain that isn't accessible to PayPal, and you're using
 * a tunnel like ngrok, this will help you get the PayPal IPN data.
 */
add_filter( 'rcp_paypal_args', function( $args ) {
	$args['notify_url'] = 'https://x.ngrok.io/index.php?listener=IPN'; // replace x.grok.io with the desired URL
	return $args;
});
