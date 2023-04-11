<?php
header("Content-Type:application/json");
if (isset($_GET['avsender']) && $_GET['avsender']!="") {
	include('../database.php');
	$avsender = $_GET['avsender'];
	$result = mysqli_query($mysqli, "SELECT avsender, innhold, emneid, til FROM meldingv2 WHERE avsender=$avsender");
	$msg_list = array();
	$index = 0;
	if(mysqli_num_rows($result)>0){
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		$innhold = $rows;
		response($innhold);
		mysqli_close($mysqli);
	}else{
		response(NULL, NULL, 200,"No Record Found");
		}
	}else{
		response(NULL, NULL, 400,"Invalid Request");
	}
function response($innhold){
	$response = $innhold;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
