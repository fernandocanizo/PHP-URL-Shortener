<?php
// First authored by Brian Cray
// Contact the author at http://briancray.com/

// Heavily modified by
// Fernando L. Canizo - http://flc.muriandre.com/

$get_url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_STRING);

if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $get_url)):
	die('That is not a valid short url'); // TODO return proper JSON
endif;

require_once('config.php');
require_once('Lib.php');


$shortened_id = Lib::getIDFromShortenedURL($get_url);

$throw_filter = '';
if (THROWAWAY_URLS) :
    $throw_filter = ' and urls_referrals = 0';
endif;

if(CACHE):
	$safeShortenedId = $mysqli->real_escape_string($shortened_id);
	$long_url = file_get_contents(CACHE_DIR . $shortened_id);
	if(empty($long_url) || !preg_match('|^https?://|', $long_url)):
		$query = 'select urls_long from ' . DB_TABLE . ' where urls_id = "' . $safeShortenedId . '"' . $throw_filter;

		if(false === ($myResult = $mysqli->query($query))):
			die("Select query failed: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
		endif;

		$row = $myResult->fetch_assoc();
		$myResult->free();

		$long_url = $row['urls_long'];
		$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
		fwrite($handle, $long_url);
		fclose($handle);
	endif;

else:
	$query = 'select urls_long from ' . DB_TABLE . ' where urls_id = "' . $safeShortenedId . '"' . $throw_filter;
	if(false === ($myResult = $mysqli->query($query))):
		die("Select query failed: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
	endif;

	$row = $myResult->fetch_assoc();
	$myResult->free();
	$long_url = $row['urls_long'];
endif;

if(TRACK):
	$query = 'update ' . DB_TABLE . ' set urls_referrals = urls_referrals + 1 where urls_id = "' . $safeShortenedId . '"';
	if(false === $mysqli->query($query)):
		die("Couldn't update referrals: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
	endif;
endif;

header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $long_url);
exit;
