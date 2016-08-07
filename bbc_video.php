<?php
	include 'connection.php';
	$done = 0;

	function fetch_word()
	{
		$data = file_get_contents('http://feeds.bbci.co.uk/news/rss.xml'); //get all contents from the chosen website
		$data = simplexml_load_string($data); //load texts in a simple format
		//echo $data; //output texts are unorganized
		//print_r($data); //output texts are unorganized
		$articles = array(); //initialize variable articles as an array

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
			
			$text = (string)$item->title;
			$text = strtolower($text);
			$split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);

			if (isset($_POST['searchWord']))
			{
				$counterLoop1 = 0;
				$counterLoop2 = 0;
				$testKey = $_POST['searchWord'];
				$testKey = strtolower($testKey);
				$split2 = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $testKey, -1, PREG_SPLIT_NO_EMPTY);

				foreach ($split2 as $count)
				{
					$counterLoop1++;
				}

				foreach ($split2 as $fromUser)
				{
					foreach ($split as $keyWord)
					{
						if ($keyWord == $fromUser)
						{
							$counterLoop2++;
						}

						if ($counterLoop1 <= $counterLoop2)
						//if ($keyWord == $_POST['searchWord'])
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
							break;
						}
					}
				}
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
		$done = 1;
		return $articles; //print out all objects of "articles"
	}

	if (!isset($_POST['submit']))
	//if (!NotEmpty($_POST['searchWord']))
	{
		echo "Please input a key word 4";
	}
	else if (NotEmpty($_POST['searchWord']))
	{
		$done = 0;
		$inputWord = $_POST['searchWord'];
		mysql_query("INSERT INTO keywords (`keyword`, `ID`)
					VALUE('$inputWord', NULL)") or die(mysql_error());
		echo "Input is added successfully";
	}
	else
	{
		if (!NotEmpty($_POST['searchWord']))
		{
			header("Location: clickseenews.php");
			die();
			//echo "1";
			//echo $_POST['searchWord'];
			//echo "1";
		}
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

	function NotEmpty($input)
	{
		$sInput = $input;
		$sInput = trim($sInput);

		if ($sInput != '')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
?>