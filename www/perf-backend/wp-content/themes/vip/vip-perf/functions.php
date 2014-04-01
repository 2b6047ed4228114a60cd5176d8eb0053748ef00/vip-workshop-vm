<?php

require( __DIR__ . '/includes/redirects.php' );

add_action( 'wp_enqueue_scripts', function() {
	wp_register_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ) );
} );

add_filter( 'comments_number', function( $wpdb ) {
	global $wpdb;

	return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_type != 'liveblog'", get_the_ID() ) );
} );
