<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

ini_set('display_errors', 0);

if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url']))
{
	die('That is not a valid short url');
}

require('config.php');

$shortened_id = getIDFromShortenedURL($_GET['url']);

if(CACHE)
{
	$safeShortenedId = mysqli->real_escape_string($shortened_id);
	$long_url = file_get_contents(CACHE_DIR . $shortened_id);
	if(empty($long_url) || !preg_match('|^https?://|', $long_url))
	{
		$query = 'select long_url from ' . DB_TABLE . ' where id="' . $safeShortenedId . '"';

		if(false === ($myResult = $mysqli->query($query))):
			die("Select query failed: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
		endif;

		$row = $myResult->fetch_assoc();
		$myResult->free();

		$long_url = $row['']; // TODO update with new columns names
		$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
		fwrite($handle, $long_url);
		fclose($handle);
	}
}
else
{
	$query = 'select long_url from ' . DB_TABLE . ' where id="' . $safeShortenedId . '"';
	if(false === ($myResult = $mysqli->query($query))):
		die("Select query failed: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
	endif;

	$row = $myResult->fetch_assoc();
	$myResult->free();
	$long_url = $row['']; // TODO put proper column name
}

if(TRACK)
{
	$query = 'update ' . DB_TABLE . ' set referrals = referrals + 1 where id = "' . $safeShortenedId . '"';
	if(false === $mysqli->query($query)):
		die("Couldn't update referrals: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
	endif;
}

header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $long_url);
exit;

function getIDFromShortenedURL ($string, $base = ALLOWED_CHARS)
{
	$length = strlen($base);
	$size = strlen($string) - 1;
	$string = str_split($string);
	$out = strpos($base, array_pop($string));
	foreach($string as $i => $char)
	{
		$out += strpos($base, $char) * pow($length, $size - $i);
	}
	return $out;
}
