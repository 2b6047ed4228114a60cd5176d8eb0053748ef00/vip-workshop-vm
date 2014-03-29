var system = require( 'system' );
var webpage = require( 'webpage' );

setTimeout( function() {
	phantom.exit( 2 );
}, 20000 );

exports.url = ( system.env.WP_HOME || '' ).replace( /\/$/, '' ) + '/';
exports.dir = ( system.env.WP_ROOT || system.env.PWD ).replace( /\/$/, '' ) + '/';

exports.fix_url = function( text ) {
	return text.replace( /@@@WP_HOME@@@/g, exports.url.replace( /\/$/, '' ) );
}

exports.page = function( name ) {
	var page = webpage.create();

	name = name || 'PAGE';

	page.onConsoleMessage = function( msg ) {
		console.log( name + ':', msg );
	};

	page.onError = function( msg, trace ) {
		var msgStack = [ name + ' ERROR: ' + msg ];

		if ( trace && trace.length ) {
			msgStack.push( 'TRACE:' );
			trace.forEach( function( t ) {
				msgStack.push( ' -> ' + t.file + ': ' + t.line + ( t.function ? ' (in function "' + t.function + '")' : '' ) );
			} );
		}

		console.error( msgStack.join( '\n' ) );
	};

	return page;
}
