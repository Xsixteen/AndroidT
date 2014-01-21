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
		
	} else if($o == "24h") {
		$now = date("Y-m-d H:m:s", strtotime("now"));
		$yesterday = date("Y-m-d H:m:s", strtotime("-1 day"));
		//Handle read request
		$result = mysql_query("SELECT * FROM temperature WHERE Time <= '$now' AND Time >= '$yesterday'", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$pre["Time"] = date('n-j \a\t ga',strtotime($row["Time"]));
			$pre["Temp"] = $row["Temp"];
			array_push($stack, $pre);
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
		if (!mysql_query("INSERT INTO temperature (Temp, Time) VALUES($temp, NOW())")) {
			die('Error: ' . mysql_error());
		}
	
		echo "OK";
	}
?>
