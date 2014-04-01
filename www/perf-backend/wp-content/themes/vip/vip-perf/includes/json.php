<?php

if ( false !== strpos( $_SERVER['REQUEST_URI'], '/slow.json' ) ) {
	$value = wp_cache_get( 'pbe_slow_json' );
	$cache_miss = false === $value;

	if ( $cache_miss ) {
		sleep( 5 ); // substitute for some slow operation
		$value = intval( $_GET['i'] );
		wp_cache_set( 'pbe_slow_json', $value, false, 30 );
	}

	echo json_encode( array( 'cache_miss' => $cache_miss, 'value' => $value ) );
	exit;
} elseif ( false !== strpos( $_SERVER['REQUEST_URI'], '/latest.json' ) ) {
	$values = array( 'cookie', 'ice cream', 'cupcake' );
	shuffle( $values );

	echo json_encode( array( 'value' => $values[0] ) );
	exit;		
}
