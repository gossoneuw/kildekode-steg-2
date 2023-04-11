<?php
require __DIR__ . '/vendor/autoload.php';
include 'logging.php';
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
$transport = new Gelf\Transport\UdpTransport("158.39.188.203", 12201);
$publisher = new Gelf\Publisher($transport);
$handler = new GelfHandler($publisher,Logger::DEBUG);
$logger->pushHandler($handler);

/*$logger = new Logger('test_channel');
$logger->pushHandler(new StreamHandler(__DIR__ . '/testlog/test.log', Logger::DEBUG));*/
// validerer navn
// sjekker om input er tom
if (empty($_POST["name"])) {
	die("Name is required");
}
if (preg_match_all('[\/|\$|"|\/|<|>|\(|\)]', $_POST['name'])) { //Sjekker hele navnet om det inneholder noen av tegnene i regulær uttrykket.
	$error = array("Illegal characters used during foreleser registration. Name used: ", $_POST['name']); //Et array med feilmeldingen
	$error = json_encode($error); //Må bruke json encode for å få variabelen det er et problem med inn i loggen.
	$logger->warning($error); //Sender loggen til graylog
	die("Name contains illegal characters"); //Avslutter spørring
	//Bør logges da et navn bør aldri inneholde noen av disse tegnene.
}
// validererer e-post
// bruker funksjonen filter_var for å sjekke om input inneholder gyldig epost-adresse
if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
	die("Valid email is required");
	//Bør også logges da kravet til en E-post er ganske standard
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

$zero = 0; //Godkjent status (Brukes bare for å sette for å sette godkjent status til 0
if (($_FILES['img']['name']!="")){ //Sjekker om fil er lastet opp.
	$target_dir = "brukerbilde/"; //Mappen filen blir lagret
	$file = $_FILES['img']['name'];
	$path = pathinfo($file);
	$filename = rand(1, 99999999); //generer et tilfeldig tall mellom verdiene som er oppgitt og setter det som navnet. Dette er også er en ganske dårlig løsning, men man skal (i teorien) laste opp en god del bilder før man skulle ha så uflaks å få samme navn
	$ext=strtolower(end(explode('.',$_FILES['img']['name']))); //Henter fil etternavnet.
	$temp_name = $_FILES['img']['tmp_name']; //Det midlertidige navnet
	$path_filename_ext = $target_dir.$filename.".".$ext; //Hvor bilde blir lagret
	$full_filename = $filename.".".$ext;
	$size = $_FILES['img']['size'];
}
/*if (!preg_match_all("\b[php]/i", $full_filename)) { //Sjekker om filnavnet innholder teskten php
	die("Invalid file");
}*/
if(strlen($full_filename)>0){ //Ganske dårlig løsning, men dette er en måte å sjekke om en fil ble lastet opp slik at du ikke må laste opp et bilde for å registrere en foreleser.
	if(preg_match('(^.*\.(?!jpg$|png$)[^.]+$)', $full_filename)){ //Sjekker om filnavnet ender på .jpg eller .png og hvis det ikke gjør det avslutter det registreringen.
		$error = array("Non jpg/png uploaded in foreleser registration. Potential malicous file. The file in question:", $full_filename);
		$error = json_encode($error);
		$logger->warning($error);
		die("Invalid file");
	}
}
if($size>5242880){ //Sjekker om filen som blir lastet opp er over 5MB. Av en eller annen grunn får jeg feilmelding "Name is required" når jeg prøver å laste opp en fil på 12MB. Har ingen annelse hvorfor. Trenger hjelp her.
	$error = array("File surpassing 5MB during foreleser registration. Potential DOS? Size of uploaded file is: ", $size);
	$error = json_encode($error);
	$logger->notice($error);
	die("File size surpasses upper limit.");
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
	//Bør logges da dette skal i teorien ikke gå feil
}

// binder verdier til placeholder verdier i INSERT stmt(sss - argument som spesifiserer datatyper s = string)
$stmt->bind_param("sss",
				  $_POST["name"],
				  $_POST["email"],
				  $password_hash);

// Hvor bildene skal bli lagret
// håndterer duplikat email
try {
	$mysqli->autocommit(FALSE);
	$stmt->execute();
	$last_id = mysqli_insert_id($mysqli); //Henter ID-en til forrige query som i dette tilfellet er id fra forrige statement.
	$stmt2 = $mysqli->prepare("INSERT INTO foreleserv2 (foreleserid, godkjent, idemne, bildeid) VALUES (?,?,?,?)");
	move_uploaded_file($temp_name,$path_filename_ext);
	$stmt2->bind_param('iiis', $last_id,$zero, $_POST["Emne"], $full_filename);
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
