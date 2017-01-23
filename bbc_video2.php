<?php
	include 'include/connection.php';

	$timezone = date("Y-m-d");

	// Function that gets data from a website
	function fetch_wordSearch()
	{
		// The query that gets websites from database
		$query = "SELECT * FROM zc_websites ORDER BY NUMBER ASC";
		$result = mysql_query($query);
		$counterFromUser = 0;

		// Initialization
		$totalArray = array();
		$my_http = 'http';
		$searchUserInputURL = ' ';

		if (isset($_POST['searchWord']))
		{
			$sFromUser = $_POST['searchWord'];
			$sFromUser = Strtolower($sFromUser);
			$sFromUser_split = Preg_split('/\W/', $sFromUser, 0, PREG_SPLIT_NO_EMPTY);
			$bUserInputted = true;

			$searchUserInputURL = 'https://www.google.com/search?q=';

			foreach ($sFromUser_split as $eachWord)
			{
				$counterFromUser = $counterFromUser + 1;

				if ($counterFromUser > 1)
				{
					$searchUserInputURL = $searchUserInputURL . '+';
				}

				$searchUserInputURL = $searchUserInputURL . $eachWord;
			}
		}

		//print_r($counterFromUser);
		//print_r($searchUserInputURL);
		if ($counterFromUser > 0)
		{
			// Read news from google search

			//$data = file_get_contents($displayInput['link']); // ZC good. test
			// Get data from the website in string
			$data = file_get_contents($searchUserInputURL);
			// Convert html tags to string
			//print_r($data);
			$data = htmlspecialchars($data, ENT_QUOTES, 'iso8859-1');
			// Remove anything before this keyword
			$sFirstRemoval = 'About';
			$shortData = strstr($data, $sFirstRemoval);
			// Keep count of news
			$iCount = 0;
			// For URL
			$sValue = "KEEPTRACK";
			// For description
			$sValueDesc = "KEEPDESC";
			$iNumberOfNews = 18;
			$iCountURLs = 0;
			
			// Only save the first 8 results
			while ($iCount < $iNumberOfNews)
			{
				//$data = simplexml_load_file("http://rss.cnn.com/rss/cnn_topstories.rss"); //load texts in a simple format
				//$data = @simplexml_load_string($data); //load texts in a simple format
				//echo $data; //output texts are unorganized
				//$shortData = strstr($data, 'About');
				//print_r();
				
				// Get the URL
				$sGetURL = get_string_between($shortData, $my_http, '&amp');
				//print_r($sGetURL);
				// Get the full format of a website URL
				$fullURL = $my_http . $sGetURL;
				// The first use of needle
				$needle = $fullURL;
				// Use to check whether the URL has HTML tag in it
				$bTagInURL = false;
				$tempURL = $fullURL;
				$tempURL = htmlspecialchars_decode($tempURL);

				// If the URL contains HTML tag, it is not a valid URL address
				if($tempURL != strip_tags($tempURL))
				{
					// contains HTML
					$bTagInURL = true;
				}

				// Replace the used URL with a unique value
				$sValueUnique = $sValue . (string)$iCount;

				$replace = $sValueUnique;

				$bFindURL = false;
				// Check if the substring is in string
				$bFindURL = strpos($shortData, $needle);

				// If true, Find and replace the specified URL with the substring
				if ($bFindURL != false)
				{
					$shortData = substr_replace($shortData, $replace, $bFindURL, strlen($needle));
				}
				
				// Remove any text before the first detection of the URL
				$shortData = strstr($shortData, $replace);

				// Search through the text and grab the substring between "> and </a>.
				// The title is between							">	and		</a>		in my case.
				$getTitle = get_string_between($shortData, '&quot;&gt;', '&lt;/a&gt;');
				// Turn the string back to the version with HTML tag
				$getTitle = htmlspecialchars_decode($getTitle);
				// Strip HTML tags from the string
				$getTitle = strip_tags($getTitle);

				// Get description of the news
				//											st">			&nbsp
				$getDesc = get_string_between($shortData, 'st&quot;&gt;', '&amp;nbsp');
				// Save the saved description in case
				$tempDesc = $getDesc;

				// If the description is more than 500 characters, it is not the right description
				if (strlen($getDesc) > 500)
				{
					// Keep any thing before the needle
					//								</span>
					$tempDesc = strstr($tempDesc, '&lt;/span&gt;', true);
					$getDesc = $tempDesc;
				}

				// Turn the string back to the version with HTML tag
				$getDesc = htmlspecialchars_decode($getDesc);
				// Strip HTML tags from the string
				$getDesc = strip_tags($getDesc);

				// If the string has HTML tag, </>, keep only the part before it.
				$needle = '&lt;/&gt;';
				if (strpos($getDesc, $needle))
				{
					$getDesc = strstr($getDesc, $needle, true);
				}

				$bFindDesc = false;
				$needle =  $tempDesc;
				// Replace the used description with a unique value
				$sValueUniqueDesc = $sValueDesc . (string)$iCount;
				$replace = $sValueUniqueDesc;
				// Check if the substring is in string
				if (strlen($needle) > 0)
				{
					$bFindDesc = strpos($shortData, $needle);
				}

				// If true, Find and replace the specified URL with the substring
				if ($bFindDesc != false)
				{
					$shortData = substr_replace($shortData, $replace, $bFindDesc, strlen($needle));
				}

				// Remove any text before the first detection of the substring
				$shortData = strstr($shortData, $replace);
			
				// If the URL is less than 150 characters and checked to be valid, put it inside the array for outputting
				if (strlen($sGetURL) < 150 && $bTagInURL == false)
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
							);
					
					// Put the article inside the array
					$totalArray = array_merge($totalArray, $articles);
				}

				$iCount = $iCount + 1;
			}
		}

		return $totalArray; //print out all objects of "articles"
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