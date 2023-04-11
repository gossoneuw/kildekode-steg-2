<?php
require __DIR__ . '/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\LogglyHandler;
use Monolog\Formatter\LogglyFormatter;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\GelfHandler;
use Gelf\Message;
use Monolog\Formatter\GelfMessageFormatter;
$logger = new Logger('Security');
$transport = new Gelf\Transport\UdpTransport("158.39.188.203", 12201 /*,Gelf\Transport\UdpTransport::CHUNK_SIZE_LAN*/);
$publisher = new Gelf\Publisher($transport);
$handler = new GelfHandler($publisher,Logger::DEBUG);
$logger->pushHandler($handler);

// $_POST = variabel som holder på verdien fra spesifisert input ($_POST["navn"])


// validerer navn
// sjekker om input er tom
if (empty($_POST["name"])) {
	die("Name is required");
}
if (preg_match_all('[\/|\$|"|\/|<|>|\(|\)]', $_POST['name'])) {
        $logger->warning("Illegal characters used in name during student registration");
	die("Name contains illegal charaacters");
        //Bør logges da et navn bør aldri inneholde noen av disse tegnene.
}
if (empty($_POST["studieretning"])) {
	die("Studieretning is required");
}
if (preg_match('[\/|\$|"|\/|<|>|\(|\)]', $_POST['studieretning'])) {
	$logger->warning("Illegal characters used in studieretning during student registration");
        die("Studieretning contains illegal charaacters");
        //Bør logges da en studieretning bør aldri inneholde noen av disse tegnene.
}
if (empty($_POST["studiekull"])) {
	die("Studiekull is required");
}
if (preg_match('[\/|\$|"|\/|<|>|\(|\)]', $_POST['studiekull'])) {
	$logger->warning("Illegal characters used in studiekull during student registration");
        die("Studiekull contains illegal charaacters");
        //Bør logges da et studiekull bør aldri inneholde noen av disse tegnene.
}

// validererer e-post
// bruker funksjonen filter_var for å sjekke om input inneholder gyldig epost-adresse
if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
	die("Valid email is required");
}

// validerer passord
// Må inneholde minimum 4 tegn: minimum 1 bokstav og 1 tall
if (strlen($_POST["password"]) < 4) {
	die("Password must consist of at least 4 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
	die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/i", $_POST["password"])) {
	die("Password must contain at least one number");
}

// sjekk om "confirm password" = "password"
if ($_POST["password"] !== $_POST["password_confirmation"]) {
	die("Password must match");
}

// hasher passord og lagrer det som en string i $password_hash variable
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// kobler til databasen, og lagrer "kobling" i objekt
$mysqli = require  __DIR__ . "/database.php";


// placeholder verdier for insert into
$sql_insert = "INSERT INTO user (name, email, password_hash) 
		VALUES (?, ?, ?)";
// initialiserer insert, ser etter injections og andre error
$stmt = $mysqli->stmt_init();

// forbereder sgl INSERT - return false om det er feil på insert
if (!$stmt->prepare($sql_insert)) {
	die("SQL error: " . $mysqli->error);
}
// binder verdier til placeholder verdier i INSERT stmt(sss - argument som spesifiserer datatyper s = string)
$stmt->bind_param("sss",
				  $_POST["name"],
				  $_POST["email"],
				  $password_hash);
// Starter en transaksjon
// håndterer duplikat email
try {
	$mysqli->autocommit(FALSE);
	$stmt->execute();
	$last_id = mysqli_insert_id($mysqli);
	$stmt2 = $mysqli->prepare("INSERT INTO studentv2 (idstudent, studieretning, studiekull) VALUES (?, ?, ?)");
	$stmt2->bind_param('iss', $last_id, $_POST["studieretning"], $_POST["studiekull"]);
	$stmt2->execute();
	header("Location: signup-success.html");
	$mysqli->autocommit(TRUE);
	exit;

} catch (mysqli_sql_exception $e) {
	if ($mysqli->errno === 1062) {
		die("email already taken");
	}
	else {
		die($mysqli->error . " " . $mysqli->errno); 
	}
}
?>
