<?php
	//create
	/*include 'includes/connection.php';

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$keyword = $_POST['searchWord'];
	$userName = $_POST['username'];
	$emailLocal = $_POST['email'];
	$passWord = $_POST['password'];
	$timezone = date_default_timezone_get();

	if (!$_POST['submit'])
	{
		echo "Please input a key word 3";
		header('Location: clickSeeNews.php');
	}
	else if (!$keyword || empty($keyword))
	{
		mysql_query("INSERT INTO keywords(`keyword`, `ID`)
					VALUE('$keyword', NULL)") or die(mysql_error());
		echo "Input is added successfully";
		header('Location: clickSeeNews.php')
	}

	if (!$_POST['submitAccount'])
	{
		echo "Please input a key word 2";
		header('Location: login.php');
	}
	// if (!empty($userName) && !empty($emailLocal) && !empty($passWord))
	else
	{
		mysql_query("INSERT INTO accounts(`ID`, `NAME`, `EMAIL`, `PASSWORD`, `DATE`)
					VALUE(NULL, '$userName', '$emailLocal', '$passWord', '$timezone')") or die(mysql_error());
		echo "Account is added successfully";
		header('Location: login.php')
	}*/
?>