<?php
// First authored by Brian Cray
// License: http://creativecommons.org/licenses/by/3.0/
// Contact the author at http://briancray.com/

// Heavily modified by
// Fernando L. Canizo - http://flc.muriandre.com/
?>
<!DOCTYPE html>
<html>
<title>URL shortener</title>
<meta name="robots" content="noindex, nofollow">
</html>
<body>
<form method="post" action="shorten.php" id="shortener">
<label for="longurl">URL to shorten</label> <input type="text" name="longurl" id="longurl"> <input type="submit" value="Shorten">
</form>
</form>
<script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>
<script>
$(function () {
	$('#shortener').submit(function () {
		$.ajax({data: {longurl: $('#longurl').val()}, url: 'shorten.php', complete: function (XMLHttpRequest, textStatus) {
			$('#longurl').val(XMLHttpRequest.responseText);
		}});
		return false;
	});
});
</script>
</body>
</html>
