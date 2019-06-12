<?php
/**
 * Plugin Name: Restrict Content Pro - Renew Deactivated Membership Levels
 * Description: Allows deactivated membership levels to be renewed by customers with existing memberships.
 * Version: 1.0
 * Author: Restrict Content Pro team
 * License: GPL2
 */

add_filter( 'rcp_can_renew_deactivated_memberships', '__return_true' );