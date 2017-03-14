<?php
/**
 * Restricts post title content for unauthorized viewers.
 *
 * @param string $title   Original post title.
 * @param int    $post_id ID of the corresponding post.
 *
 * @return string Modified post title.
 */
function ag_rcp_restrict_post_titles( $title, $post_id ) {

	$member = new RCP_Member( get_current_user_id() );

	if ( ! $member->can_access( $post_id ) ) {
		$title = __( 'Restricted to Subscribers' ); // Message to show instead of the post title
	}

	return $title;

}
add_filter( 'the_title', 'ag_rcp_restrict_post_titles', 10, 2 );