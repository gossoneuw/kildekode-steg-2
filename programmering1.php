<?php
    include 'database.php';
    include 'savecomment.php';


    function addreply($mysqli, $idmelding) {
        echo "<form method='POST' action='savecomment.php'>
        <input type='hidden' name='avsender' value='103'>
        <input type='hidden' name='emneid' value='2'>
        <input type='hidden' name='rapstat' value='1'>
        <input type='hidden' name='til' value='$idmelding'>
        <input type='hidden' name='msgtype' value='2'>
        <textarea name='innhold'></textarea>
        <button type='submit' name='commentSubmit'>Comment</button>
        </form>";
}

    function getComments($mysqli) {
        $sql = "SELECT * FROM meldingv2 where emneid = 2 and msgtype = 1";
        $result = $mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
        $idmelding = $row['idmelding'];
        echo "<p>Anonymous student:</p>";
        echo $row['innhold']."<br>";
	getForeleserMelding($mysqli, $idmelding);
        getReplies($mysqli, $idmelding);
        addreply($mysqli, $idmelding);
    }}

    function getReplies($mysqli, $idmelding) {
        $sql = "SELECT * FROM meldingv2 WHERE til = $idmelding and msgtype = 2";
        $result = $mysqli->query($sql);
        while ($replymessage = $result->fetch_assoc()){
		echo "<p> Gjest: ". $replymessage['innhold']." </p>";
    }}
    function getForeleserMelding($mysqli, $idmelding) {
        $sql = "SELECT * FROM meldingv2 WHERE til = $idmelding and msgtype = 3";
        $result = $mysqli->query($sql);
        while ($replymessage = $result->fetch_assoc()){
                echo "<p> Forelser: ". $replymessage['innhold']." </p>";
    }}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
    <title>Password protected</title>
</head>
<body>
    <div style="text-align:center;margin-top:50px;">
        you have come to Programmering 1.
    </div>
	<?php
		getComments($mysqli);
	?>
</body>
</html>
