<?php
	$o    = $_GET['o'];
	$temp = $_GET['TEMP'];

	$sql = mysql_query("INSERT INTO 'temperature' VALUES('$temp')");

	$con = mysql_connect("localhost", "arduinot", "6AApjDjf") or die(mysql_error());
	mysql_select_db("arduinot") or die(mysql_error());
	
	echo "OK";
?>
