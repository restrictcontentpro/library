<?php

/**
 * Snippet to list all membership levels.
 *
 * Usage: add the code in a custom template files
 *
 * Returns:
 * $id                  Filter by membership level ID. Default empty.
 * $id__in              Array of membership level IDs to include. Default empty.
 * $id__not_in          Array of membership level IDs to exclude. Default empty.
 * $name                Filter by name. Default empty.
 * $duration            Filter by duration. Default empty.
 * $duration_unit       Filter by duration unit. Default empty.
 * $trial_duration      Filter by trial duration. Default empty.
 * $trial_duration_unit Filter by trial duration unit. Default empty.
 * $price               Filter by price. Default empty.
 * $fee                 Filter by signup fee. Default empty.
 * $maximum_renewals    Filter by maximum renewals. Default empty.
 * $after_final_payment Filter by after final payment action. Default empty.
 * $list_order          Filter by list order. Default empty.
 * $level               Filter by access level. Default empty.
 * $status              Filter by status. Default empty.
 * $status__not_in      Array of statuses to exclude. Default empty.
 * $role                Filter by user role. Default empty.
 * $role__in            Array of roles to include. Default empty.
 * $role__not_in        Array of roles to exclude. Default empty.
 * $date_created_query  Date query for filtering by date created. See WP_Date_Query. Default empty.
 * $count               Whether to return the count of records (true) or membership level objects (false). Default false.
 * $number              Maximum number of results to return. Default 20.
 * $offset              Number of records to offset the query. Used to build LIMIT clause. Default 0.
 * $no_found_rows       Whether to disable the `SQL_CALC_FOUND_ROWS` query. Default true.
 * $orderby             Accepts `id`, `name`, `duration`, `trial_duration`, `maximum_renewals`,
 *                              `list_order`, `level`, `status`, `date_created`, and `date_modified`.
 *                      Also accepts false, an empty array, or `none` to disable `ORDER BY` clause.
 *                               Default `id`.
 * $order               How to order results. Accepts `ASC` and `DESC`. Default `DESC`.
 * $search              Search term(s). Default empty.
 * $update_cache        Whether to prime the cache. Default false.
 * 
 * @since 3.4
 * @return RCP\Membership_Level[] Array of RCP\Membership_Level objects.
 */

$levels = rcp_get_membership_levels( array( 'number' => 999 ) );

    echo "<h3>List all membership levels</h3>";

    if ( ! empty( $levels ) ) {
        
        echo "<ul>";
    
        foreach ( $levels as $level ) {
    
            echo "<li> . $level->description . </li>";
        
        }
        
        echo "</ul>";
    }
