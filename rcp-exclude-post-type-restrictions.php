<?php
/**
 * Plugin Name: Restrict Content Pro - Exclude from Post Type Restrictions
 * Description: Allows you to exclude a post from global post type restrictions.
 * Version: 1.0
 * Author: Restrict Content Pro team
 * License: GPL2
 */

/**
 * If this post has post type restrictions, add a checkbox allowing you to exclude this post.
 *
 * @since 1.0
 * @return void
 */
function ag_rcp_exclude_from_restrictions_meta() {

	$pt_restrictions = rcp_get_post_type_restrictions( get_post_type( get_the_ID() ) );

	// Bail if this post type is not restricted.
	if ( empty( $pt_restrictions ) ) {
		return;
	}

	// Otherwise, show the checkbox.
	$override = get_post_meta( get_the_ID(), 'rcp_override_pt_restrictions', true );
	?>
	<div class="rcp-metabox-field">
		<p><strong><?php _e( 'Override' ); ?></strong></p>
		<label for="rcp-override-pt-restrictions">
			<input type="checkbox" name="rcp_override_pt_restrictions" id="rcp-override-pt-restrictions" value="1"<?php checked( $override ); ?>/>
			<?php _e( 'Override post type restrictions and make this post available to everyone.', 'rcp' ); ?>
		</label>
	</div>
	<?php
	wp_nonce_field( 'rcp_save_post_type_restriction_override', 'rcp_post_type_restriction_override_nonce' );

}

add_action( 'rcp_metabox_additional_options_before', 'ag_rcp_exclude_from_restrictions_meta' );

/**
 * Save checkbox.
 *
 * @param int $post_id ID of the post being saved.
 *
 * @since 1.0
 * @return void
 */
function ag_rcp_save_pt_override( $post_id ) {

	// Verify nonce.
	if ( empty( $_POST['rcp_post_type_restriction_override_nonce'] ) || ! wp_verify_nonce( $_POST['rcp_post_type_restriction_override_nonce'], 'rcp_save_post_type_restriction_override' ) ) {
		return;
	}

	// Check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions.
	if ( 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {

		return;

	}

	if ( ! empty( $_POST['rcp_override_pt_restrictions'] ) ) {
		update_post_meta( $post_id, 'rcp_override_pt_restrictions', 1 );
	} else {
		delete_post_meta( $post_id, 'rcp_override_pt_restrictions' );
	}

}

add_action( 'save_post', 'ag_rcp_save_pt_override' );

/**
 * If this post has a global post type restriction and the override box is checked, grant the user access.
 *
 * @param bool       $can_access Whether or not the user can access this post.
 * @param int        $user_id    ID of the user being checked.
 * @param int        $post_id    ID of the post being checked.
 * @param RCP_Member $member     Member object.
 *
 * @since 1.0
 * @return bool
 */
function ag_rcp_maybe_override_pt_restrictions( $can_access, $user_id, $post_id, $member ) {

	if ( $can_access ) {
		return $can_access;
	}

	$post_type_restrictions = rcp_get_post_type_restrictions( get_post_type( $post_id ) );

	// Post type isn't restricted - bail.
	if ( empty( $post_type_restrictions ) ) {
		return $can_access;
	}

	$override = get_post_meta( $post_id, 'rcp_override_pt_restrictions', true );

	if ( ! empty( $override ) ) {
		return true;
	}

	return $can_access;

}

add_filter( 'rcp_member_can_access', 'ag_rcp_maybe_override_pt_restrictions', 99999, 4 );