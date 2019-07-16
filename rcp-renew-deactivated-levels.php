<?php
/**
 * Plugin Name: Restrict Content Pro - Renew Deactivated Membership Levels
 * Description: Allows deactivated membership levels to be renewed by customers with existing memberships.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

add_filter( 'rcp_can_renew_deactivated_membership_levels', '__return_true' );