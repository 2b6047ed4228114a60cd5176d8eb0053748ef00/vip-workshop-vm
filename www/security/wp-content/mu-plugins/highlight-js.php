<?php

function vip_highlight_js_fix_text( $text ) {
	return preg_replace_callback( '#(<pre><code[^>]*?>)(.*?)(</code></pre>)#s', 'vip_highlight_js_fix_text_callback', $text );
}

function vip_highlight_js_fix_text_callback( $matches ) {
	return $matches[1] . trim( htmlspecialchars( $matches[2], ENT_QUOTES ) ) . $matches[3];
}

function vip_highlight_js() {
	wp_enqueue_script( 'highlight-js', plugins_url( '/highlight.js/highlight.pack.js', __FILE__ ), array(), '8.0', true );
	wp_enqueue_style( 'highlight-js', plugins_url( '/highlight.js/styles/github.css', __FILE__ ), array(), '8.0' );
	add_action( 'wp_print_footer_scripts', 'vip_highlight_js_start' );
}

function vip_highlight_js_start() {
?>
<script>hljs.initHighlightingOnLoad();</script>
<?php
}

add_action( 'wp_enqueue_scripts', 'vip_highlight_js' );

add_filter( 'the_content', 'vip_highlight_js_fix_text', 9 );
