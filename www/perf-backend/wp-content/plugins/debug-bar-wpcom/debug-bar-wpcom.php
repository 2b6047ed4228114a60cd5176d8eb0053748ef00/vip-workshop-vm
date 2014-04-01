<?php

/**
 * Plugin Name: Debug Bar WP.com Panels
 * Descriptions: Advanced Panels for more debugging goodness
 */

add_action( 'debug_bar_panels', 'wpcom_debug_bar_load_panels', 99 );

function wpcom_debug_bar_load_panels( $panels ) {
	require( __DIR__ . '/includes/class-wpcom-debug-bar-queries.php' );
	$panels[] = new WPCOM_Debug_Bar_Queries;

	require( __DIR__ . '/includes/class-wpcom-debug-bar-query-summary.php' );
	$panels[] = new WPCOM_Debug_Bar_Query_Summary;

	return $panels;
}

add_filter( 'debug_bar_title', function( $title ) {
	$title = sprintf( '%ss | %sq', timer_stop(), get_num_queries() );
	return $title; 
} );
