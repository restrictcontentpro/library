<?php

/*
 * 1. Add your column IDs / Names
 * 2. Fill the column values from get_user_meta() using your unique meta keys
 */
function pw_rcp_register_export_columns( $columns ) {
	$columns[ 'my_custom_column' ] = 'My Column Name';
	$columns[ 'my_second_custom_column' ] = 'My Second Column Name';
	return $columns; 
}
add_filter( 'rcp_export_csv_cols_members', 'pw_rcp_register_export_columns' );

function pw_rcp_add_fields_to_export( $data ) {
	
	foreach( $data as $key => $row ) {

		$data[ $key ][ 'my_custom_column' ] = get_user_meta( $row['user_id'], 'my_meta_key', true );
		$data[ $key ][ 'my_second_custom_column' ] = get_user_meta( $row['user_id'], 'my_second_meta_key', true );

	}

	return $data;

}
add_filter( 'rcp_export_get_data_members', 'pw_rcp_add_fields_to_export' );
