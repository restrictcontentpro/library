<?php
/**
 * Plugin Name: Restrict Content Pro - Pending Payment Reminder
 * Description: Sends an email to a member when their payment has remained "Pending" for 24 hours.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Sends an email to a member when their payment has remained "Pending" for 24 hours.
 * This can help remind users that their payment has not completed and prompt them to
 * complete their signup.
 */

/**
 * Set up a cron job to run our email task once a day.
 *
 * @return void
 */
function ag_rcp_setup_pending_payment_cron() {

	if ( ! wp_next_scheduled( 'ag_rcp_send_pending_payment_reminders' ) ) {
		wp_schedule_event( current_time( 'timestamp' ), 'daily', 'ag_rcp_send_pending_payment_reminders' );
	}

}

add_action( 'wp', 'ag_rcp_setup_pending_payment_cron' );

/**
 * Find all pending payments that are 24 hours old and email the user.
 *
 * @return void
 */
function ag_rcp_send_pending_payment_reminders() {

	/**
	 * @var RCP_Payments $rcp_payments_db
	 */
	global $rcp_payments_db;

	$args = array(
		'number' => 9999,
		'status' => 'pending',
		'date'   => array(
			'end' => date( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) )
		)
	);

	$payments = $rcp_payments_db->get_payments( $args );

	if ( $payments ) {
		foreach ( $payments as $payment ) {

			$user = get_userdata( $payment->user_id );

			// Set up the RCP_Emails class and properties.
			$emails             = new RCP_Emails;
			$emails->member_id  = $payment->user_id;
			$emails->payment_id = $payment->id;

			// Set up the email subject and message. You'll want to customize these!
			$subject = __( 'Your payment has not been completed' );
			$message = __( 'Hello %name%, your last payment of %amount% has not been completed.' );

			// Send the email.
			$emails->send( $user->user_email, $subject, $message );

			// Add a note to the user's account so we know this email was sent.
			rcp_add_member_note( $user->ID, __( 'Pending payment reminder emailed to user.' ) );

		}
	}

}