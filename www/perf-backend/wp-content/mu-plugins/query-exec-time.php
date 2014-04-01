<?php

add_filter( 'query', function( $query ) {
	global $query_execution_time;

	if ( ! isset( $query_execution_time ) ) {
		$query_execution_time = array();
	}

	$query_execution_time[ $query ] = timer_stop();

	return $query;
} );
