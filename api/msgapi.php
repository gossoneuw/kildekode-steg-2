<?php

require '../database.php';
session_start();
require '../vendor/autoload.php';
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


$sql_query = "SELECT * FROM studentv2 WHERE idstudent = {$_SESSION['user_id']}";
$namequery = "SELECT * FROM user WHERE id = {$_SESSION['user_id']}";
$query_result = $mysqli->query($sql_query);
$user = $query_result -> fetch_assoc();
$nameresult = $mysqli->query($namequery);
$nameget = $nameresult ->fetch_assoc();



$stmtquery= "INSERT INTO meldingv2 (innhold, avsender, emneid, rapstat, msgtype) VALUES (?,?,?,?,?)";
if (!$stmt=$mysqli->prepare($stmtquery)) {
	die("sql error: hei");
}
$stmt->bind_param('sssss', $innhold, $avsender, $emneid, $rapstat, $msgtype);
	$innhold = $_GET['innhold'];
	if (preg_match_all('[\/|\$|"|\/|<|>]', $innhold)) {
                        $error = array("Illegal characters used during commenting with api. comment used: ", $innhold);
                        $error = json_encode($error);
                        $logger->warning($error);
                        die("sql error: kaffetyven");
                }
	$avsender = $_SESSION['user_id'];
	$emneid = $_GET['emne'];
	$rapstat = 1;
	$msgtype = 1;


if($stmt->execute()){

    $response['error']="000";
    $response['message']="Message sent!";

}


else
{
die("SQL error: " . $mysqli->error);
  $response['error']="001";
  $response['message']="message failed!";
}

echo json_encode($response);
?>
