<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

ini_set('display_errors', 0);

if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url']))
{
	die('That is not a valid short url'); // TODO return proper JSON
}

require_once('config.php');
require_once('Lib.php');


$shortened_id = Lib::getIDFromShortenedURL($_GET['url']);

if(CACHE)
{
	$safeShortenedId = mysqli->real_escape_string($shortened_id);
	$long_url = file_get_contents(CACHE_DIR . $shortened_id);
	if(empty($long_url) || !preg_match('|^https?://|', $long_url))
	{
		$query = 'select urls_long from ' . DB_TABLE . ' where urls_id = "' . $safeShortenedId . '"';

		if(false === ($myResult = $mysqli->query($query))):
			die("Select query failed: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
		endif;

		$row = $myResult->fetch_assoc();
		$myResult->free();

		$long_url = $row['urls_long'];
		$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
		fwrite($handle, $long_url);
		fclose($handle);
	}
}
else
{
	$query = 'select urls_long from ' . DB_TABLE . ' where urls_id = "' . $safeShortenedId . '"';
	if(false === ($myResult = $mysqli->query($query))):
		die("Select query failed: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
	endif;

	$row = $myResult->fetch_assoc();
	$myResult->free();
	$long_url = $row['urls_long'];
}

if(TRACK)
{
	$query = 'update ' . DB_TABLE . ' set urls_referrals = urls_referrals + 1 where urls_id = "' . $safeShortenedId . '"';
	if(false === $mysqli->query($query)):
		die("Couldn't update referrals: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
	endif;
}

header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $long_url);
exit;


