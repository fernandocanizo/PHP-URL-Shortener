<?php
// First authored by Brian Cray
// License: http://creativecommons.org/licenses/by/3.0/
// Contact the author at http://briancray.com/

// Heavily modified by
// Fernando L. Canizo - http://flc.muriandre.com/
?>
<!DOCTYPE html>
<html>
	<head>
		<title>URL shortener</title>
		<meta name="robots" content="noindex, nofollow">
	</head>
	<body>
		<form method="post" action="shorten.php" id="shortener">
			<label for="longurl">URL to shorten:</label>
			<input type="text" name="longurl" id="longurl">

			<input type="submit" id="submitUrl" value="Shorten">
		</form>

		<div>
			<p id="resultText"></p>
			<a href="" id="resultLink"></a>
		</div>

		<script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>

		<script>
$(function () {
	$('#shortener').on('click', function () {
		$.ajax({
			data: { longurl: $('#longurl').val() },
			url: 'shorten.php',
			complete: function (XMLHttpRequest, textStatus) {
				$('#resultText').text(XMLHttpRequest.responseText);
				$('#resultLink').attr('href', XMLHttpRequest.responseText);
				$('#resultLink').text($('#longurl').val());
			}
		});

		return false;
	});
});
		</script>
	</body>
</html>
