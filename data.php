<?php
	$o    = $_GET['o'];
	$temp = $_GET['TEMP'];
	
	if(!isset($o) && !isset($temp)) {
		die();
	}
	//Set the timezone.
	date_default_timezone_set('America/Detroit');

	$con = mysql_connect("localhost", "arduinot", "6AApjDjf") or die(mysql_error());
	mysql_select_db("arduinot") or die(mysql_error());

	if(isset($o)) {
		//Handle read request
		$result = mysql_query("SELECT * FROM temperature", $con) or die('Error: ' . mysql_error());
		
		echo json_encode(mysql_fetch_assoc($result));
	
	} else {
		if (!mysql_query("INSERT INTO temperature VALUES($temp, NOW())")) {
			die('Error: ' . mysql_error());
		}
	
		echo "OK";
	}
?>
