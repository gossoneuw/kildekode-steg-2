  <?php
 
  require '../database.php';
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

  $email1 = $_GET['email'];
  $checkUser="SELECT * from user WHERE email='$email1'";
  $checkQuery=mysqli_query($mysqli, $checkUser);
  if(mysqli_num_rows($checkQuery)>0){
     $response['error']="002";
    $response['message']="Email already registered";
  }
  else
  {

	$stmtquery="INSERT INTO user(name,email,password_hash) VALUES(?,?,?)";
	if (!$stmt = $mysqli->prepare($stmtquery)){
		die("sql error: hei");
		}
	$stmt->bind_param('sss', $username, $email, $password);
	
	$username=$_GET['name'];
	if (preg_match_all('[\/|\$|"|\/|<|>|\(|\)]', $username)) {
                        $error = array("Illegal characters used during name with studreg api. name used: ", $username);
                        $error = json_encode($error);
                        $logger->warning($error);
                        die("sql error: kaffetyven");
                }
	$email=$email1;
	$password = password_hash($_GET["password"], PASSWORD_DEFAULT);

  if($stmt->execute()){
    $stmtquery2="INSERT INTO studentv2(idstudent, studieretning, studiekull) VALUES(?,?,?)";
    if (!$stmt2 = $mysqli->prepare($stmtquery2)){
	die("sql error: hei2");
	}
	$stmt2->bind_param('sss', $last_id, $studieretning, $studiekull);
	
	$last_id = mysqli_insert_id($mysqli);
	$studieretning=$_GET['studieretning'];
	$studiekull=$_GET['studiekull'];

	if($stmt2->execute()){
    	$response['error']="000";
     	$response['message']="Register successful!";
	}
  }
  else
  {
    $response['error']="001";
    $response['message']="Registeration failed!";
  }
  }
  echo json_encode($response);
?>
