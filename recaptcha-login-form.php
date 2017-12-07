<?php
/**
 * Adds reCAPTCHA to the RCP login form
 *
 * reCAPTCHA must be enabled in Restrict > Settings > Misc.
 * @see http://docs.restrictcontentpro.com/article/1586-recaptcha-supported
 */

/**
 * Add reCAPTCHA to the login form.
 *
 * @return void
 */
function ag_rcp_show_captcha_login_form() {
	global $rcp_options, $rcp_load_scripts;

	if ( rcp_is_recaptcha_enabled() ) :
		$rcp_load_scripts = true;
		?>
		<div id="rcp_recaptcha" data-callback="rcp_validate_recaptcha" class="g-recaptcha" data-sitekey="<?php echo esc_attr( $rcp_options['recaptcha_public_key'] ); ?>"></div>
		<input type="hidden" name="g-recaptcha-remoteip" value=<?php echo esc_attr( rcp_get_ip() ); ?>/><br/>
	<?php endif;
}
add_action( 'rcp_login_form_fields_before_submit', 'ag_rcp_show_captcha_login_form' );

/**
 * Validate reCAPTCHA during login form submission and throw an error if invalid.
 *
 * @param array $data Data passed through the login form.
 *
 * @return void
 */
function ag_rcp_validate_login_captcha( $data ) {

	global $rcp_options;

	if ( ! rcp_is_recaptcha_enabled() ) {
		return;
	}

	if ( empty( $data['g-recaptcha-response'] ) || empty( $data['g-recaptcha-remoteip'] ) ) {
		rcp_errors()->add( 'invalid_recaptcha', __( 'Please verify that you are not a robot', 'rcp' ), 'login' );

		return;
	}

	$verify = wp_safe_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'body' => array(
				'secret'   => trim( $rcp_options['recaptcha_private_key'] ),
				'response' => $data['g-recaptcha-response'],
				'remoteip' => $data['g-recaptcha-remoteip']
			)
		)
	);

	$verify = json_decode( wp_remote_retrieve_body( $verify ) );

	if ( empty( $verify->success ) || true !== $verify->success ) {
		rcp_errors()->add( 'invalid_recaptcha', __( 'Please verify that you are not a robot', 'rcp' ), 'login' );
	}

}
add_action( 'rcp_login_form_errors', 'ag_rcp_validate_login_captcha' );