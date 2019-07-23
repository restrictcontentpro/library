<?php
/**
 * Custom Payment Gateway Class
 *
 * @package   rcp
 * @copyright Copyright (c) 2019, Sandhills Development, LLC
 * @license   GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// @todo This class name should match what you set in pw_rcp_register_custom_gateway()
class AG_RCP_Payment_Gateway_Custom extends RCP_Payment_Gateway {

	/**
	 * @todo Create any properties required for storing API keys, etc.
	 */

	/**
	 * @var string
	 */
	protected $api_key;

	/**
	 * @var string
	 */
	protected $api_url;

	/**
	 * Declare feature support and set up any environment variables like API key(s), endpoint URL, etc.
	 */
	public function init() {

		// Declare feature support.
		$this->supports[] = 'one-time';
		$this->supports[] = 'recurring';
		$this->supports[] = 'fees';
		$this->supports[] = 'gateway-submits-form';
		$this->supports[] = 'trial';

		// Configure API key.
		$this->api_key = '123456';

		// Set API URL based on sandbox or live mode.
		if ( rcp_is_sandbox() ) {
			$this->api_url = 'sandbox.gateway.com'; // API endpoint for sandbox
		} else {
			$this->api_url = 'live.gateway.com'; // API endpoint for live transactions
		}

	}

	/**
	 * Interact with the payment gateway API to create a charge / subscription,
	 * or redirect to third party payment page to complete payment.
	 *
	 * Useful properties are:
	 *
	 * $this->auto_renew (bool) - Whether or not this registration has auto renew enabled.
	 * $this->initial_amount (float) - Amount to be charged today. This has fees/credits/discounts included.
	 * $this->amount (float) - Amount to be charged on renewals.
	 * $this->length (int) - Length of the membership billing cycle. So if each cycle is 1 month then this value will
	 *                       be "1" and the below value will be "month".
	 * $this->length_unit (string) - Duration unit ("day", "month", or "year").
	 * $this->payment (object) - "Pending" payment record for the payment to be made.
	 * $this->membership (RCP_Membership object) - "Pending" membership record.
	 * $this->email (string) - Email address for the customer signing up.
	 * $this->user_id (int) - ID of the user account for the customer signing up.
	 *
	 * @return void
	 */
	public function process_signup() {

		/**
		 * @var RCP_Payments $rcp_payments_db
		 */
		global $rcp_payments_db;

		$payment_failed = false;

		// Don't use this variable. It's for backwards compatibility in some action hooks.
		$member = new RCP_Member( $this->membership->get_customer()->get_user_id() );

		if ( $this->auto_renew ) {

			/*
			 * Create recurring subscription where the amount due today is $this->initial_amount and the recurring
			 * amount is $this->amount.
			 *
			 * Once the subscription is created, make sure you set the subscription ID. This is how we link the
			 * record in RCP to the record in the gateway.
			 */
			$this->membership->set_gateway_subscription_id( 'gateway_subscription_id_goes_here' );

		} else {

			/*
			 * Process a one-time charge for $this->initial_amount.
			 */

		}

		/*
		 * What to do if part of the process fails.
		 */
		if ( $payment_failed ) {
			$error_message = __( 'An error occurred' ); // @todo Replace with your error message.

			$this->handle_processing_error( new Exception( $error_message ) ); // This will wp_die()
		}

		/*
		 * If payment can be confirmed now, then activate the membership by completing the pending payment.
		 * This activates the membership for you automatically.
		 */
		$rcp_payments_db->update( $this->payment->id, array(
			'transaction_id' => 'your_transaction_id_here', // @todo set transaction ID
			'status'         => 'complete'
		) );

		do_action( 'rcp_gateway_payment_processed', $member, $this->payment->id, $this );

		/*
		 * When you're all done, redirect to the success page.
		 */
		wp_redirect( $this->return_url );
		exit;

	}

	/**
	 * Handles the error processing.
	 *
	 * @param Exception $exception
	 */
	protected function handle_processing_error( $exception ) {

		$this->error_message = $exception->getMessage();

		do_action( 'rcp_registration_failed', $this );

		wp_die( $exception->getMessage(), __( 'Error', 'rcp' ), array( 'response' => 401 ) );

	}

	/**
	 * Demonstrates how to add fields to the registration form, like credit card fields.
	 *
	 * Remove this if no extra fields are required.
	 *
	 * @return string
	 */
	public function fields() {

		ob_start();
		rcp_get_template_part( 'card-form' );

		return ob_get_clean();

	}

	/**
	 * Demonstrates how to check for errors on the form fields.
	 *
	 * Remove this if you don't have any included / required fields.
	 */
	public function validate_fields() {

		if ( empty( $_POST['rcp_card_number'] ) ) {
			rcp_errors()->add( 'missing_card_number', __( 'Please enter a card number', 'rcp' ), 'register' );
		}

		if ( empty( $_POST['rcp_card_cvc'] ) ) {
			rcp_errors()->add( 'missing_card_code', __( 'The security code you have entered is invalid', 'rcp' ), 'register' );
		}

		if ( empty( $_POST['rcp_card_zip'] ) ) {
			rcp_errors()->add( 'missing_card_zip', __( 'Please enter a Zip / Postal Code code', 'rcp' ), 'register' );
		}

	}

	/**
	 * Process webhooks - for logging renewal payments
	 *
	 * @return void
	 */
	public function process_webhooks() {

		/**
		 * @var RCP_Payments $rcp_payments_db
		 */
		global $rcp_payments_db;

		$data = $_POST; // However you retrieve webhook data.

		/*
		 * The best way to link a webhook to associated membership is by the gateway subscription ID, assuming
		 * the webhook contains this information.
		 */
		$this->membership = rcp_get_membership_by( 'gateway_subscription_id', $data['subscription_id'] );

		// You will need to exit if the membership cannot be located.
		if ( empty( $this->membership ) ) {
			die();
		}

		// Don't use this variable. It's for backwards compatibility in some action hooks.
		$member = new RCP_Member( $this->membership->get_customer()->get_user_id() );

		/*
		 * The below functions demonstrate how to perform different membership actions. Format will vary depending
		 * on your gateway API.
		 */

		switch ( $data['event_type'] ) {

			/**
			 * Successful renewal payment.
			 */
			case 'renewal_payment_success' :

				// Renew the membership.
				$this->membership->renew( true );

				// Insert a new payment record.
				$payment_id = $rcp_payments_db->insert( array(
					'transaction_type' => 'renewal',
					'user_id'          => $this->membership->get_customer()->get_user_id(),
					'customer_id'      => $this->membership->get_customer_id(),
					'membership_id'    => $this->membership->get_id(),
					'amount'           => $data['amount'], // @todo Payment amount.
					'transaction_id'   => $data['transaction_id'], // @todo Transaction ID.
					'subscription'     => rcp_get_subscription_name( $this->membership->get_object_id() ),
					'subscription_key' => $this->membership->get_subscription_key(),
					'object_type'      => 'subscription',
					'object_id'        => $this->membership->get_object_id(),
					'gateway'          => 'custom_gateway_slug' // @todo Custom gateway slug.
				) );

				do_action( 'rcp_webhook_recurring_payment_processed', $member, $payment_id, $this );
				do_action( 'rcp_gateway_payment_processed', $member, $payment_id, $this );

				die( 'renewal payment recorded' );

				break;

			/**
			 * Renewal payment failed.
			 */
			case 'renewal_payment_failed' :

				$this->webhook_event_id = $data['transaction_id']; // @todo Set to failed transaction ID if available.

				do_action( 'rcp_recurring_payment_failed', $member, $this );

				die( 'renewal payment failed' );

				break;

			/**
			 * Subscription cancelled.
			 */
			case 'subscription_cancelled' :

				// If this is a completed payment plan, we can skip any cancellation actions.
				if ( $this->membership->has_payment_plan() && $this->membership->at_maximum_renewals() ) {
					rcp_log( sprintf( 'Membership #%d has completed its payment plan - not cancelling.', $this->membership->get_id() ) );
					die( 'membership payment plan completed' );
				}

				if ( $this->membership->is_active() ) {
					$this->membership->cancel();
				}

				do_action( 'rcp_webhook_cancel', $member, $this );

				die( 'subscription cancelled' );

				break;

		}

	}

}