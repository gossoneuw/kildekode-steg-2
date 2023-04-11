<?php


session_start();
include 'database.php';

include 'savecomment.php';


if(isset($_SESSION["user_id"])) {
	$mysqli = require __DIR__ . "/database.php";

	$sql_query = "SELECT * FROM foreleserv2 WHERE foreleserid = {$_SESSION['user_id']}";
	$namequery = "SELECT * FROM user WHERE id = {$_SESSION['user_id']}";
	$query_result = $mysqli->query($sql_query);
	$user = $query_result -> fetch_assoc();
	$nameresult = $mysqli->query($namequery);
	$nameget = $nameresult ->fetch_assoc();
	$emneid = $user["idemne"];
}
?>
<!DOCTYPE html>
 <!DOCTYPE html>
 <html>
 <head>
 	<meta charset="utf-8">
 	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
 	<title>Home</title>
 </head>
 <body>

 	<?php if (isset($user)): ?>
	 	<h1>Her er meldingene for emne du har.</h1>
	
 		<p>Hei <?= htmlspecialchars($nameget["name"]) ?></p>

 		<p><a href="logout.php">Log out</a></p>
	<?php
		$img = $user["bildeid"];
	?>
	<img src="brukerbilde/<?php echo $img; ?>">
 	<?php else: ?>

 		<h1>Enten har du glemt å logge inn eller så skal du ikke være her. Hvem av de er du?</h1>
		<h3><a href="/steg1">Gå tilbake til startside.</a></h3>
 	<?php endif; ?>
	<?php

	function getComments($mysqli, $emneid) {
        	$sql = "SELECT * FROM meldingv2 where emneid = $emneid and msgtype = 1";
        	$result = $mysqli->query($sql);
        	while ($row = $result->fetch_assoc()) {
        		$idmelding = $row['idmelding'];
        		echo "<p>Anonymous student:</p>";
        		echo $row['innhold']."<br>";
			addreply($mysqli, $emneid, $idmelding);
    }}

	function addreply($mysqli, $emneid, $idmelding) {
        	echo "<form method='POST' action='savecomment.php'>
        	<input type='hidden' name='avsender' value='{$_SESSION['user_id']}'>
        	<input type='hidden' name='emneid' value='$emneid'>
        	<input type='hidden' name='rapstat' value='1'>
        	<input type='hidden' name='til' value='$idmelding'>
        	<input type='hidden' name='msgtype' value='3'>
        	<textarea name='innhold'></textarea>
        	<button type='submit' name='commentSubmit'>Comment</button>
        	</form>";
}

	getComments($mysqli, $emneid);
	
	?>
 </body>
 </html>
