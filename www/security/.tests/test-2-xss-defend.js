#!/usr/bin/env phantomjs

var wp = require( './wp' );

var victimPage = wp.page( 'VICTIM' );
var maliciousPage = wp.page( 'MALICIOUS' );

function clearPoll( page ) {
	return function() {
		page.evaluate( function() {
			var clearIt = function() {
				if ( 'undefined' !== window.ajax_poll_interval ) {
					clearInterval( window.ajax_poll_interval );
				}
			}
			clearIt();
			setTimeout( clearIt, 200 );
		} );
	};
};

maliciousPage.onLoadFinished = clearPoll( maliciousPage );
victimPage.onLoadFinished = clearPoll( victimPage );

maliciousPage.open( wp.url, function() {
	maliciousPage.evaluate( function() {
		save_color( '#deface" /><br id="deface' );
	} );

	setTimeout( function() {
		phantom.clearCookies();

		victimPage.open( wp.url, function() {
			var exists = victimPage.evaluate( function() {
				return !!document.getElementById( 'deface' );
			} );

			if ( exists ) {
				console.log( "XSS Vulnerability Still Exists" );
				phantom.exit( 1 );
			} else {
				console.log( "XSS Vulnerability Successfully Closed" );
				phantom.exit();
			}
		} );
	}, 2000 );
} );
