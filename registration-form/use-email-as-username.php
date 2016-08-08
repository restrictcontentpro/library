<?php
/**
 * This will remove the username requirement on the registration form
 * and use the email address as the username.
 */
function jp_rcp_user_registration_data( $user ) {
	rcp_errors()->remove( 'username_empty' );
	$user['login'] = $user['email'];
	return $user;
}
add_filter( 'rcp_user_registration_data', 'jp_rcp_user_registration_data' );