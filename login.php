<?php
/*action="<?=$_SERVER['PHP_SELF'];?>"*/
include('init.inc.php'); //the location of this file has to be in the same folder with news.php
include('includes/connection.php');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
?>

<!DOCTYPE html PUBLIC "-/W3C//DTD XHTML 1.1 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict-dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" consent="text/html; charset=utf-8" />
		<title></title>
		<style>
		#nav
		{
			line-height:30px;
			background-color:#eeeeee;
			height:auto;
			width:200px;
			float:left;
			color:black;
			position:fixed;
			padding:5px;
		}
		#section
		{
			height:100%;
			float:left;
			margin-left:210px;
			padding:10px;
		</style>
	</head>
	<body>
		<div>
			<h3> Please fill out the fields below </h3>
			<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
				Username :&ensp;<input type="text" name="username">
				Email :&emsp;&emsp;<input type="text" name="email">
				Password :&ensp;<input type="text" name="password">
				<input type="submit" name="submitAccount">
			</form>
		</div>
		<div>
			<a href="news.php"> Click to see news </a>
		</div>
	</body>
</html>