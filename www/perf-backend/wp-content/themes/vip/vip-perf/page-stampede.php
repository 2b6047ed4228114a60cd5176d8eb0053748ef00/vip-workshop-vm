<?php
/**
 * Template Name: Stampede
 */

get_header(); ?>

<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<button id="start-stampede" style="width: 100%; display: block;">Start Stampede</button>
			<ul id="latest" style="float: left; width:40%; padding: 2%; margin: 2%; border: 2px dotted green">
				<li>This list is results from ongoing AJAX polling running every n seconds</li>
			</ul>
			<ul id="stampede" style="float: right; width:40%; padding: 2%; margin: 2%; border: 2px dotted blue">
				<li>This is our stampede! A flood of requests for a slow process.</li>
			</ul>
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- #main-content -->

<script>
jQuery( function( $ ) {
	function pbe_get_latest() {
		var start_time = new Date();
		$.ajax( {
			url: '/perf-backend/latest.json',
			method: 'GET',
			dataType: 'json',
			success: function( data ) {
				var elapsed_time = pbe_elapsed_time( start_time );
				var message = 'yay: ' + data.value + '! (' + elapsed_time + 's)';
				pbe_update_latest( message );
			},
			error: function( data ) {
				var elapsed_time = pbe_elapsed_time( start_time );
				var message = 'fail! (' + elapsed_time + 's)';
				pbe_update_latest( message );
			}
		} );
	}

	function pbe_update_latest( message ) {
		$('<li/>').html( message ).appendTo( $( '#latest' ) );
	}

	function pbe_get_slow( index ) {
		var start_time = new Date();
		$.ajax( {
			url: '/perf-backend/slow.json?i=' + index,
			type: 'GET',
			dataType: 'json',
			success: function( data ) {
				var elapsed_time = pbe_elapsed_time( start_time );
				var message = '#' + index + ' || value: ' + data.value + ' || cache_miss: ' + data.cache_miss + ' (' + elapsed_time + 's)';
				pbe_update_slow( message );
			},
			error: function( data ) {
				var elapsed_time = pbe_elapsed_time( start_time );
				var message = 'fail! (' + elapsed_time + 's)';
				pbe_update_slow( message );
			}
		} );
	}

	function pbe_update_slow( message ) {
		$('<li/>').html( message ).appendTo( $( '#stampede' ) );
	}

	function pbe_elapsed_time( start ) {
		var end = new Date(),
			diff = ( end - start ) / 1000,
			seconds = Math.round( diff % 60 );

		return seconds;
	}

	var index = 0,
		is_stampeding = false;
		stampede_interval = false;

	setInterval( pbe_get_latest, <?php echo intval( LATEST_INTERVAL ) ?> );

	jQuery( '#start-stampede' ).on( 'click', function() {
		if ( ! is_stampeding ) {
			is_stampeding = true;
			$( this ).html( 'Stop Stampeding' );
			stampede_interval = setInterval( function() {
				index++;
				console.log( 'stampede request #' + index );
				pbe_get_slow( index );
			}, <?php echo intval( STAMPEDE_INTERVAL ); ?> );
		} else {
			clearInterval( stampede_interval );
			is_stampeding = false;
			$( this ).html( 'Start Stampeding' );
			index = 0;
		}
	} );
} );
</script>

<?php
get_footer();
