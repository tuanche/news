<?php
	include 'include/connection.php';

	$timezone = date("Y-m-d");

	function fetch_word()
	{
		$query = "SELECT * FROM zc_websites ORDER BY NUMBER ASC";
		$result = mysql_query($query);
		$totalArray = array();
		$my_http = 'http';
		
		while ($displayInput = mysql_fetch_array($result))
		{
			if ($displayInput['NUMBER'] == 4)
			{
				// Read news from google search

				//$data = file_get_contents($displayInput['link']); // ZC good. test
				$data = file_get_contents($displayInput['link']);
				$data = htmlspecialchars($data);
				$shortData = strstr($data, 'About');
				$iCount = 0;
				$sValue = "KEEPTRACK";
				$sValueDesc = "KEEPDESC";
				
				while ($iCount < 20)
				{
					//$data = simplexml_load_file("http://rss.cnn.com/rss/cnn_topstories.rss"); //load texts in a simple format
					//$data = @simplexml_load_string($data); //load texts in a simple format
					//echo $data; //output texts are unorganized
					//$shortData = strstr($data, 'About');
					
					$fullstring = $shortData;
					$getURL = get_string_between($fullstring, $my_http, '&amp');
					$fullURL = $my_http . $getURL;
					$needle = $fullURL;
					$posURL = false;
					$tempURL = $fullURL;
					$tempURL = htmlspecialchars_decode($tempURL);

					// If the URL contains HTML tag, it is not a valid URL address
					if($tempURL != strip_tags($tempURL))
					{
						// contains HTML
						$posURL = true;
					}

					$sValueUnique = $sValue . (string)$iCount;

					$replace = $sValueUnique;

					$bFindURL = false;
					// Check if the substring is in string
					$bFindURL = strpos($shortData, $needle);

					// If true, Find and replace the specified URL with the string "DUDE"
					if ($bFindURL != false)
					{
						$shortData = substr_replace($shortData, $replace, $bFindURL, strlen($needle));
					}
					
					// Remove any text before the first detection of string "DUDE"
					$shortData = strstr($shortData, $replace);

					// Search through the text and grab the substring between "> and </a>.
					// The title is between "> and </a> in my case.
					$getTitle = get_string_between($shortData, '&quot;&gt;', '&lt;/a&gt;');
					// Turn the string back to the version with HTML tag
					$getTitle = htmlspecialchars_decode($getTitle);
					// Strip HTML tags from the string
					$getTitle = strip_tags($getTitle);

					// Get date of the publication
					$getDate = get_string_between($shortData, 'st&quot;&gt;', '&lt;b&gt;');

					//$sValueUniqueDesc = $sValueDesc . (string)$iCount;
					//$replace = $sValueUniqueDesc;
					// Remove any text before the first detection of the substring
					//$shortData = strstr($shortData, $replace);

					//$getDesc = get_string_between($shortData, '&lt;/b&gt;', '&amp;nbsp');
					$getDesc = "Okay";
				
					// If the URL is less than 150 characters and checked to be valid, put it inside the array for outputting
					if (strlen($getURL) < 150 && $posURL == false)
					{
						// initialize the array
						$articles = array(); //initialize variable articles as an array

						// Put data inside the array in order
						$articles[] = array(
								//point to string
								//key				value
								'title'			=> (string)$getTitle,
								'description'	=> (string)$getDesc,
								'link'			=> (string)$fullURL,
								'date'			=> (string)$getDate,
								);
						
						$totalArray = array_merge($totalArray, $articles);
					}

					// Keep track of loop counts
					$iCount = $iCount + 1;
				}
			}
		}

		return $totalArray; //print out all objects of "articles"
		//return $articles; //print out all objects of "articles"
	}

	if (!isset($_POST['submit']))
	{
		//echo "Please input a key word 4" . "<br>";
	}
	else if (!IsEmpty($_POST['searchWord']) && isset($_COOKIE["SavedUserInfo"]) && $_COOKIE["SavedUserInfo"] != "999999999")
	{
		$inputWord = $_POST['searchWord'];
		$sInsertUser = $_COOKIE["SavedUserInfo"];

		mysql_query("INSERT INTO zc_accounts_keywords (`ID`, `ACCOUNT_NUMBER`, `KEYWORD`)
					VALUE(NULL, '$sInsertUser', '$inputWord')") or die(mysql_error());
		echo "Input is added successfully";
	}

	if (!isset($_POST['submitAccount']))
	{
		//echo "Please input a key word 2";
	}
	// if (!empty($userName) && !empty($emailLocal) && !empty($passWord))
	else
	{
		$i_countUsers = mysql_query("SELECT COUNT(ID) FROM zc_accounts") or die(mysql_error());
		$i_countUsers = $i_countUsers + 1;

		$userName = $_POST['username'];
		$emailLocal = $_POST['email'];
		$passWord = $_POST['password'];
		date_default_timezone_set('America/Los_Angeles');
		//$timezone = date_default_timezone_get();
		//$timezone = date("Y-m-d");
		mysql_query("INSERT INTO zc_accounts (`ID`, `ACCOUNT_NUMBER`, `USERNAME`, `EMAIL`, `PASSWORD`, `CREATED_DATE`)
										VALUE(NULL, '$i_countUsers', '$userName', '$emailLocal', '$passWord', '$timezone')") or die(mysql_error());
		echo "Account is added successfully" . $timezone;
	}

	// If log out button is clicked, do LogOut() function.
	if (isset($_POST['submitLogOut']))
	{
		LogOut();
		/*setcookie("SavedUsername", "doesnotexist", 1);
		setcookie("SavedPassword", "doesnotexist", 1);
		header("Location: clickSeeNews.php");
		exit();*/
	}

	function IsEmpty($input)
	{
		$sInputTemp = $input;
		$sInputTemp = trim($sInputTemp);

		if ($sInputTemp == '')
		{
			return true;
		}

		return false;
	}

	function LogIn($Username, $Password)
	{
		$result = mysql_query("SELECT DISTINCT ACCOUNT_NUMBER FROM zc_accounts WHERE USERNAME='$Username' AND PASSWORD='$Password'") or die(mysql_error());

		while ($displayInput = mysql_fetch_array($result))
		{
			// 86400 = 1 day
			$iExpireTime = 86400 * 30;
			setcookie("SavedUserInfo", $displayInput['ACCOUNT_NUMBER'], time() + $iExpireTime);
			return true;
		}

		return false;
	}
	
	function LogOut()
	{
		// 86400 = 1 day
		$iExpireTime = 86400 * 30;
		// Change the cookie values when user logs out.
		setcookie("SavedUserInfo", "999999999", time() + $iExpireTime);
		// Refresh the page so the change can be made.
		header("Location: clickSeeNews.php"); /* Redirect browser */
		exit();
	}

	function GetUserName($inputName)
	{
		$result = mysql_query("SELECT DISTINCT USERNAME FROM zc_accounts WHERE ACCOUNT_NUMBER=$inputName") or die(mysql_error());

		while ($displayInput = mysql_fetch_array($result))
		{
			return $displayInput['USERNAME'];
		}
	}

	// This function returns a string between 2 specified values.
	function get_string_between($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
?>