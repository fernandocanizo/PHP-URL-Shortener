<?php
// Creation Date: 18 Dec 2014
// Author: Fernando L. Canizo - http://flc.muriandre.com/


abstract class Lib {
	// General useful functions packed up into a class for better portability

	public static function echoJson($status, $message, $result = null) {
		// $result defaults to null so this function can be used for errors, when there's no result

		$toReturn = array(
			'status' => ($status)? true : false,
			'message' => $message, // string meant to be logged to console for development purposes
			'result' => $result // anything, including UI messages
		);

		header('Content-type: application/json');
		echo json_encode($toReturn);
		exit; // after returning a json object we have to finish the script
	}


	public static function getShortenedURLFromID($integer, $base = ALLOWED_CHARS) {
		$length = strlen($base);
		while($integer > $length - 1):
			$out = $base[fmod($integer, $length)] . $out;
			$integer = floor( $integer / $length );
		endwhile;

		return $base[$integer] . $out;
	}


	public static function getIDFromShortenedURL($string, $base = ALLOWED_CHARS) {
		$length = strlen($base);
		$size = strlen($string) - 1;
		$string = str_split($string);
		$out = strpos($base, array_pop($string));
		foreach($string as $i => $char):
			$out += strpos($base, $char) * pow($length, $size - $i);
		endforeach;

		return $out;
	}

} // class ends
