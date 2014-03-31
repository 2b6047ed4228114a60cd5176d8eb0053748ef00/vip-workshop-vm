<?php
/*
Plugin Name: FEP: Clear onunload
Description: Clear the contents of the page before reloading to show how long it takes to reload
Author: Automattic
Author URI: http://automattic.com/
*/

/*

EXAMPLE OPERATION
=================

0. Enable the plugin
1. Note that pages go blank before reloading

*/


add_action( 'wp_head', function() {
	?><script>
	(function($) {
		$( document ).ready( function() {
			$( window ).on( 'beforeunload', function() {
				console.log( 'something' );
				$( 'body' ).html( '' ).css( 'background-color', 'cornsilk' );
			} );
		} );
	} )(jQuery);
	</script><?php
} );
