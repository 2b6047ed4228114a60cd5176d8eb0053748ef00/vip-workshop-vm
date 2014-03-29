<?php

function vip_hack_translate( $text ) {
	if ( 'Proudly powered by %s' === $text ) {
		if ( ! is_singular() ) {
			return $text;
		}

		switch ( get_query_var( 'page' ) ) {
		case 2 :
			return 'Evilly powered by evil';
		case 3 :
			return 'Naïvely powered by naïveté';
		}
	}

	return $text;
}

add_filter( 'gettext', 'vip_hack_translate' );

function vip_hack_description( $text ) {
	if ( ! is_singular() ) {
		return $text;
	}

	switch ( get_query_var( 'page' ) ) {
	case 2 :
		return 'Just another malicious WordPress site';
	case 3 :
		return 'Just another insecure WordPress site';
	}

	return $text;
}


add_filter( 'pre_option_blogdescription', 'vip_hack_description' );

