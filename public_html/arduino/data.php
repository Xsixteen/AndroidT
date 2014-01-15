<?php
	$o    = $_GET['o'];
	$temp = $_GET['TEMP'];
	$stack = array();

	if(!isset($o) && !isset($temp)) {
		die();
	}
	//Set the timezone.
	date_default_timezone_set('America/Detroit');

	$con = mysql_connect("localhost", "arduinot", "6AApjDjf") or die(mysql_error());
	mysql_select_db("arduinot") or die(mysql_error());

	if($o == "ts") {
		$result = mysql_query("SELECT Time FROM temperature", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			array_push($stack, date('n-j \a\t ga',strtotime($row["Time"])));
		}
		echo json_encode($stack);
		
	} else if(isset($o)) {
		//Handle read request
		$result = mysql_query("SELECT * FROM temperature", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			array_push($stack, $row);
		}
		echo json_encode($stack);
	
	} else {
		if (!mysql_query("INSERT INTO temperature VALUES($temp, NOW())")) {
			die('Error: ' . mysql_error());
		}
	
		echo "OK";
	}
?>
