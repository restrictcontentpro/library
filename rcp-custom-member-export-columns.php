<?php
/**
 * Plugin Name: Restrict Content Pro - Custom Membership Export Columns
 * Description: Adds custom user meta or membership meta data to each membership export row.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

/**
 * Adds custom user meta data to each member's export row.
 *
 * 1. Add your column IDs / Names
 * 2. Fill the column values from get_user_meta() using your unique meta keys
 */

/**
 * Add additional columns
 *
 * @param array $columns Array of columns.
 *
 * @return array
 */
function pw_rcp_register_export_columns( $columns ) {
	$columns[ 'my_custom_column' ] = 'My Column Name';
	$columns[ 'my_second_custom_column' ] = 'My Second Column Name';
	$columns[ 'my_third_custom_column' ] = 'My Third Column Name';

	return $columns; 
}
add_filter( 'rcp_export_csv_cols_members', 'pw_rcp_register_export_columns' );

/**
 * Add values to each membership row
 *
 * @param array          $data       Array of member data to be included in the CSV export.
 * @param RCP_Membership $membership Membership object.
 *
 * @return array Array with new data added.
 */
function pw_rcp_add_fields_to_export( $data, $membership ) {

	/*
	 * This example is for including user meta data.
	 */
	$user_id = $membership->get_customer()->get_user_id();

	$data[ 'my_custom_column' ] = get_user_meta( $user_id, 'my_meta_key', true );
	$data[ 'my_second_custom_column' ] = get_user_meta( $user_id, 'my_second_meta_key', true );

	/*
	 * This example is for including membership meta data.
	 */
	$data[ 'my_third_custom_column' ] = rcp_get_membership_meta( $membership->get_id(), 'my_third_meta_key', true );

	return $data;

}
add_filter( 'rcp_export_memberships_get_data_row', 'pw_rcp_add_fields_to_export', 10, 2 );