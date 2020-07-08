<?php
/**
 * Plugin Name: Restrict Content Pro - Ultimate Member Directory
 * Description: Adds support for displaying active RCP members in the member directories for Ultimate Member.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * The following plugin adds support for displaying active RCP members
 * in the member directories for Ultimate Member.
 *
 * It adds a new dropdown to the member directory edit screen, where you
 * can select the subscription level you want to display.
 */

/**
 * Add meta box to Ultimate Member directory screen.
 *
 * @return void
 */
function ag_um_rcp_metabox() {
	add_meta_box( 'rcp_um_directory', __( 'Restrict Content Pro', 'rcp' ), 'jp_um_admin_extend_directory_options_general', 'um_directory', 'normal', 'high' );
}

add_action( 'add_meta_boxes', 'ag_um_rcp_metabox' );

/**
 * Adds the subscription level dropdown to the member directory edit screen.
 *
 * @return void
 */
function jp_um_admin_extend_directory_options_general( $um_metabox ) {

	$post_id     = get_the_ID();
	$saved_level = get_post_meta( $post_id, 'um_rcp_subscription_level', true );
	$saved_level = ! empty( $saved_level ) ? absint( $saved_level ) : 'none';
	?>
	<p>
		<label class="um-admin-half" for="um_rcp_subscription_level">RCP Members to Display</label>
		<span class="um-admin-half">

			<select name="um_rcp_subscription_level" id="um_rcp_subscription_level" class="umaf-selectjs um-adm-conditional" style="width: 300px" data-cond1='other' data-cond1-show='custom-field'>
				<option value="none" <?php selected( 'none', $saved_level ); ?>>None</option>
				<?php foreach ( rcp_get_subscription_levels() as $key => $level ) {
					echo '<option value="' . esc_attr( $level->id ) . '" ' . selected( $level->id, $saved_level ) . '>' . $level->name . '</option>';
				}
				?>
			</select>

		</span>
	</p>
	<div class="um-admin-clear"></div>
	<?php
	wp_nonce_field( 'um_rcp_subscription_level_nonce', 'um_rcp_subscription_level_nonce' );
}

/**
 * Saves the subscription level selected on the member directory edit screen.
 *
 * @param int     $post_id
 * @param WP_Post $post
 *
 * @return void
 */
function jp_save_post_um_directory( $post_id, $post ) {

	if ( ! isset( $_POST['um_rcp_subscription_level_nonce'] ) || ! wp_verify_nonce( $_POST['um_rcp_subscription_level_nonce'], 'um_rcp_subscription_level_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( empty( $_POST['um_rcp_subscription_level'] ) || 'none' === $_POST['um_rcp_subscription_level'] ) {
		delete_post_meta( $post_id, 'um_rcp_subscription_level' );

		return;
	}

	update_post_meta( $post_id, 'um_rcp_subscription_level', absint( $_POST['um_rcp_subscription_level'] ) );

}

add_action( 'save_post_um_directory', 'jp_save_post_um_directory', 10, 2 );

/**
 * Alters the member directory query to display active RCP members.
 *
 * @param array $query_args
 * @param array $args
 *
 * @return array
 */
function jp_um_prepare_user_query_args( $query_args, $args ) {

	if ( empty( $args['form_id'] ) ) {
		return $query_args;
	}

	$level = get_post_meta( $args['form_id'], 'um_rcp_subscription_level', true );

	if ( empty( $level ) ) {
		return $query_args;
	}

	$query_args['meta_query']['relation'] = 'AND';
	$query_args['meta_query'][] = array(
		'key'   => 'rcp_subscription_level',
		'value' => absint( $level )
	);
	$query_args['meta_query'][] = array(
		'key'   => 'rcp_status',
		'value' => 'active'
	);

	// Remove the UM filters since we're just showing active RCP members.
	remove_filter( 'um_prepare_user_query_args', 'um_remove_special_users_from_list', 99 );

	return $query_args;
}

add_filter( 'um_prepare_user_query_args', 'jp_um_prepare_user_query_args', 10, 2 );