<?php
/*
Plugin Name: FEP: Blocking Resources
Description: Shows how blocking Javascripts can slow down page load, and if WP is involved, then it's usually even worse.
Author: Automattic
Author URI: http://automattic.com/
*/

/*

EXAMPLE OPERATION
=================

0. Enable FEP: Slow Server plugin
0. Enable this plugin

BLOCKING, HEAD
1. Make a request to the homepage of the site
2. Note that the page is blocked (no content), while it waits for the blocking JS to load
3. Note in the Network tab that fep-blocking-resources.php will take more than the Slow Server time to load each time

BLOCKING, FOOTER
4. Add '?fep-js-foot' to the URL and reload
5. Note that the content shows immediately,
6. Note that DOMContentLoaded and onload both fire once the JS has finished (3+s)

BLOCKING, NO WORDPRESS
7. Add '&fep-no-wp' to the URL and reload (?fep-js-foot&fep-no-wp)
8. Note that fep-blocking-js.php now takes less time to load; approx 5.01s

NON-BLOCKING, NO WORDPRESS
9. Add '&fep-async' to the URL and reload (?fep-js-foot&fep-no-wp&fep-async)
10. Note that the content shows immediately. DOMContentLoaded and onload fire simultaneously, and the script loads for 3s more

*/

// Handler for th request comes first to avoid undefined functions.
if ( isset( $_GET['load'] ) && 'blocking-js' === $_GET['load'] ) {
	if ( !isset( $_GET['fep-no-wp'] ) ) {
		require_once dirname( dirname( __DIR__ ) ) . '/wordpress/wp-load.php';
	}

	header( 'Content-type: text/javascript' );
	die( "console.log( 'FEP: Javascript has been loaded. Peak memory usage was " . number_format( memory_get_peak_usage( true ) ) . " bytes.' );" );
}

$the_slow_javascript_resource = plugins_url( basename( __FILE__ ) . '?load=blocking-js', __FILE__ );
if ( isset( $_GET['fep-no-wp'] ) ) {
	$the_slow_javascript_resource .= '&fep-no-wp';
}

// Add our script request as early as we can, using one of our 2 methods
if ( isset( $_GET['fep-async'] ) ) {
	/*
		In the document head, use Javascript to create a request for our JS file.
		Since the element is only added onload, it blocks neither DOMContentLoaded or onload
	 */
	add_action( 'wp_head', function() {
		global $the_slow_javascript_resource;
		?>
		<script>
		// http://friendlybit.com/js/lazy-loading-asyncronous-javascript/
		(function() {
			function async_load() {
				var s = document.createElement('script'),
					x = document.getElementsByTagName('script')[0];
				s.type = 'text/javascript';
				s.async = true;
				s.src = '<?php echo $the_slow_javascript_resource; ?>';
				x.parentNode.insertBefore(s, x);
			}
			if ( window.attachEvent )
				window.attachEvent( 'onload', async_load );
			else
				window.addEventListener( 'load', async_load, false );
		})();
		</script>
		<?php
	} );
} else {
	/*
		If we just use the normal approahc, and enqueue our script in the head
		of our document, then it blocks the rest of the page from loading fully
		until this resource is loaded.
	 */
	add_action( 'wp_enqueue_scripts', function() {
		global $the_slow_javascript_resource;

		// Optionally load in footer vs header
		$footer = isset( $_GET['fep-js-foot'] ) ? true : false;

		wp_enqueue_script(
			'fep-blocking-js',
			$the_slow_javascript_resource,
			array(),
			1,
			$footer
		);
	}, 1 );
}
