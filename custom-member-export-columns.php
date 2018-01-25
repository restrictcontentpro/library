<?php
/**
 * Plugin Name: Restrict Content Pro - Custom Member Export Columns
 * Description: Adds custom user meta data to each member's export row.
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
	return $columns; 
}
add_filter( 'rcp_export_csv_cols_members', 'pw_rcp_register_export_columns' );

/**
 * Add values to each member's row
 *
 * @param array      $data   Array of member data.
 * @param RCP_Member $member RCP member object.
 *
 * @return array Array with new data added.
 */
function pw_rcp_add_fields_to_export( $data, $member ) {
	
	$data[ 'my_custom_column' ] = get_user_meta( $member->ID, 'my_meta_key', true );
	$data[ 'my_second_custom_column' ] = get_user_meta( $member->ID, 'my_second_meta_key', true );

	return $data;

}
add_filter( 'rcp_export_members_get_data_row', 'pw_rcp_add_fields_to_export', 10, 2 );
