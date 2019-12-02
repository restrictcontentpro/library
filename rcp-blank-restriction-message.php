<?php
/**
 * Plugin Name: Restrict Content Pro - Blank Restriction Message
 * Description: Removes the "this content is restricted..." message so nothing is displayed to unauthorized viewers.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

add_filter( 'rcp_restricted_content_message', '__return_empty_string' );