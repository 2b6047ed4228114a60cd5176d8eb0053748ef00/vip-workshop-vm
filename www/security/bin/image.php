<?php

require dirname( dirname( __FILE__ ) ) . '/wp-content/plugins/background-color-picker/color-functions.php';

$bg_color_hex = empty( $argv[1] ) ? '#ffffff' : $argv[1];
$fg_color_hex = contrasting_color_YIQ( $bg_color_hex );

$text = empty( $argv[2] ) ? '. . .' : $argv[2];

$bounds = imagettfbbox( 24, 0, dirname( __FILE__ ) . '/lato.ttf', $text );

$xs = array();
$ys = array();

for ( $i = 0; $i < 8; $i += 2 ) {
	$xs[] = $bounds[$i];
	$ys[] = $bounds[$i + 1];
}

$left   = min( $xs );
$right  = max( $xs );
$top    = min( $ys );
$bottom = max( $ys );

$width  = abs( $right - $left );
$height = abs( $bottom - $top );
if ( $height < 100 ) {
	$height = 54;
}

$im = @imagecreatetruecolor( $width + 10, $height ) or exit( 1 );

$bg_color_args = hex_to_rgb( $bg_color_hex, 255 );
$fg_color_args = hex_to_rgb( $fg_color_hex, 255 );

array_unshift( $bg_color_args, $im );
array_unshift( $fg_color_args, $im );

$bg_color = call_user_func_array( 'imagecolorallocate', $bg_color_args );
$fg_color = call_user_func_array( 'imagecolorallocate', $fg_color_args );

imagefill( $im, 0, 0, $bg_color );
imagettftext( $im, 24, 0, 2, 39, $fg_color, dirname( __FILE__ ) . '/lato.ttf', $text );

imagepng( $im );
imagedestroy( $im );
