<?php
include 'database.php';
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

//$mysqli = require __DIR__ . "/database.php"
if (isset($_POST['commentSubmit'])) {
	//$innhold = $_POST['innhold'];
	//$avsender = $_POST['avsender'];
	//$emneid = $_POST['emneid'];
	//$rapstat = $_POST['rapstat'];
	//$msgtype = $_POST['msgtype'];
	//$til = $_POST['til'];

	//$sql = "INSERT INTO melding (innhold, rapportert, student_idstudent, emne_idemne) VALUES ('$til', '1', '1', '1')";
	//$sql = "INSERT INTO meldingv2 (innhold, avsender, emneid, rapstat, msgtype, til) VALUES ('$innhold', '$avsender', '$emneid', '$rapstat', '$msgtype', '$til')";
        //$result = $mysqli->query($sql);

	$stmtquery = "INSERT INTO meldingv2 (innhold, avsender, emneid, rapstat, msgtype, til) VALUES (?,?,?,?,?,?)";
	if (!$stmt=$mysqli->prepare($stmtquery)) {
		die("sql error: hei");
	}
	$stmt->bind_param('ssssss', $innhold, $avsender, $emneid, $rapstat, $msgtype, $til);
		$innhold = $_POST['innhold'];
		if (preg_match_all('[\/|\$|"|\/|<|>]', $innhold)) {
			$error = array("Illegal characters used during commenting guest. comment used: ", $innhold);
			$error = json_encode($error);
			$logger->warning($error);
			die("sql error: kaffetyven");
		}
        	$avsender = $_POST['avsender'];
        	$emneid = $_POST['emneid'];
        	$rapstat = $_POST['rapstat'];
        	$msgtype = $_POST['msgtype'];
        	$til = $_POST['til'];
	if ($stmt->execute()) {
		echo "lagret!";
	}
        //echo "lagret";
        echo "<form method='POST' action='index.php'>
                <button type='submit' name='back'>back</button>
            </form>";
    }

?>
