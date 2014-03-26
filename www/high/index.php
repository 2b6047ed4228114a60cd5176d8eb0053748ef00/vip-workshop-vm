<?php
$file_name = $_SERVER['DOCUMENT_ROOT'] .  preg_replace( '/\.phps$/', '.php', urldecode( $_SERVER['REQUEST_URI'] ) );
if ( !file_exists( $file_name ) ) {
	header( 'HTTP/1.1 404 File Not Found' );
	if ( preg_match( '/testing/', $file_name ) ) {
		die('<h1>File not found. Did you checkout the latest code?</h1>');
	} else {
		die('<h1>File not found :(</h1>');
	}
}
highlight_file( $file_name );
