<?php

// This file is super secret! Don't let anyone read it!

define('DB_NAME', 'frontend');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

$table_prefix  = 'fep_';

define('AUTH_KEY',         'p%J;Rw$)WoAaa$PF@Hd I>Wxvn;_LhtgcOw6R(|XL~dBMqT@D_N-SsT[.|W&PC&|');
define('SECURE_AUTH_KEY',  ':WMg~URB)F,Z3Zd6+U{Vo?=a5H8^dwwwf,&mYU)V}y+cHnL4ZZD}D,7@j:hrBlEO');
define('LOGGED_IN_KEY',    'TGN8)&)@?^Jj:Xj:p!|#9dMl.fL]].3AdGoTJY|DSa#zm;L=Tug+<S0-G|eDM]zD');
define('NONCE_KEY',        'l~||fM_%RHV&hS?nMn0- ixpLh)n$dUTI.{z.,-ZLO{Ag:iVJ -itEG(KDnZQX$F');
define('AUTH_SALT',        'SUPERSECRET');
define('SECURE_AUTH_SALT', ' U+BH.bq+{Uw+bI4< _.%bPsH5>j8PS}P+MXk1;jMPCNL(GNo(n9`/,N Ze74BYe');
define('LOGGED_IN_SALT',   'CUYx^#<MEh(nP:h,]SFoMIia~f)`/XXMahPQ]0.MkDdabMDy%d=!-!p+;]:=}Cg]');
define('NONCE_SALT',       '9?)NYI[_*D(E-PnX%b_C->YnAE~&l7yQ=j#d&dWgBT?(*5z;Fn<7O)0)=ds289l#');

define('WPLANG', '');

define('WP_HOME', 'http://vip-workshop.dev/front-end-performance');
define('WP_SITEURL', WP_HOME . '/wordpress' );

define('WP_CONTENT_DIR', __DIR__ . '/wp-content');
define('WP_CONTENT_URL', WP_HOME . '/wp-content');


if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');

error_reporting( E_ALL & ~E_DEPRECATED );
