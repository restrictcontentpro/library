<?php
/**
 * Plugin Name: Restrict Content Pro - Unrestrict RSS Feed
 * Description: Removes post restrictions from RSS feeds. Note that RSS feeds are publicly accessible so this will make your content visible to non-members if they visit your feed URL.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Remove restrictions from RSS feeds.
 *
 * @param string $content
 *
 * @return string
 */
function ag_rcp_remove_restrictions_feeds( $content ) {
	if ( is_feed() ) {
		remove_filter( 'the_content', 'rcp_filter_restricted_content' , 100 );
	}

	return $content;
}

add_filter( 'the_content', 'ag_rcp_remove_restrictions_feeds' , 10 );