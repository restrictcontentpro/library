<?php
/**
 * Plugin Name: Restrict Content Pro - Confirm Email
 * Description: Adds a second field to the registration form to confirm email address.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * This adds the "Confirm Email" field after the "Password Again" field, as that's where our hook is. If you want to
 * add the second email field directly after the regular email field, you'll need to edit the "register.php" and
 * "register-single.php" template files, paste the contents of the HTML below after the email field, and remove this
 * function from this plugin. See here for instructions on editing template files:
 *
 * @link http://docs.restrictcontentpro.com/article/1738-template-files
 *
 * @return void
 */
function ag_rcp_add_second_email_field() {

	?>
	<p id="rcp_user_email_confirm_wrap">
		<label for="rcp_user_email_confirm"><?php _e( 'Confirm Email', 'rcp' ); ?></label>
		<input name="rcp_user_email_confirm" id="rcp_user_email_confirm" class="required" type="text" value="<?php echo ! empty( $_POST['rcp_user_email_confirm'] ) ? esc_attr( $_POST['rcp_user_email_confirm'] ) : ''; ?>"/>
	</p>
	<?php

}

add_action( 'rcp_after_password_registration_field', 'ag_rcp_add_second_email_field' );

/**
 * Throw an error if the "Confirm Email" field is empty or does not match the normal "Email" field.
 *
 * @param array $data Posted data.
 *
 * @return void
 */
function ag_rcp_validate_confirm_email( $data ) {

	// Return early if no email provided. RCP core already handles this.
	if ( empty( $data['rcp_user_email'] ) ) {
		return;
	}

	// Error if email confirm field is empty.
	if ( empty( $data['rcp_user_email_confirm'] ) || $data['rcp_user_email_confirm'] != $data['rcp_user_email'] ) {
		rcp_errors()->add( 'email_mismatch', __( 'Emails do not match', 'rcp' ), 'register' );
	}

}

add_action( 'rcp_before_form_errors', 'ag_rcp_validate_confirm_email' );