<?php
	include 'include/connection.php';

	$timezone = date("Y-m-d");

	function fetch_word()
	{
		$query = "SELECT * FROM zc_websites ORDER BY NUMBER ASC";
		$result = mysql_query($query);
		$totalArray = array();
		
		while ($displayInput = mysql_fetch_array($result))
		{
			$data = file_get_contents($displayInput['link']);
			//$data = simplexml_load_file("http://rss.cnn.com/rss/cnn_topstories.rss"); //load texts in a simple format
			$data = @simplexml_load_string($data); //load texts in a simple format
			//echo $data; //output texts are unorganized
			//print_r($data); //output texts are unorganized

			$articles = array(); //initialize variable articles as an array

			$bUserInputted = false;

			if (isset($_POST['searchWord']))
			{
				$sFromUser = $_POST['searchWord'];
				$sFromUser = Strtolower($sFromUser);
				$sFromUser_split = Preg_split('/\W/', $sFromUser, 0, PREG_SPLIT_NO_EMPTY);
				$bUserInputted = true;
				$counterFromUser = 0;

				foreach ($sFromUser_split as $Count)
				{
					$counterFromUser = $counterFromUser + 1;
				}
			}

			//"item" object in variable "data" is duplicated to variable "item"
			foreach ($data->channel->item as $item)
			{
				$media = $item->children('http://search.yahoo.com/mrss/');
				//print_r($data->channel->item);
				$image = array();

				// If the articles are from BBC, show images
				if ($displayInput['NUMBER'] == 1)
				{
					if ($media->thumbnail && $media->thumbnail[0]->attributes())
					{
						foreach ($media->thumbnail[0]->attributes() as $key => $value)
						{
							$image[$key] = (string)$value;
						}
					}
				}
				
				// Get title and compare it with user input
				$sTitle = (string)$item->title;
				$sTitle = strtolower($sTitle);
				$sTitle_split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $sTitle, -1, PREG_SPLIT_NO_EMPTY);
				
				// Get description and compare it with user input.
				$wholeWord = (string)$item->description;
				$wholeWord = strtolower($wholeWord);
				$wholeWord_split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $wholeWord, -1, PREG_SPLIT_NO_EMPTY);

				// If user has inputted something in search box
				if ($bUserInputted)
				{
					$counterFromTitle = 0;
					$counterFromDescription = 0;

					// Split user input into separate word
					foreach ($sFromUser_split as $fromUser)
					{
						// Split title into separate word
						foreach ($sTitle_split as $fromTitle)
						{
							// If the number of words from user matches with of title
							if ($fromTitle == $fromUser)
							{
								$counterFromTitle++;
								$counterFromDescription++;
								break;
							}
							// If title doesn't match enough, search in description
							else
							{
								// Split description into separate word
								foreach ($wholeWord_split as $fromDescription)
								{
									// If the number of words from user matches with of description
									if ($fromDescription == $fromUser)
									{
										$counterFromDescription++;
										break;
									}
								}
							}
						}
					}

					// Display only articles that are related to user input
					if ($counterFromUser <= $counterFromTitle || $counterFromUser <= $counterFromDescription)
					{
						$articles[] = array(
						//point to string
						//key				value
						'title'			=> (string)$item->title,
						'description'	=> (string)$item->description,
						'link'			=> (string)$item->link,
						'date'			=> (string)$item->pubDate,
						'image'			=> $image,
						'display'		=> $displayInput['NUMBER'],
						);
					}
				}
				// Display all articles by default
				else
				{
					$articles[] = array(
					//point to string
					//key				value
					'title'			=> (string)$item->title,
					'description'	=> (string)$item->description,
					'link'			=> (string)$item->link,
					'date'			=> (string)$item->pubDate,
					'image'			=> $image,
					'display'		=> $displayInput['NUMBER'],
					);
				}
			}
			
			$totalArray = array_merge($totalArray, $articles);
		}

		$i_countUsers = mysql_query("SELECT MAX(ACCOUNT_NUMBER) FROM zc_accounts") or die(mysql_error());
		
		
		return $totalArray; //print out all objects of "articles"
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

	if (!isset($_POST['submitRegisterNow']))
	{
		//echo "Please input a key word 2";
	}
	// if (!empty($userName) && !empty($emailLocal) && !empty($passWord))
	else
	{
		$userName = $_POST['txtUsername'];
		$passWord = $_POST['txtPassword'];
		$sPassWord2nd = $_POST['txtPassword2nd'];
		$txtRandomNumber1 = $_POST['txtRandomNumber1'];
		$txtRandomNumber2 = $_POST['txtRandomNumber2'];
		$txtRandomAnswer = $_POST['txtRandomAnswer'];
		$sUserAnswer = $txtRandomNumber1 + $txtRandomNumber2;
		$result = mysql_query("SELECT MAX(ACCOUNT_NUMBER) FROM zc_accounts") or die(mysql_error());
		while ($displayInput = mysql_fetch_array($result))
		{
			$iMaxNumber = $displayInput['MAX(ACCOUNT_NUMBER)'];
		}

		if ($iMaxNumber < 90000000)
		{
			$iMaxNumber = 90000000;
		}

		$iMaxNumber = $iMaxNumber + rand(1, 5);
		$bUniqueUsername = true;

		$result = mysql_query("SELECT DISTINCT USERNAME FROM zc_accounts") or die(mysql_error());
		while ($displayInput = mysql_fetch_array($result))
		{
			if ((string)$userName == (string)$displayInput['USERNAME'])
			{
				$bUniqueUsername = false;
				break;
			}
		}

		// Check if passwords match
		$bPasswordMatch = false;
		if ($passWord == $sPassWord2nd && strlen($passWord) > 1 && strlen($sPassWord2nd) > 1)
		{
			$bPasswordMatch = true;
		}

		// Check if passwords match
		$bRandomMatch = false;
		if ($sUserAnswer == $txtRandomAnswer)
		{
			$bRandomMatch = true;
		}

		// ZC: I stopped working here. Goal is to create a unique account number for each new user.
		// Check before letting user register successfully.
		if ($bUniqueUsername && $bPasswordMatch && $bRandomMatch)
		{
			//$userName = $_POST['txtUsername'];
			$emailLocal = $_POST['txtUsername'];
			
			date_default_timezone_set('America/Los_Angeles');
			//$timezone = date_default_timezone_get();
			//$timezone = date("Y-m-d");
			mysql_query("INSERT INTO zc_accounts (`ID`, `ACCOUNT_NUMBER`, `USERNAME`, `EMAIL`, `PASSWORD`, `CREATED_DATE`)
											VALUE(NULL, '$iMaxNumber', '$userName', '$emailLocal', '$passWord', '$timezone')") or die(mysql_error());
			echo "Account is added successfully. Plesae Log In.";
		}
		else if ($bUniqueUsername == false)
		{
			echo "The username is already taken! Please enter another cute one.";
		}
		else if ($bPasswordMatch == false)
		{
			echo "The passwords did not match. Please try again.";
		}
		else if ($bRandomMatch == false)
		{
			echo "The answer for bot checking did not match. Please try again.";
		}
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
?>