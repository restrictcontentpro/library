<?php
/**
 * Plugin Name: Restrict Content Pro - Email as Username
 * Description: Use the email address as the username on the registration form.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * This will remove the username requirement on the registration form
 * and use the email address as the username.
 *
 * @param array $user User data.
 *
 * @return array
 */
function jp_rcp_user_registration_data( $user ) {
	rcp_errors()->remove( 'username_empty' );
	$user['login'] = $user['email'];
	return $user;
}
add_filter( 'rcp_user_registration_data', 'jp_rcp_user_registration_data' );