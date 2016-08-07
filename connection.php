<?php
	//connection
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = '';
	$db = 'mysql';

	$connect = mysql_connect($dbhost, $dbuser, $dbpass); //connect to server
	mysql_select_db($db); //connect to database. Update
?>