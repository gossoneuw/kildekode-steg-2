<?php 

session_start();
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
$transport = new Gelf\Transport\UdpTransport("158.39.188.203", 12201);
$publisher = new Gelf\Publisher($transport);
$handler = new GelfHandler($publisher,Logger::DEBUG);
$logger->pushHandler($handler);

if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/database.php";

    $sql_query = "SELECT * FROM user 
                  WHERE id = {$_SESSION["user_id"]}";

    $query_result = $mysqli->query($sql_query);

    $user = $query_result->fetch_assoc();

    $avsender = $user["id"];

}

$innhold = htmlspecialchars($_POST["innhold"]);
$emneid = htmlspecialchars($_POST["emneid"]);

if (empty($innhold)) {
    die("Message is required");
}

$rapstat = 1;
$msgtype = 1;
// kobler til databasen, og lagrer "kobling" i objekt
$mysqli = require  __DIR__ . "/database.php";
// placeholder verdier for insert into
$sql_insert = "INSERT INTO meldingv2 (innhold, avsender, emneid, rapstat, msgtype) VALUES (?, ?, ?, ?, ?)";

// initialiserer insert, ser etter injections og andre error
$stmt = $mysqli->stmt_init();

// forbereder sgl INSERT - return false om det er feil på insert
if (!$stmt->prepare($sql_insert)) {
    die("SQL error: Hei på deg ");

}

// binder verdier til placeholder verdier i INSERT stmt(sss - argument som spesifiserer datatyper s = string)
$stmt->bind_param("siiii", $innhold, $avsender, $emneid, $rapstat, $msgtype);

if (preg_match_all('[\/|\$|"|\/|<|>]', $innhold)) {
                        $error = array("Illegal characters used during commenting from logged in user. comment used: ", $innhold);
                        $error = json_encode($error);
                        $logger->warning($error);
                        die("sql error: kaffetyven");
                }
$stmt->execute();
header("Location: index.php");
exit();


?>
