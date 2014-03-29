#!/usr/bin/env php
<?php

if ( ! isset( $_SERVER['WP_HOME'] ) ) {
	echo "Sorry :( Test is broken. Ask for help.\n";
	exit;
}
$wp_url = $_SERVER['WP_HOME'];

libxml_disable_entity_loader( true );
libxml_use_internal_errors( true );

$response = file_get_contents( $wp_url . '/?woo="><script>alert(/deface/)</script><oo' );

do {
	if ( false === strpos( $response, 'deface' ) ) {
		break;
	}
	
	$dom = new DOMDocument;
	$dom->loadHTML( $response );

	$scripts = $dom->getElementsByTagName( 'script' );
	if ( ! $scripts->length ) {
		break;
	}

	for ( $i = 0; $i < $scripts->length; $i++ ) {
		$script = $scripts->item( $i );
		if ( 'alert(/deface/)' === $script->textContent ) {
			break 2;
		}
	}
	
	echo "The Trickier XSS Vulnerability is Also Closed\n";
	exit;
} while ( false );

echo "The Trickier XSS Vulnerability is Still Exploitable in Some Browsers\n";
exit( 1 );
