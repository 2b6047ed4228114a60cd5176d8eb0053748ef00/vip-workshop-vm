<?php
/*
Plugin Name: FEP: CSS Jank
Description: Another example of loading WP slowing things down, combined with inline vs external CSS and page repaint jank.
Author: Automattic
Author URI: http://automattic.com/
*/

/*

EXAMPLE OPERATION
=================

0. Enable FEP: Slow Server plugin
0. Enable this plugin

EXTERNAL CSS REQUEST
1. Load the page and compare the output with and without the plugin active

EXTERNAL CSS, NO WP
2. Add '?fep-no-wp' to the URL and observe faster load time

INLINE CSS
3. Add '&fep-inline-css' to the URL and reload
4. Observe that there is 1 less HTTP request, and the load time is about the same as NO WP

PAGE REPAINT JANK
5. Enable continuous page repainting in DevTools
6. Observe high page repaint times
7. Go through elements an 'h' to hide them and watch repaint times
8. Narrow down to an element, then go through styles and see what's causing it

*/

// Don't load any custom CSS; you should notice the page color/font size difference
if ( isset( $_GET['fep-no-css'] ) )
	return;

// The custom CSS we want to output. Could be variable based on user settings though.
$css = "article { font-size: 280% !important; }
article img { border-radius: 5px; box-shadow: 0px 0px 20px 0px rgba(50, 50, 50, 0.75); margin-bottom: 1em; }";

// Handle direct requests for the "external" CSS file (the bail)
if ( isset( $_GET['load'] ) && 'fep-external-css' === $_GET['load'] ) {
	// Unless we're told not to, load WP so that we can (hypothetically) load options from the DB etc
	if ( !isset( $_GET['fep-no-wp'] ) ) {
		require_once dirname( dirname( __DIR__ ) ) . '/wordpress/wp-load.php';

		// Query for some settings -- using a default since it's not actually there
		$css = get_option( 'fep_custom_css', $css );
	}

	header( 'Content-type: text/css' );
	die( $css );
}

// Handle normal requests. Either inline or enqueue approach
if ( isset( $_GET['fep-inline-css'] ) ) {
	// If we're requesting the inline version, then inject our CSS into wp_head
	add_action( 'wp_head', function() {
		global $css;
		?>
		<style>
			<?php echo $css; ?>
		</style>
		<?php
	} );
} else {
	// Default/normal operation is to load the CSS externally (via a separate request)
	add_action( 'wp_enqueue_scripts', function() {
		$wp = isset( $_GET['fep-no-wp'] ) ? '&fep-no-wp' : '';
		wp_enqueue_style(
			'fep-wp-css',
			plugins_url( basename( __FILE__ ) . '?load=fep-external-css' . $wp, __FILE__ )
		);
	} );
}
