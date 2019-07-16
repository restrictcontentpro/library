<?php
/**
 * Plugin Name: Restrict Content Pro - Restrict Blog Page
 * Description: Redirects non-active subscribers to the home page when they try to view the blog page.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * This snippet will redirect non-active subscribers to the home page
 * when they try to view the blog page
 */
function pw_rcp_redirect_blog() {
	
	if( is_home() && ! rcp_is_active() ) {
		wp_redirect( home_url( '/' ) ); exit;
	}
}
add_action( 'template_redirect', 'pw_rcp_redirect_blog' );