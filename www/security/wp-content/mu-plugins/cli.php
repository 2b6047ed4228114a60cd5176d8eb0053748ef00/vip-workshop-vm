<?php

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

function vip_shortcode_highlight( $atts = array(), $content = null, $tag ) {
	switch( $tag ) {
	case 'url' :
		$return = vip_url_shortcode( $atts, $content );
		break;
	case 'attack' :
		$return = vip_attack_shortcode( $atts, $content );
		break;
	}
	return "\033[1m$return\033[0m";
}

function vip_url_shortcode_filter_text( $text ) {
	$shortcode_tags = $GLOBALS['shortcode_tags'];
	remove_all_shortcodes();
	add_shortcode( 'url', 'vip_shortcode_highlight' );
	add_shortcode( 'attack', 'vip_shortcode_highlight' );

	$text = do_shortcode( $text );

	$GLOBALS['shortcode_tags'] = $shortcode_tags;

	return $text;
}

function vip_chunk_filter_text( $text ) {
	$text = preg_replace( '#<\w+[^>]*+>#', "\$0\033[1m", $text );
	$text = preg_replace( '#</\w+[^>]*+>#', "\033[0m\$0", $text );

	$text = wp_kses( $text, 'strip' );

	$lines = preg_split( '/\r?\n/', $text );
	$out = array();
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( strlen( $line ) > 80 ) {
			$words = preg_split( '/\s+/', $line );
			$head = array();
			while ( $words ) {
				$head[] = array_shift( $words );
				$this_head = join( ' ', $head );
				if ( strlen( $this_head ) > 80 ) {
					$out[] = $this_head;
					$head = array();
				}
			}

			if ( $head ) {
				$out[] = join( ' ', $head );
			}
		} else {
			$out[] = $line;
		}
	}

	return join( "\n", $out );
}

function vip_url_shortcode_filter_wp_cli_output() {
	ob_start( 'vip_url_shortcode_filter_text' );
	ob_start( 'vip_chunk_filter_text' );
}

WP_CLI::add_hook( 'before_invoke:post', 'vip_url_shortcode_filter_wp_cli_output' );
WP_CLI::add_hook( 'before_invoke:comment', 'vip_url_shortcode_filter_wp_cli_output' );
