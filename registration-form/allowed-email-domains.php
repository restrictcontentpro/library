<?php
/**
 * Plugin Name: Restrict Content Pro - Allowed Email Domains
 * Description: Only allow registrations from certain email domains.
 * Version: 1.0
 * Author: Restrict Content Pro team
 * License: GPL2
 */
add_action( 'rcp_before_form_errors', function( $data ) {

	/**
	 * Return early if no email provided. RCP core already handles this.
	 */
	if( empty( $data['rcp_user_email'] ) ) {
		return;
	}

	/** Add a default error which will be removed below if a valid email is supplied. */
	rcp_errors()->add( 'email_invalid', 'Invalid email address', 'register' );

	/** Get the email tld */
	$domain = explode( '@', $data['rcp_user_email'] );
	$domain = end( $domain );
	$tld = substr( $domain, strrpos( $domain, '.' ) );

	/**
	 * Allowed email domains, including the period.
	 * Add/edit/remove whatever you want here.
	 */
	$allowed = array (
		'.mil',
		'.gov',
	);

	foreach( $allowed as $check ) {
		if( $check === $tld ) {
			rcp_errors()->remove( 'email_invalid' );
			break;
		}
	}

} );