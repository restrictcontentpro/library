<?php
/**
 * Normally the length of the excerpt is a fixed number of words for all posts. This snippet
 * changes that value to a percentage of the post's length. This example uses an excerpt
 * length of 10%.
 *
 * @param int $excerpt_length Default excerpt length in words.
 *
 * @return int
 */
function ag_rcp_excerpt_length( $excerpt_length ) {

	global $post;

	$percentage     = 10; // 10% of post length is used for excerpt. Change this to adjust the percentage.
	$words_in_post  = str_word_count( wp_strip_all_tags( $post->post_content ) );
	$excerpt_length = round( $words_in_post * ( $percentage / 100 ) );

	return $excerpt_length;

}
add_filter( 'rcp_filter_excerpt_length', 'ag_rcp_excerpt_length' );