<?php
/**
 * Plugin Name: Restrict Content Pro - Purchase via Woocommerce
 * Description: Allows users to purchase non-recurring Restrict Content Pro memberships through WooCommerce.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * INSTRUCTIONS:
 *
 * Once the plugin is installed, a new tab will be added to the product data section on the "Edit Product" page.
 * Click the "RCP Membership" tab to assign an RCP subscription level to the product. This membership level
 * will be granted to any users who purchase this product.
 *
 * NOTES:
 *
 * This integration does not work with renewals, so it's best used with membership levels that have an unlimited
 * duration. If used with a membership level that has a fixed duration (not unlimited), the user's expiration
 * date will be automatically calculated. They will need to purchase the product again to renew their membership.
 * Renewals will not be handled automatically.
 */

/**
 * Add a new product tab for "RCP Membership".
 *
 * @param array $tabs
 *
 * @return array
 */
function ag_rcp_woo_data_tabs( $tabs ) {

	$tabs['rcp_membership'] = array(
		'label'  => __( 'RCP Membership' ),
		'target' => 'rcp_membership',
		'class'  => array()
	);

	return $tabs;

}

add_filter( 'woocommerce_product_data_tabs', 'ag_rcp_woo_data_tabs' );

/**
 * Display contents of RCP Membership tab.
 *
 * @return void
 */
function ag_rcp_woo_data_display() {

	?>
	<div id="rcp_membership" class="panel woocommerce_options_panel">
		<div class="options_group">
			<p><?php _e( 'When a user purchases this product, grant them access to a Restrict Content Pro subscription level.' ); ?></p>

			<?php
			$subscription_levels    = rcp_get_subscription_levels( 'all' );
			$subscription_level_ids = array(
				0 => __( 'None' )
			);

			foreach ( $subscription_levels as $level ) {
				$subscription_level_ids[ $level->id ] = $level->name;
			}

			woocommerce_wp_select( array(
				'id'      => '_rcp_woo_subscription_level_id',
				'label'   => __( 'Subscription Level' ),
				'options' => $subscription_level_ids
			) )
			?>

			<?php wp_nonce_field( 'rcp_woo_product_subscription_level_meta_nonce', 'rcp_woo_product_subscription_level_meta_nonce', false ); ?>
		</div>
	</div>
	<?php

}

add_filter( 'woocommerce_product_data_panels', 'ag_rcp_woo_data_display' );

/**
 * Save post meta.
 *
 * @param int $post_id ID of the product being saved.
 *
 * @return void
 */
function ag_rcp_woo_save_meta( $post_id ) {

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['rcp_woo_product_subscription_level_meta_nonce'] ) || ! wp_verify_nonce( $_POST['rcp_woo_product_subscription_level_meta_nonce'], 'rcp_woo_product_subscription_level_meta_nonce' ) ) {
		return;
	}

	// Don't save revisions and autosaves
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Check user permission
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! empty( $_POST['_rcp_woo_subscription_level_id'] ) ) {
		update_post_meta( $post_id, '_rcp_woo_subscription_level_id', absint( $_POST['_rcp_woo_subscription_level_id'] ) );
	} else {
		delete_post_meta( $post_id, '_rcp_woo_subscription_level_id' );
	}

}

add_action( 'save_post_product', 'ag_rcp_woo_save_meta' );

/**
 * When WooCommerce order is completed, grant the customer an RCP membership
 * if they purchased a product that's assigned a membership level.
 *
 * @param int            $order_id ID of the order.
 * @param WC_Order|false $order    Order object.
 *
 * @return void
 */
function ag_rcp_woo_payment_complete( $order_id, $order = false ) {

	if ( ! function_exists( 'wc_get_order' ) || ! function_exists( 'rcp_add_membership' ) ) {
		return;
	}

	if ( ! is_object( $order ) ) {
		return;
	}

	$user  = $order->get_user();
	$items = $order->get_items();

	if ( empty( $user->ID ) || empty( $items ) || ! is_array( $items ) ) {
		return;
	}

	foreach ( $items as $item ) {
		/**
		 * @var WC_Order_Item_Product $item
		 */

		$product_id              = $item->get_product_id();
		$rcp_membership_level_id = get_post_meta( $product_id, '_rcp_woo_subscription_level_id', true );

		// Skip to next order item if this one doesn't grant an RCP membership.
		if ( empty( $rcp_membership_level_id ) ) {
			continue;
		}

		// Create customer record if it doesn't already exist.
		$customer = rcp_get_customer_by_user_id( $user->ID );

		if ( empty( $customer ) ) {
			$customer_id = rcp_add_customer( array(
				'user_id' => absint( $user->ID )
			) );

			if ( empty( $customer_id ) ) {
				return;
			}
		} else {
			$customer_id = $customer->get_id();
		}

		// Grant the user access to the designated membership level.
		$membership_args = array(
			'customer_id' => absint( $customer_id ),
			'status'      => 'active',
			'object_id'   => absint( $rcp_membership_level_id ),
			'recurring'   => false // This doesn't support renewals.
		);

		// This function will auto calculate the expiration date.
		$membership_id = rcp_add_membership( $membership_args );

		if ( empty( $membership_id ) ) {
			return;
		}

		$membership = rcp_get_membership( $membership_id );

		// Flag this membership as coming from WooCommerce.
		$membership->add_note( sprintf( __( 'Membership added via WooCommerce order #%d' ), $order_id ) );

		// We can only add one membership at this time.
		break;
	}

}

add_action( 'woocommerce_order_status_completed', 'ag_rcp_woo_payment_complete', 10, 2 );