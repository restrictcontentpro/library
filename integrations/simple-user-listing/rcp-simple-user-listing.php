<?php
/**
 * Plugin Name: Restrict Content Pro - Simple User Listing
 * Description: Adds two new shortcode parameters to the [userlist] shortcode in Simple User Listing: "rcp_subscription_level" and "rcp_status".
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * This code adds two new shortcode parameters to the
 * [userlist] shortcode in Simple User Listing.
 * They are:
 * rcp_subscription_level - accepts the numeric ID number of the subscription level.
 * rcp_status - accepts the status you want to display (active, expired, pending, cancelled). Defaults to active.
 *
 * Examples:
 * [userlist rcp_subscription_level="6"] - will show active members with the subscription level ID of 6.
 * [userlist rcp_subscription_level="6" rcp_status="expired"] - will show expired members with the subscription level ID of 6.
 *
 * @param array  $args     Query args.
 * @param string $query_id Query ID.
 * @param array  $atts     Shortcode attributes.
 *
 * @return array
 */
function jp_sul_user_query_args( $args, $query_id, $atts ) {

	if ( ! empty( $atts['rcp_subscription_level'] ) ) {
		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key'     => 'rcp_subscription_level',
				'value'   => absint( $atts['rcp_subscription_level'] )
			),
			array(
				'key'     => 'rcp_status',
				'value'   => ! empty( $atts['rcp_status'] ) ? sanitize_text_field( $atts['rcp_status'] ) : 'active'
			)
		);
	}

	return $args;

}
add_filter( 'sul_user_query_args', 'jp_sul_user_query_args', 10, 3 );
