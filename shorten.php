<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */


require_once('config.php');
require_once('Lib.php');


ini_set('display_errors', 0);

$url_to_shorten = get_magic_quotes_gpc() ? stripslashes(trim($_REQUEST['longurl'])) : trim($_REQUEST['longurl']);

if(!empty($url_to_shorten) && preg_match('|^https?://|', $url_to_shorten))
{
	// check if the client IP is allowed to shorten
	if($_SERVER['REMOTE_ADDR'] != LIMIT_TO_IP)
	{
		die('You are not allowed to shorten URLs with this service.');
	}

	// check if the URL is valid
	if(CHECK_URL)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_to_shorten);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		$response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($response_status == '404')
		{
			die('Not a valid URL');
		}

	}

	// check if the URL has already been shortened
	$safeUrl = $mysqli->real_escape_string($url_to_shorten);
	if(false === ($myResult = $mysqli->query('SELECT urls_id FROM ' . DB_TABLE. ' WHERE urls_long = "' . $safeUrl . '"'))):
		die("Select query failed: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
	endif;

	$row = $myResult->fetch_assoc();
	$myResult->free();

	if(null !== $row):
		// URL has already been shortened
		$shortened_url = Lib::getShortenedURLFromID($row['urls_id']);

	else:
		// URL not in database, insert
		$safeRemoteAddress = $mysqli->real_escape_string($_SERVER['REMOTE_ADDR']);
		$query = 'insert into ' . DB_TABLE .
			' (urls_long, urls_created_on, urls_creator) values ("' .
			$safeUrl . '", "' . time() . '", "' . $safeRemoteAddress . '")';

		if(false === $mysqli->query($query)):
			die("Failed to insert new shortened url: (" . $mysqli->connect_errno . ') ' . $mysqli->connect_error); // TODO replace with proper JSON reply
		endif;
		$shortened_url = Lib::getShortenedURLFromID($mysqli->insert_id);
	endif;

	echo BASE_HREF . $shortened_url;
}
