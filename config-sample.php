<?php
// First authored by Brian Cray
// Contact the author at http://briancray.com/

// Heavily modified by
// Fernando L. Canizo - http://flc.muriandre.com/


$serverName = ''; // use this to get different settings for production or development machine


if($serverName === $_SERVER['HTTP_HOST']):
	// show everything on development
	ini_set('display_errors', '1');
	error_reporting(-1);

	////////////////////////////////////////////////////////////////////////////////
	// You may want to fill these if you're developing ushort and not just using it
	////////////////////////////////////////////////////////////////////////////////
	define('DB_NAME', '');
	define('DB_USER', '');
	define('DB_PASSWORD', '');
	define('DB_HOST', 'localhost');
	define('DB_TABLE', 'urls');

else:
	// do not show errors on production
	ini_set('display_errors', '0');
	error_reporting(0);

	////////////////////////////////////////////////////////////////////////////////
	// YOU NEED TO FILL THESE
	////////////////////////////////////////////////////////////////////////////////
	define('DB_NAME', '');
	define('DB_USER', '');
	define('DB_PASSWORD', '');
	define('DB_HOST', 'localhost');
	define('DB_TABLE', 'urls');
endif;


// let's use UTC timezone
date_default_timezone_set('Europe/London');


// connect to database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if($mysqli->connect_error):
	die("Can't connect to database: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
endif;

// base location of script (include trailing slash)
define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/');

// change to limit short url creation to a single IP
define('LIMIT_TO_IP', $_SERVER['REMOTE_ADDR']);

// change to TRUE to start tracking referrals
define('TRACK', FALSE);

// check if URL exists first
define('CHECK_URL', FALSE);

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
define('CACHE', TRUE);

// if so, where will the cache files be stored? (include trailing slash)
define('CACHE_DIR', dirname(__FILE__) . '/cache/');

// all URLS will be one-use only. Requires TRACK to be set to true
define('THROWAWAY_URLS', true);