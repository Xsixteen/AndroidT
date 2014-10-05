<?php
	$o    = $_GET['o'];
	$temp = $_GET['TEMP'];
	$stack = array();
	$rptID = $_GET['rptID'];

	if(!isset($o) && !isset($temp)) {
		die();
	}
	
	//By default if rptID is not set it is = 0
	if(!isset($rptID)) {
		$rptID = 0;	
	}
	//Set the timezone.
	date_default_timezone_set('America/Detroit');

	$con = mysql_connect("localhost", "arduinot", "6AApjDjf") or die(mysql_error());
	mysql_select_db("arduinot") or die(mysql_error());

	if($o == "ts") {
		$now      = date("Y-m-d H", strtotime("now"));
		$twoweeks = date("Y-m-d H", strtotime("-2 weeks"));
		$result = mysql_query("SELECT Time FROM temperature WHERE RemoteNum = '$rptID' AND Time <= '$now' AND Time >= '$twoweeks' ORDER BY Time asc", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			array_push($stack, date('n-j \a\t ga',strtotime($row["Time"])));
		}
		echo json_encode($stack);
		
	} else if($o == "24h") {
		$now = date("Y-m-d H", strtotime("now"));
		$yesterday = date("Y-m-d H", strtotime("-1 day"));
		//Handle read request
		$result = mysql_query("SELECT * FROM temperature WHERE RemoteNum = '$rptID' AND Time <= '$now' AND Time >= '$yesterday' ORDER BY Time asc", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$pre["Time"] = date('n-j \a\t ga',strtotime($row["Time"]));
			$pre["Temp"] = $row["Temp"];
			array_push($stack, $pre);
		}
		echo json_encode($stack);
	} else if($o == "24hstats") {
		$now = date("Y-m-d H:m:s", strtotime("now"));
		$yesterday = date("Y-m-d H:m:s", strtotime("-1 day"));
		//Handle read request
		$result = mysql_query("SELECT AVG(Temp) AS temp_avg, MAX(Temp) AS temp_max, MIN(Temp) AS temp_min FROM temperature WHERE RemoteNum = '$rptID' AND Time <= '$now' AND Time >= '$yesterday'", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$stack["temp_max"] = $row["temp_max"];
			$stack["temp_min"] = $row["temp_min"];
			$stack["temp_avg"] = $row["temp_avg"];
		}
		echo json_encode($stack);
	} else if($o == "2wkstats") {
		$now = date("Y-m-d H:m:s", strtotime("now"));
		$twoweeks = date("Y-m-d H:m:s", strtotime("-2 weeks"));
		//Handle read request
		$result = mysql_query("SELECT AVG(Temp) AS temp_avg, MAX(Temp) AS temp_max, MIN(Temp) AS temp_min FROM temperature WHERE RemoteNum = '$rptID' AND Time <= '$now' AND Time >= '$twoweeks'", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$stack["temp_max"] = $row["temp_max"];
			$stack["temp_min"] = $row["temp_min"];
			$stack["temp_avg"] = $row["temp_avg"];
		}
		echo json_encode($stack);
	} else if($o == "monthstats") {
		$month_avg	= array();
		
		for($i = 1; $i <= 12; $i++) {
			$month_start      = date("Y-m-d H:m:s", mktime(0,0,0, $i, 1, date("Y")));
			$month_end	  = date("Y-m-d H:m:s", mktime(0,0,0, $i, date("t"), date("Y")));

			$result = mysql_query("SELECT AVG(Temp) AS temp_avg, MAX(Temp) AS temp_max, MIN(Temp) AS temp_min FROM temperature WHERE RemoteNum = '$rptID' AND Time <= '$month_end' AND Time >= '$month_start'", $con) or die('Error: ' . mysql_error());
			while($row = mysql_fetch_assoc($result)) {
			        $stack["month"]    = date("F", mktime(0,0,0, $i, 1, date("Y")));
                                $stack["temp_max"] = ($row["temp_max"] == null ? 0 : $row["temp_max"]);
                                $stack["temp_min"] = ($row["temp_min"] == null ? 0 : $row["temp_min"]);
                                $stack["temp_avg"] = ($row["temp_avg"] == null ? 0 : $row["temp_avg"]);
                                array_push($month_avg, $stack);
			}
		}
		echo json_encode($month_avg);
		
	} else if($o =="last") {
		$result = mysql_query("SELECT * FROM temperature WHERE RemoteNum = '$rptID' ORDER BY tID DESC LIMIT 1");
		while($row = mysql_fetch_assoc($result)) {
			array_push($stack, $row);
		}
		echo json_encode($stack);
		
	} else if($o == "month") {
		//Handle read request
		$now      = date("Y-m-d H:m:s", strtotime("now"));
		$month = date("Y-m-d H:m:s", strtotime("-1 month"));
		$result = mysql_query("SELECT * FROM temperature WHERE RemoteNum = '$rptID' AND Time <= '$now' AND Time >= '$month' ORDER BY Time asc", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$rowProcess["Temp"] = $row["Temp"];
			$rowProcess["Time"] = date('n-j \a\t ga',strtotime($row["Time"]));
			array_push($stack, $rowProcess);
		}
		echo json_encode($stack);
	} else if(isset($o)) {
		//Handle read request
		$now      = date("Y-m-d H", strtotime("now"));
		$twoweeks = date("Y-m-d H", strtotime("-2 weeks"));
		$result = mysql_query("SELECT * FROM temperature WHERE RemoteNum = '$rptID' AND Time <= '$now' AND Time >= '$twoweeks' ORDER BY Time asc", $con) or die('Error: ' . mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$rowProcess["Temp"] = $row["Temp"];
			$rowProcess["Time"] = date('n-j \a\t ga',strtotime($row["Time"]));
			array_push($stack, $rowProcess);
			 
		}
		echo json_encode($stack);
	
	} else {
		if (!mysql_query("INSERT INTO temperature (Temp, Time, RemoteNum) VALUES($temp, NOW(), $rptID)")) {
			die('Error: ' . mysql_error());
		}
	
		echo "OK";
	}

?>
