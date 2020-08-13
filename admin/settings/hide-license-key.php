<?php
/**
 * Plugin Name: Restrict Content Pro - Hide License Key
 * Description: Hides the license key field.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Hide license key field.
 *
 * Note: Once this code is added you won't be able to view / edit / remove the license key.
 */
function ag_rcp_hide_license_key() {
	global $rcp_settings_page;
	$screen = get_current_screen();

	if ( $rcp_settings_page != $screen->id ) {
		return;
	}
	?>
	<style>
		#rcp-settings-wrap #general .form-table tbody > tr:first-child + tr {
			display: none;
		}
	</style>
	<?php
}

add_action( 'admin_head', 'ag_rcp_hide_license_key' );
