<?php
/**
 * Plugin Name: Restrict Content Pro - Body Classes
 * Description: Adds body classes to the registered pages in the Restrict Content Pro settings.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Adds body classes to the registered pages in the Restrict Content Pro settings
 *
 * @param $classes
 *
 * @return array
 */
function jp_add_body_classes( $classes ) {
	global $post, $rcp_options;

	if ( ! is_object( $post ) ) {
		return $classes;
	}

	if ( ! is_page() ) {
		return $classes;
	}

	$page_classes = array(
		'rcp-registration'   => isset( $rcp_options['registration_page'] ) ? $rcp_options['registration_page'] : 0,
		'rcp-success'        => isset( $rcp_options['redirect'] )          ? $rcp_options['redirect']          : 0,
		'rcp-account'        => isset( $rcp_options['account_page'] )      ? $rcp_options['account_page']      : 0,
		'rcp-edit-profile'   => isset( $rcp_options['edit_profile'] )      ? $rcp_options['edit_profile']      : 0,
		'rcp-update-billing' => isset( $rcp_options['update_card'] )       ? $rcp_options['update_card']       : 0,
	);

	$found_classes = array_keys( $page_classes, $post->ID );

	if ( ! empty( $found_classes ) ) {
		foreach ( $found_classes as $class ) {
			$classes[] = $class;
		}
	}

	return $classes;
}
add_filter( 'body_class', 'jp_add_body_classes', 10, 1 );