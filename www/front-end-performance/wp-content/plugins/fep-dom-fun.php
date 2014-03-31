<?php
/*
Plugin Name: FEP: DOM Fun
Description: Memory leaks from DOM manipulation and AJAX optimizations for JS-based data sources.
Author: Automattic
Author URI: http://automattic.com/
*/

/*

EXAMPLE OPERATION
=================

0. *Disable* FEP: Slow Server
0. Enable this plugin

REMOTE REQUEST vs BOOTSTRAPED DATA
1. Add '?fep-bootstrap-js' to the URL and reload the page
2. Observe less HTTP requests
3. Observe later onload/DCL
4. Also observe slower/longer "Receiving" for the page (bigger single payload)

DOM MANIPULATION: SINGLE vs MANY
5. Remove '?fep-bootstrap-js' from the URL and reload
6. Clear the Timeline tab
7. Hit Record
8. Click the 'Hide' button that appears above the comment listing
9. Once the comments re-appear, click Record again to stop recording
10. Observe the amount of time that is spent Recalculating Style
11. Add '?fep-single-dom-edit' to the URL and refresh, then re-test (steps above)
12. Observe differences: *less* time Recalculating Style, *less* time in Timer/Function calls

MEMORY LEAKS: 3 SNAPSHOT TECHNIQUE
13. Remove all query string parameters and reload
14. Take a Heap Snapshot
15. Click 'Hide' in the comment 'sidebar', wait for them to reappear
16. Take a Heap Snapshot
17. Click 'Hide' in the comment 'sidebar', wait for them to reappear
18. Take a Heap Snapshot
19. Make sure you have the third snapshot highlighted, and change 'All Objects' to 'Objects allocated between Snapshot 1 and Snapshot 2'
20. Observe all those HTMLLIElements! "Remember that yellow objects have JavaScript references on them and red objects are detached nodes which are referenced from one with a yellow background."

jQUERY COMPARISON
21. Add '?fep-single-dom-edit' and reload again; repeat the 3 Snapshot Technique
22. Compare the lack of leaked DOM nodes

*/

// Don't load any AJAX/JS-manipulation at all
if ( isset( $_GET['fep-no-ajax'] ) )
	return;

// If requested, output a list of comments as a JSON object (automatically ends response)
add_action( 'init', function() {
	if ( isset( $_GET['fep-ajax-data'] ) ) {
		$data = get_comments( array( 'number' => 20 ) );
		wp_send_json_success( $data );
	}
} );

// Include some basic CSS to format the comments. Alwys in the head
add_action( 'wp_head', function() {
	?>
	<style>
	#fep-ajax-output {
		background: rgba( 0, 0, 0, .8 );
		color: #fff;
		position: absolute;
		top: 100px;
		left: 240px;
		width: 400px;
		z-index: 2;
		padding: 1em;
	}
	#fep-ajax-output button {
		position: fixed;
	}
	#fep-ajax-output button:nth-of-type( 2 ) {
		margin-left: 285px;
	}
	#fep-ajax-output ol {
		margin-top: 2em;
	}
	</style>
	<?php
} );

// Include the JS to load/display the comments. Default to doing it in the head,
// optionally do it in the foot.
$ajax_where = isset( $_GET['fep-ajax-foot'] ) ? 'wp_footer' : 'wp_head';
add_action( $ajax_where, function() {
	if ( isset( $_GET['fep-bootstrap-js'] ) ) {
		$data = get_comments( array( 'number' => 20 ) );
		$response = array( 'success' => true, 'data' => $data );
		echo '<script type="text/data" id="fep-bootstrap-data">' . json_encode( $response ) . '</script>';
	}
	?>
	<script>
	(function($) {
		var items = [];

		function do_output( data ) {
			// Set up a container
			$( 'body' ).append( '<div id="fep-ajax-output"><button id="hide">Hide</button> &nbsp; <button id="remove">Remove</button></div>' );

			// And render our items into the container (using variable functions per PHP)
			render_items( data );
			console.log( 'Rendered ' + data.data.length + ' comments, 50 times each.' );

			// Temporarily remove the comment list, then put it back in a few seconds
			$( '#hide' ).click( function() {
				var wrap = document.querySelector( '#fep-ajax-output' ),
					ol = document.querySelector( '#fep-ajax-output ol' );
				wrap.removeChild( ol );
				wrap.parentNode.removeChild( wrap );
				setTimeout( do_output, 3000, data );
			} );

			// Permanently remove the comment list and container
			$( '#remove' ).click( function() {
				$( '#fep-ajax-output' ).remove();
			} );
		}

		// Render items according to either a single, string-based modification,
		// or a series of DOM manipulations.
		<?php if ( isset( $_GET['fep-single-dom-edit'] ) ) : ?>
			function render_items( data ) {
				// Build an entire string for all comments
				var html = '<ol>';
				for ( i = 0; i < 50; i++ ) {
					$.each( data.data, function( index, comment ) {
						html += '<li>At ' + comment.comment_date_gmt + ', ' + comment.comment_author + ' said ' + comment.comment_content + '</li>';
					} );
				}
				html += '</ol>';

				// Add our built string in a single DOM manipulation
				$( '#fep-ajax-output' ).append( html );
			}
		<?php else : ?>
			function render_items( data ) {
				// Add an empty list
				$( '#fep-ajax-output' ).append( '<ol></ol>' );

				var list = document.querySelector( '#fep-ajax-output ol' );

				// Loop through all data and add it to the output
				for ( i = 0; i < 50; i++ ) {
					$.each( data.data, function( index, comment ) {
						var item = document.createElement( 'li' );
						item.appendChild( document.createTextNode( 'At ' + comment.comment_date_gmt + ', ' + comment.comment_author + ' said ' + comment.comment_content + '' ) );

						// Storing a reference to these children creates the leak
						items.push( list.appendChild( item ) );
					} );
				}
			}
		<?php endif; ?>

		<?php
		// Optionally load data "inline" to bootstrap things
		if ( isset( $_GET['fep-bootstrap-js'] ) ) { ?>
			var data = $.parseJSON( $( '#fep-bootstrap-data' ).text() );
			jQuery(document).ready(function($) {
				// Needs to be delayed until the DOM is built out
				do_output( data );
			});
			<?php
		} else {
			?>var data = $.getJSON( '<?php echo home_url( '?fep-ajax-data' ); ?>', do_output );<?php
		}
		?>
	})(jQuery);
	</script>
	<?php
} );
