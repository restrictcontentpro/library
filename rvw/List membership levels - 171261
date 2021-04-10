<?php

$levels = rcp_get_membership_levels( array( 'number' => 999 ) );

if ( ! empty( $levels ) ) {
    echo "<h3>List all membership levels</h3>";
    echo "<ul>";
    
    foreach ( $levels as $level ) {
    
        echo "<li> . $level->description . </li>";
        
        }
        
        echo "</ul>";
    }
?>
