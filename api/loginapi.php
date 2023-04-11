<?php

	require "../database.php";

	if (empty($email = $_GET["email"])) {
		die("Email is required");
	} 


	if (empty($password = $_GET['password'])) {
		die("Password is required");
	} 

	$sql_query = "SELECT * FROM user
                  WHERE email = '$email'";

  	$query_result = $mysqli->query($sql_query);

  	$user = $query_result->fetch_assoc();




   	if ($user) {


	    if (password_verify($password, $user["password_hash"])) {

            session_start();

            session_regenerate_id();

            $_SESSION["user_id"] = $user["id"];

            $_SESSION["logged_in"] = true;

      		$response['status']="200";
      		$response['message']="login success";

		}

    	else {

    		$response['user']=(object)[];
      		$response['error']="400";
      		$response['message']="Wrong credentials";

    	}
    }

	else {

		$response['user']=(object)[];
		$response['error']="400";
		$response['message']="Wrong credential";

    	}

	echo json_encode($response);
	echo ($user["email"]);
?>