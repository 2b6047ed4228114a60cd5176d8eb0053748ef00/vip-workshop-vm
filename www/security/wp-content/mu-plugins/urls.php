<?php

function vip_url_shortcode( $atts = array(), $content = null ) {
	$atts = shortcode_atts( array(
		'post'   => null,
		'page'   => null,
		'attack' => null,
	), $atts );

	if ( empty( $atts['post'] ) && empty( $atts['attack'] ) ) {
		return home_url();
	}

	if ( isset( $atts['post'] ) ) {
		$query = new WP_Query( array(
			'name' => $atts['post'],
		) );

		if ( $query->have_posts() ) {
			$url = get_permalink( $query->posts[0]->ID );
		} else {
			$url = add_query_arg( urlencode_deep( array(
				'name'   => $atts['post'],
				'action' => 'show_post',
			) ), home_url( '/' ) );
			$url = wp_nonce_url( $url, 'show_post_' . $atts['post'] );
		}

		if ( ! empty( $atts['page'] ) ) {
			$url = add_query_arg( 'page', intval( $atts['page'] ), $url );
		}
	} else {
		$url = home_url( "attacks/{$atts['attack']}" );
	}

	return vip_url_shortcode_output( $url, $content );
}

function vip_url_init() {
	global $wpdb;

	if ( ! isset( $_GET['action'] ) || 'show_post' !== $_GET['action'] || !isset( $_GET['name'] ) ) {
		return;
	}

	$name = wp_unslash( $_GET['name'] );

	if ( !wp_verify_nonce( $_GET['_wpnonce'], 'show_post_' . $_GET['name'] ) ) {
		return;
	}

	// There must be a better way :(
	$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT `ID` FROM `$wpdb->posts` WHERE `post_name` = %s", $name ) );
	wp_publish_post( $post_id );

	$url = remove_query_arg( array( 'action', '_wpnonce' ) );
	nocache_headers();
	wp_safe_redirect( $url );
	exit;
}

add_action( 'init', 'vip_url_init' );

function vip_url_shortcode_output( $url, $content ) {
	if ( ! $content ) {
		return $url;
	}

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return "[$content]($url)";
	}

	return "<a href='$url'>$content</a>";
}

add_shortcode( 'url', 'vip_url_shortcode' );

add_filter( 'comment_text', 'do_shortcode', 8 );
add_filter( 'the_content', 'make_clickable', 11 );

add_filter( 'widget_text', 'do_shortcode' );

function vip_attack_shortcode( $atts = array(), $content = null ) {
	$atts = shortcode_atts( array(
		'attack' => null,
	), $atts );

	if ( ! $atts['attack'] ) {
		return '';
	}

	$attack = trim( file_get_contents( ABSPATH . "../attacks/{$atts['attack']}" ) );

	$attack = str_replace( '@@@WP_HOME@@@', untrailingslashit( home_url() ), $attack );

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return $attack;
	}

	$parts = explode( '.', $atts['attack'] );
	$type = array_pop( $parts );

	return "<pre><code class='$type'>" . htmlspecialchars( $attack, ENT_QUOTES ) . "</code></pre>";
}

add_shortcode( 'attack', 'vip_attack_shortcode' );
