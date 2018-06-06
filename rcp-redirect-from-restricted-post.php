<?php
/**
 * Plugin Name: Restrict Content Pro - Restrict from Restricted Posts
 * Description: Prevents unauthorized users from accessing restricted pages by redirecting them to another page.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * NOTE:
 *
 * This works exactly the same as the "Hide Restricted Posts" feature in Restrict > Settings > Misc, but it only
 * handles the redirect portion. It does not remove restricted posts from archive queries. Use this code if you
 * want the redirect benefits of "Hide Restricted Posts" without the remove-from-queries feature.
 *
 * INSTRUCTIONS:
 *
 * Uncheck "Hide Restricted Posts" in Restrict > Settings > Misc. Select your "Redirect Page" in the setting below.
 *  Unauthorized users will be redirected to this chosen page if they try to access a restricted post. If you do not
 * have a redirect page chosen, the homepage will be used instead.
 */

/**
 * Redirect unauthorized users to designated redirect page.
 *
 * @since 1.0
 * @return void
 */
function ag_rcp_redirect_from_restricted_post() {
	global $rcp_options, $post;

	$member = new RCP_Member( get_current_user_id() );

	// Bail if we're not on a single post/page.
	if ( ! is_singular() ) {
		return;
	}

	// Bail if current user has permission to view this post/page.
	if ( $member->can_access( $post->ID ) ) {
		return;
	}

	$redirect_page_id = $rcp_options['redirect_from_premium'];

	// Use chosen redirect page, or homepage if not set.
	$redirect_url = ( ! empty( $redirect_page_id ) && $post->ID != $redirect_page_id ) ? get_permalink( $redirect_page_id ) : home_url();

	wp_redirect( $redirect_url );
	exit;
}

add_action( 'template_redirect', 'ag_rcp_redirect_from_restricted_post', 999 );