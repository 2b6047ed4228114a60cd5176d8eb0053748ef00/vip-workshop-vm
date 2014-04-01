<?php

// To test, go to any 404 page or go to /redirect-me for a valid redirect

add_action( 'template_redirect', function() {
	if ( is_404() ) {
		global $wp;
		$redirect_post_id = pbe_get_redirect_post_id( parse_url( $wp->request, PHP_URL_PATH ) );

		if ( $redirect_post_id ) {
			wp_safe_redirect( get_permalink( $redirect_post_id ) );
			exit;
		}
	}

}, 9 ); // run before canonical

function pbe_get_redirect_post_id( $url ) {
	$post_ids = get_posts( array( 
		'post_type' => array( 'post', 'page' ),
		'posts_per_page' => 1,
		'meta_key' => '_legacy_url', 'meta_value' => $url )
	);

	if ( empty( $post_ids ) ) {
		return false;
	}

	$post_id = $post_ids[0];

	sleep( 1 ); // simulate slow meta query because generating enough data to make it slow is hard :)
	
	return $post_id;	
}
