<?php
/**
 * Plugin Name: Restrict Content Pro - Disable Upgrades
 * Description: Prevent customers from being able to upgrade or downgrade their membership.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

add_filter( 'rcp_can_upgrade_subscription', '__return_false' );