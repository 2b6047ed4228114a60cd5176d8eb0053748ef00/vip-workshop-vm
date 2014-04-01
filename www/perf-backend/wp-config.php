<?php

// This file is super secret! Don't let anyone read it!

define('DB_NAME', 'perf_backend');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

$table_prefix  = 'perf_backend_';

define('AUTH_KEY',         'h$d.igxP(^M8[u)MdWrW*NmOkC$x;APvR4YpW3bK@.]vh)Lv=4J;Ky14*{!u?i+O');
define('SECURE_AUTH_KEY',  '-K5I3:Kj<1oY9|S/W&pakUeAL?)&1Q/Vlz|aMv1_P6{ORy $$|W9eYg1A&-q[Eo,');
define('LOGGED_IN_KEY',    'f+=t-lFGqIu!)1 )2Lh^vrDv-@4gi;OU@|]|ZQTLG&eXq]2I-W<*u>&RU5u:IU)Z');
define('NONCE_KEY',        '-/|,v3{7m/La(^Z5(FdBz])]!@!Wg:8(s^vQ(be>x{Bs]h<;JOkF=ujLP+>tAUm8');
define('AUTH_SALT',        'k}m#<Q+8/p`w:+t/_Ch.q?OC&Ax&g|q+h!5vuqrfK 4x4?IxM~p/2wCatZ%A6j8t');
define('SECURE_AUTH_SALT', 'p|y+}M@8vkvPE >X];>g4BBA)kTd!GS6X,M*-[>#29<umn4a(SL![ks|;7OgpJ{#');
define('LOGGED_IN_SALT',   '3Ib$VKOT^9B^2NpgLQ+z;%!4XTd}auZpH1sy71g-+23/<y+5}%0*7P;4GpJqua/!');
define('NONCE_SALT',       '<^ZtH3%,b/u`#@zVH63W2K*Ej?61-v8Re+BJwk+GB+ X,*+S6$+UglVVGX|My2g6');

define('WPLANG', '');

error_reporting( E_ALL & ~E_DEPRECATED );
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', true);

define('SAVEQUERIES', true);

define('WP_HOME', 'http://vip-workshop.dev/perf-backend');
define('WP_SITEURL', WP_HOME . '/wordpress' );

define('WP_CONTENT_DIR', __DIR__ . '/wp-content');
define('WP_CONTENT_URL', WP_HOME . '/wp-content');

require( __DIR__ . '/config/batcache-config.php' );

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');

