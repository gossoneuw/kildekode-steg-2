<?php
    $is_invalid = false;
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require  __DIR__ . "/database.php";

    $pin_query =  sprintf("SELECT * FROM emne
                  WHERE pin = '%s'",
                  $mysqli->real_escape_string($_POST["pin"]));

    $query_result = $mysqli->query($pin_query);

    $pin = $query_result->fetch_assoc();

    $pin_code = $pin["pin"];
function getPin($mysqli, $pin_id){
	$pinQuery = "SELECT pin FROM emne where idemne = $pin_id";
	$pinResult= $mysqli->query($pinQuery);
	$pinFetch = $pinResult->fetch_assoc();
	$pinPin = $pinFetch['pin'];
	return $pinPin;
}
	if ($pin) {

        if ($pin_code==getPin($mysqli, 1)) {
            header("Location: Datasikkerhet.php");
        }

        elseif ($pin_code == getPin($mysqli, 2)) {
            header("Location: programmering1.php");
        }

         elseif ($pin_code == getPin($mysqli, 3)) {
            header("Location: programmering2.php");
        }

         elseif ($pin_code == getPin($mysqli, 14)) {
            header("Location: diskretmatematikk.php");
        }

        exit();

        }
        $is_invalid=true;
    }

?>

<!DOCTYPE html>
 <!DOCTYPE html>
 <html>
 <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
    <title>Emne Login</title>
 </head>
 <body>
    <a href="index.php"><img src="#" alt="Home Button" style="width:64px;height:56px;"></a>
    <h1><a href="index.php" style="color:white">Skriv inn PIN for emne</a></h1>

    <?php if ($is_invalid):?>
        <p style="color: indianred; font-size:80%;">invalid login</p>
    <?php endif; ?>

    <form method="post">
        <label for="pin">Pin for emne:</label>
        <input type="number" name="pin" id="pin" maxlength="4" required value="<?php echo htmlspecialchars($pin); ?>">
        <input type="submit" value ="Logg inn">
    </form>

</body>
</html>
