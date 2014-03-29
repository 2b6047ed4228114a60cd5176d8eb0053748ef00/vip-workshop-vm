jQuery( function( $ ) {
	$( '.site-title' ).after( $( '#vip-test' ) );

	$( '.widget_text .widget-title' ).click( function() {
		$( '.widget_text .textwidget' ).slideDown( 'fast' );
	} );
} );
