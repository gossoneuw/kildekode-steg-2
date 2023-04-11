<?php
  
  // include database information
  require_once('/var/www/private/database_config.php');

  $mysqli = new mysqli(
    $db_host,
    $db_user,
    $db_password,
    $db_name
  );
	
  if ($mysqli->connect_error) {
    echo 'Errno: '.$mysqli->connect_errno;
    echo '<br>';
    echo 'Error: '.$mysqli->connect_error;
    exit();
  }

  /*
  echo 'Success: A proper connection to MySQL was made.';
  echo '<br>';
  echo 'Host information: '.$mysqli->host_info;
  echo '<br>';
  echo 'Protocol version: '.$mysqli->protocol_version;
  */

  return $mysqli;

  $mysqli->close();
?>
