function set_color_cookie( value ) {
	document.cookie = 'color=' + escape( value ) + ';path=' + escape( VIP_Color_Picker.cookie_path );
}

function new_recent_color( value, reason, hash ) {
	var $new_color;
	$new_color = jQuery( '<a href="#"><img src="' + VIP_Color_Picker.color_url
		+ '&amp;value=' + encodeURIComponent( value )
		+ '&amp;reason=' + encodeURIComponent( reason )
		+ '&amp;hash=' + encodeURIComponent( hash )
		+ '" data-value="' + value + '" /></a>' );
	jQuery( '.recent-colors' ).prepend( '<li>' ).find( 'li:first' ).append( $new_color );
}

function list_recent_colors() {
	get_recent_colors( show_recent_colors );
}

function get_recent_colors( callback ) {
	var url = VIP_Color_Picker.ajax_url + document.location.search;
	if ( -1 === url.indexOf( '?' ) ) {
		url += '?';
	} else {
		url += '&';
	}

	url += 'action=recent_colors';

	jQuery
		.ajax( url )
		.done( callback );
}

function show_recent_colors( data ) {
	var colors = [];
	jQuery( data ).find( 'color' ).each( function() {
		var color = jQuery( this );
		colors.push( [ color.find( 'value' ).text(), color.find( 'reason' ).text(), color.find( 'hash' ).text() ] );
	} );

	jQuery( '.recent-colors' ).empty();

	jQuery.each( colors.reverse(), function() {
		new_recent_color( this[0], this[1], this[2] );
	} );
}

function save_color( value, reason, callback ) {
	var jqXHR = jQuery.ajax( {
		type: "POST",
		url: VIP_Color_Picker.ajax_url + '?action=save_color',
		contentType: "text/xml",
		data: "<colors><color><value><![" + "CDATA[" + value + "]]" + "></value><reason>" + ( reason || "" ) + "</reason></color></colors>"
	} );

	if ( 'function' === typeof callback ) {
		jqXHR.done( callback );
	}
}

// http://24ways.org/2010/calculating-color-contrast/
function get_contrast_YIQ( hex ) {
	var r = parseInt( hex.substr( 1, 2 ), 16 );
	var g = parseInt( hex.substr( 3, 2 ), 16 );
	var b = parseInt( hex.substr( 5, 2 ), 16 );

	var yiq = ( ( r * 299 ) + ( g * 587 ) + ( b * 114 ) ) / 1000;
	return 128 <= yiq ? '#000' : '#fff';
}

jQuery( function( $ ) {
	var $live_colors = $( 'div.site, .site-content .entry-header, .site-content .entry-content, .site-content .entry-summary, .page-content, .page-links > span' );

	var $color_picker = $( '#vip-background-color-picker-input' ).on( 'change', function() {
		var value = $( this ).val();

		// Live update the site as a preview
		$live_colors.css( {
			'background-color': value,
			'color': get_contrast_YIQ( value )
		} );

		// Set the cookie so the visitor doesn't have to click "Save!"
//		set_color_cookie( value );
	} );

	$( '#vip-background-color-picker :submit:first' ).on( 'click', function( event ) {
		event.preventDefault();

		save_color( $color_picker.val(), $( '#vip-background-color-picker-reason' ).val() );
	} );

	// Click recent colors to change your own
	$( '.recent-colors' ).on( 'click', 'img', function( event ) {
		event.preventDefault();

		$color_picker.val( $( this ).data( 'value' ) ).change();
	} );

	window.ajax_poll_interval = setInterval( list_recent_colors, 2000 );
} );
