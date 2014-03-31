<?php
/*
Plugin Name: FEP: Slow Server
Description: Super-simple simulation of a slow server by sleeping for 2 seconds on every request.
Author: Automattic
Author URI: http://automattic.com/
*/

/*

EXAMPLE OPERATION
=================

0. Enable the plugin
1. Observe that all non-admin requests take 2 extra seconds to respond

*/

if ( !is_admin() )
	sleep( 2 );
