<?php
/**
 * Plugin Name: Restrict Content Pro - Purchase via Easy Digital Downloads
 * Description: Allows users to purchase non-recurring Restrict Content Pro memberships through Easy Digital Downloads.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * INSTRUCTIONS:
 *
 * Once the plugin is installed, a new setting for "RCP Membership" will be added to the "Download Settings"
 * metabox on the "Edit Download" page. Select a subscription level from the dropdown to assign an RCP subscription
 * level to the product. This subscription level will be granted to any users who purchase this product.
 *
 * You must have "Require Login" checked on in Downloads > Settings > Misc > Checkout, as user accounts are
 * required to purchase an RCP membership.
 *
 * NOTES:
 *
 * This integration does not work with renewals/EDD Recurring, so it's best used with subscription levels that have an
 * unlimited duration. If used with a subscription level that has a fixed duration (not unlimited), the user's
 * expiration date will be automatically calculated. They will need to purchase the product again to renew their
 * membership. Renewals will not be handled automatically.
 */

/**
 * Add RCP membership option to the "Download Settings" metabox.
 *
 * @param int $post_id
 *
 * @return void
 */
function ag_rcp_edd_metabox( $post_id ) {

	if ( ! current_user_can( 'manage_shop_settings' ) ) {
		return;
	}

	$subscription_levels    = rcp_get_subscription_levels( 'all' );
	$subscription_level_ids = array(
		0 => __( 'None' )
	);

	foreach ( $subscription_levels as $level ) {
		$subscription_level_ids[ $level->id ] = $level->name;
	}

	$selected_level = get_post_meta( $post_id, '_rcp_edd_subscription_level_id', true );
	?>
	<div id="rcp_edd_membership">
		<p><strong><?php _e( 'RCP Membership' ); ?></strong></p>
		<label for="_rcp_edd_subscription_level_id">
			<?php _e( 'When a user purchases this download, grant them access to a Restrict Content Pro subscription level.' ); ?>
		</label>
		<br/>
		<?php
		echo EDD()->html->select( array(
			'id'               => '_rcp_edd_subscription_level_id',
			'name'             => '_rcp_edd_subscription_level_id',
			'selected'         => $selected_level,
			'options'          => $subscription_level_ids,
			'show_option_all'  => false,
			'show_option_none' => false
		) );
		?>
		<?php wp_nonce_field( 'rcp_edd_download_subscription_level_meta_nonce', 'rcp_edd_download_subscription_level_meta_nonce', false ); ?>
	</div>
	<?php

}

add_action( 'edd_meta_box_settings_fields', 'ag_rcp_edd_metabox', 999 );

/**
 * Save post meta.
 *
 * @param int $post_id ID of the download being saved.
 *
 * @return void
 */
function ag_rcp_edd_save_meta( $post_id ) {

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['rcp_edd_download_subscription_level_meta_nonce'] ) || ! wp_verify_nonce( $_POST['rcp_edd_download_subscription_level_meta_nonce'], 'rcp_edd_download_subscription_level_meta_nonce' ) ) {
		return;
	}

	// Don't save revisions and autosaves
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Check user permission
	if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( 'manage_shop_settings' ) ) {
		return;
	}

	if ( ! empty( $_POST['_rcp_edd_subscription_level_id'] ) ) {
		update_post_meta( $post_id, '_rcp_edd_subscription_level_id', absint( $_POST['_rcp_edd_subscription_level_id'] ) );
	} else {
		delete_post_meta( $post_id, '_rcp_edd_subscription_level_id' );
	}

}

add_action( 'save_post_download', 'ag_rcp_edd_save_meta' );

/**
 * @param int          $payment_id Payment ID.
 * @param EDD_Payment  $payment    EDD_Payment object containing all payment data.
 * @param EDD_Customer $customer   EDD_Customer object containing all customer data.
 *
 * @return void
 */
function ag_rcp_edd_complete_purchase( $payment_id, $payment, $customer ) {

	// There needs to be an account associated with the payment.
	if ( empty( $payment->user_id ) ) {
		return;
	}

	$member    = new RCP_Member( $payment->user_id );
	$downloads = $payment->downloads;

	if ( empty( $downloads ) || ! is_array( $downloads ) ) {
		return;
	}

	foreach ( $downloads as $download ) {
		$rcp_subscription_level_id = get_post_meta( $download['id'], '_rcp_edd_subscription_level_id', true );

		// Skip to next order item if this one doesn't grant an RCP membership.
		if ( empty( $rcp_subscription_level_id ) ) {
			continue;
		}

		// Grant the user access to the designated subscription level.
		$subscription_args = array(
			'subscription_id' => absint( $rcp_subscription_level_id ),
			'recurring'       => false // This doesn't support renewals.
		);

		// This function will auto calculate the expiration date and assign the correct status.
		rcp_add_user_to_subscription( $member->ID, $subscription_args );

		// Flag this membership as coming from Easy Digital Downloads.
		rcp_add_member_note( $member->ID, sprintf( __( 'Membership added via Easy Digital Downloads order #%d' ), $payment_id ) );

		// We can only add one membership at this time.
		break;
	}

}

add_action( 'edd_complete_purchase', 'ag_rcp_edd_complete_purchase', 10, 3 );