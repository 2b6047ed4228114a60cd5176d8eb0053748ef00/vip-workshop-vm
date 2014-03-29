<?php

/*
 * http://serennu.com/colour/rgbtohsl.php
 */

function color_similarity( $hex1, $hex2 ) {
	list ( $h1, $s1, $l1 ) = hex_to_hue( $hex1 );
	list ( $h2, $s2, $l2 ) = hex_to_hue( $hex2 );

	$delta = $h1 - $h2;

	return abs( $delta );
}

function complementary_color( $hex ) {
	list ( $h, $s, $l ) = hex_to_hue( $hex );

	$h += 0.5;

	if ( $h > 1 ) {
		$h -= 1;
	}

	return hue_to_hex( $h, $s, $l );
}

// http://24ways.org/2010/calculating-color-contrast/
function contrasting_color_YIQ( $hex ) {
	list ( $r, $g, $b ) = hex_to_rgb( $hex, 255 );

	$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;
	return 128 <= $yiq ? '#000000' : '#ffffff';
}

function hex_to_rgb( $hex, $factor = 1 ) {
	return array(
		hexdec( substr( $hex, 1, 2 ) ) / 255 * $factor,
		hexdec( substr( $hex, 3, 2 ) ) / 255 * $factor,
		hexdec( substr( $hex, 5, 2 ) ) / 255 * $factor,
	);
}

function rgb_to_hex( $r, $g, $b, $factor = 1 ) {
	return sprintf( "#%02x%02x%02x", $r * 255 / $factor, $g * 255 / $factor, $b * 255 / $factor );
}

function rgb_to_hue( $r, $g, $b ) {
	$min = min( $r, $g, $b );
	$max = max( $r, $g, $b );
	$delta = $max - $min;

	$l = ( $max + $min ) / 2;

	if ( 0 == $delta ) {
		$h = 0;
		$s = 0;
	} else {
		if ( $l < 0.5 ) {
			$s = $delta / ( $max + $min );
		} else {
			$s = $delta / ( 2 - $max - $min );
		}

		$delta_r = ( ( ( $max - $r ) / 6 ) + ( $delta / 2 ) ) / $delta;
		$delta_g = ( ( ( $max - $g ) / 6 ) + ( $delta / 2 ) ) / $delta;
		$delta_b = ( ( ( $max - $b ) / 6 ) + ( $delta / 2 ) ) / $delta;
	
		if ( $r == $max ) {
			$h = $delta_b - $delta_g;
		} else if ( $g == $max ) {
			$h = 0.333 + $delta_r - $delta_b;
		} else if ( $b == $max ) {
			$h = 0.667 + $delta_g - $delta_r;
		}
	
		if ( $h < 0 ) {
			$h += 1;
		}

		if ( $h > 1 ) {
			$h -= 1;
		}
	}
	
	return array( $h, $s, $l );
}

function _hue_to_rgb( $v1, $v2, $vh ) {
	if ( $vh < 0 ) {
		$vh += 1;
	}

	if ( $vh > 1 ) {
		$vh -= 1;
	}

	if ( ( 6 * $vh ) < 1 ) {
		return( $v1 + ( $v2 - $v1 ) * 6 * $vh );
	}

	if ( ( 2 * $vh ) < 1 ) {
		return( $v2 );
	}

	if ( ( 3 * $vh ) < 2 ) {
		return( $v1 + ( $v2 - $v1 ) * ( ( 0.667 - $vh ) * 6 ) );
	}

	return( $v1 );
}


function hue_to_rgb( $h, $s, $l ) {
	if ( 0 == $s ) {
		return array_fill( 0, 3, $l * 255 );
	}

	if ( $l < 0.5 ) {
		$v2 = $l * ( 1 + $s );
	} else {
		$v2 = ( $l + $s ) - ( $s * $l );
	}

	$v1 = ( 2 * $l ) - $v2;

	return array(
		_hue_to_rgb( $v1, $v2, $h + 0.333 ),
		_hue_to_rgb( $v1, $v2, $h ),
		_hue_to_rgb( $v1, $v2, $h - 0.333 ),
	);
}

function hex_to_hue( $hex ) {
	list ( $r, $g, $b ) = hex_to_rgb( $hex );
	return rgb_to_hue( $r, $g, $b );
}

function hue_to_hex( $h, $s, $l ) {
	list ( $r, $g, $b ) = hue_to_rgb( $h, $s, $l );
	return rgb_to_hex( $r, $g, $b );
}
