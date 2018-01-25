<?php
/**
 * Plugin Name: Restrict Content Pro - Per-Post Restricted Message
 * Description: Adds support for custom restriction messages per post.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * This plugin adds support for custom restriction messages per post.
 * If a custom message exists, it is used in place of the ones defined
 * in the RCP settings under Restrict > Settings > General.
 */

/**
 * Displays the custom message metabox on the post edit screen.
 */
function rcp_post_level_restriction_message_metabox() {

	global $post;
	$content = get_post_meta( $post->ID, 'rcp_post_level_restriction_message', true );
	?>

	<hr>

	<div class="rcp-metabox-field">
		<p><strong><?php _e( 'Override restricted content messages for this post.', 'rcp' ); ?></strong></p>
		<p>
			<label for="rcp_post_level_restriction_message">
				<?php wp_editor( $content, 'rcp_post_level_restriction_message', array( 'drag_drop_upload' => true ) ); ?>
			</label>
		</p>
	</div>
	<?php
	wp_nonce_field( 'rcp_post_level_restriction_message_nonce', 'rcp_post_level_restriction_message_nonce' );
}
add_action( 'rcp_metabox_fields_after', 'rcp_post_level_restriction_message_metabox' );

/**
 * Saves the custom message on the post edit screen.
 */
function rcp_save_post_level_restriction_message( $post_id, $post ) {

	if ( empty( $_POST['rcp_post_level_restriction_message_nonce'] ) || ! wp_verify_nonce( $_POST['rcp_post_level_restriction_message_nonce'], 'rcp_post_level_restriction_message_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( empty( $_POST['rcp_post_level_restriction_message'] ) ) {
		delete_post_meta( $post_id, 'rcp_post_level_restriction_message' );
		return;
	}

	update_post_meta( $post_id, 'rcp_post_level_restriction_message', wp_kses_post( $_POST['rcp_post_level_restriction_message'] ) );
}
add_action( 'save_post', 'rcp_save_post_level_restriction_message', 10, 2 );

/**
 * Overrides the restricted message if a custom one exists for the current post.
 */
function rcp_restricted_message_override( $message ) {

	global $post;

	$custom  = get_post_meta( $post->ID, 'rcp_post_level_restriction_message', true );

	if ( ! empty( $custom ) ) {
		return $custom;
	}

	return $message;

}
add_filter( 'rcp_restricted_message', 'rcp_restricted_message_override' );