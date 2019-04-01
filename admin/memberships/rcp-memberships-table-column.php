<?php
/**
 * Plugin Name: Restrict Content Pro - Memberships Table Column
 * Description: Adds a new column to the Memberships admin table.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Add a new column to the memberships table for "email".
 *
 * This is just where you set the name of the column.
 *
 * @param array $columns
 *
 * @return array
 */
function ag_rcp_memberships_list_table_columns( $columns ) {
	$columns['email'] = __( 'Email' );

	return $columns;
}
add_filter( 'rcp_memberships_list_table_columns', 'ag_rcp_memberships_list_table_columns' );

/**
 * Display email value.
 *
 * This is where you display the column value for each membership record.
 *
 * @param mixed          $value
 * @param RCP_Membership $membership
 *
 * @return string
 */
function ag_rcp_memberships_list_table_email_value( $value, $membership ) {

	$user_id = $membership->get_customer()->get_user_id();

	if ( empty( $user_id ) ) {
		return $value;
	}

	$user = get_userdata( $user_id );

	if ( empty( $user ) ) {
		return $value;
	}

	return $user->user_email;

}
add_filter( 'rcp_memberships_list_table_column_email', 'ag_rcp_memberships_list_table_email_value', 10, 2 );
/*
 * Note: The filter name above contains the column slug on the end:
 * rcp_memberships_list_table_column_{$slug}
 * You need to change this if you've modified the slug on line 20.
 */