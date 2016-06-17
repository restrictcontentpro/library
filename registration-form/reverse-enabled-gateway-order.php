<?php
/**
 * This will reverse the order of the enabled gateways
 * in the dropdown on the registration form.
 */
function jp_enabled_gateways_ordering( $enabled, $available ) {
	return array_reverse( $enabled );
}
add_filter( 'rcp_enabled_payment_gateways', 'jp_enabled_gateways_ordering', 10, 2 );