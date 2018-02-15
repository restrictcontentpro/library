<?php
/**
 * Plugin Name: Restrict Content Pro - Email After Manual Payment
 * Description: Emails a member after they sign up with the "Manual" payment gateway.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Emails a member after they sign up with the "Manual" payment gateway.
 * You can use this email to send further instructions on how to submit
 * payment via cheque, bank transfer, etc.
 */

/**
 * Email the member a new manual payment is received (and is pending).
 *
 * @param RCP_Member                 $member
 * @param int                        $payment_id
 * @param RCP_Payment_Gateway_Manual $gateway
 *
 * @return void
 */
function ag_rcp_email_member_on_manual_payment( $member, $payment_id, $gateway ) {

	$subject = 'Please complete your payment to activate your membership';

	$message = 'Hello %name%,
	
Please send your payment to us via bank transfer or cheque to activate your membership.

Your invoice is available here: %invoice_url%

Thank you.';

	$emails             = new RCP_Emails;
	$emails->member_id  = $member->ID;
	$emails->payment_id = $payment_id;

	$emails->send( $member->user_email, $subject, $message );

}
add_action( 'rcp_process_manual_signup', 'ag_rcp_email_member_on_manual_payment', 20, 3 );