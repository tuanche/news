<?php
	include 'connection.php';
	$done = 0;

	function fetch_word()
	{
		$query = "SELECT * FROM websites ORDER BY ID DESC";
		$result = mysql_query($query);	
		
		while ($displayInput = mysql_fetch_array($result))
		{
			$data = file_get_contents($displayInput['link']);
			//$data = file_get_contents('http://feeds.bbci.co.uk/news/rss.xml'); //get all contents from the chosen website
			//$data2 = file_get_contents('http://hosted.ap.org/lineups/TOPHEADS.rss'); //get all contents from the chosen website
			//$data = $data + $data2;
			$data = simplexml_load_string($data); //load texts in a simple format
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

				echo $counterFromUser;
			}

			foreach ($data->channel->item as $item) //"item" object in variable "data" is duplicated to variable "item"
			{
				$media = $item->children('http://search.yahoo.com/mrss/');
				$image = array();
				
				if ($media->thumbnail && $media->thumbnail[0]->attributes())
				{
					foreach ($media->thumbnail[0]->attributes() as $key => $value)
					{
						$image[$key] = (string)$value;
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

				if ($bUserInputted)
				{
					$counterFromTitle = 0;
					$counterFromDescription = 0;

					foreach ($sFromUser_split as $fromUser)
					{
						foreach ($sTitle_split as $fromTitle)
						{
							if ($fromTitle == $fromUser)
							{
								$counterFromTitle++;
								$counterFromDescription++;
								break;
							}
							else
							{
								foreach ($wholeWord_split as $fromDescription)
								{
									if ($fromDescription == $fromUser)
									{
										$counterFromDescription++;
										break;
									}
								}
							}
						}
					}

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
						);
					}

					//echo $counterFromUser;
					//echo $counterFromTitle;
					//echo $counterFromDescription++;
				}
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
					);
				}
			}
		}
		$done = 1;
		return $articles; //print out all objects of "articles"
	}

	if (!isset($_POST['submit']))
	{
		echo "Please input a key word 4";
	}
	else
	{
		$done = 0;
		$inputWord = $_POST['searchWord'];
		mysql_query("INSERT INTO keywords (`keyword`, `ID`)
					VALUE('$inputWord', NULL)") or die(mysql_error());
		echo "Input is added successfully";
	}

	if (!isset($_POST['submitAccount']))
	{
		echo "Please input a key word 2";
	}
	// if (!empty($userName) && !empty($emailLocal) && !empty($passWord))
	else
	{
		$userName = $_POST['username'];
		$emailLocal = $_POST['email'];
		$passWord = $_POST['password'];
		date_default_timezone_set('America/Los_Angeles');
		//$timezone = date_default_timezone_get();
		$timezone = date("Y-m-d");
		mysql_query("INSERT INTO accounts (`ID`, `NAME`, `EMAIL`, `PASSWORD`, `CREATED_DATE`)
					VALUE(NULL, '$userName', '$emailLocal', '$passWord', '$timezone')") or die(mysql_error());
		echo "Account is added successfully" . $timezone;
	}
?>