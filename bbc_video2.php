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
		$searchUserInputURL = ' ';

		// If user submitted a search, do this
		if (isset($_POST['searchWord']))
		{
			// Get user input
			$sFromUser = $_POST['searchWord'];
			// Make the whole string lowercase
			$sFromUser = Strtolower($sFromUser);
			// Split the string into separate word
			$sFromUser_split = Preg_split('/\W/', $sFromUser, 0, PREG_SPLIT_NO_EMPTY);

			// Do this to search user input via Google News
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

			// Form the proper URL
			$searchUserInputURL = $searchUserInputURL . '&tbm=nws';
		}

		// Only perform if user submitted a proper input
		if ($counterFromUser > 0)
		{
			// **************************************************
			// Read news from google search

			// Get data from the website in string
			$shortData = file_get_contents($searchUserInputURL);
			//print_r($data);
			// Convert html tags to string
			$shortData = $shortData;
			$shortData = htmlspecialchars($shortData, ENT_QUOTES, 'iso8859-1');
			// Remove anything before this keyword
			$sFirstRemoval = 'About';
			$shortData = strstr($shortData, $sFirstRemoval);
			//print_r($shortData);


			// **************************************************
			// Do some initialization

			// Keep count of news
			$iCount = 0;
			// For URL
			$sValue = "KEEPTRACK";
			// For description
			$sValueDesc = "KEEPDESC";
			// For title
			$sValueTitle = "KEEPTITLE";
			$iNumberOfNews = 18;
			$iCountURLs = 0;
			

			// **************************************************
			// Get data from website

			// Only save the first 18 results
			while ($iCount < $iNumberOfNews)
			{
				// Get the URL
				$sMy_http = 'q=http';
				$sGetURL = get_string_between($shortData, $sMy_http, '&amp');
				
				// Get the full format of a website URL
				$fullURL = 'http' . $sGetURL;


				// **************************************************
				// The first use of needle for URL
				$needle = $fullURL;
				$bFindURL = false;
				// Check if the substring is in string
				$bFindURL = strpos($shortData, $needle);

				// If true, Find and replace the specified URL with the substring
				if ($bFindURL != false)
				{
					// Replace the used URL with a unique value
					$replace = $sValue . (string)$iCount;
					// Remove any text before the first detection of the URL
					$shortData = remove_string_front($shortData, $replace, $bFindURL, $needle);
				}

				// **************************************************
				// Check whether or not the URL is valid
				// Use to check whether the URL has HTML tag in it
				$bTagInURL = false;
				$sGetWebsiteURL = '(' . $fullURL . ')';
				$tempURL = $fullURL;
				$tempURL = htmlspecialchars_decode($tempURL);

				// If the URL contains HTML tag, it is not a valid URL address
				if($tempURL != strip_tags($tempURL))
				{
					// contains HTML
					$bTagInURL = true;
				}
				
				// If the URL is less than 150 characters and checked to be valid, put it inside the array for outputting
				if (strlen($sGetURL) < 200 && $bTagInURL == false)
				{

					// **************************************************
					// Get the 2nd URL
					$sGetURL2nd = get_string_between($shortData, $sMy_http, '&amp');
					
					// Get the full format of a website URL
					$fullURL2nd = $sMy_http . $sGetURL2nd;

					// Get the string between 2 URLs to check for description
					$sGetDescInBetween = get_string_between($shortData, $replace, $sGetURL2nd);


					// **************************************************
					// Get the title
					$getTitle = '';
					// Keep looking for the title
					while (strlen($getTitle) < 2)
					{
						// Search through the text and grab the substring between "> and </a>.
						// The title is between							">	and		</a>		in my case.
						$getTitle = get_string_between($shortData, '&quot;&gt;', '&lt;/a&gt;');

						// The first use of needle for URL
						$needle = 'cite&gt;';
						
						$bFindTitle = false;
						// Check if the word "cite" is in this string. if yes, then it is a false title/alarm
						$bFindTitle = strpos($getTitle, $needle);

						// If true, Find and replace the false title with something else
						if ($bFindTitle == true)
						{
							// Replace with a unique value
							$replace = $sValueTitle . (string)$iCount;
							// Remove any text before the first detection of the title
							$shortData = remove_string_front($shortData, $replace, $bFindTitle, $getTitle);
							// Reset the title to search again
							$getTitle = '';
						}
					}
					// Turn the string back to the version with HTML tag
					$getTitle = htmlspecialchars_decode($getTitle);
					// Strip HTML tags from the string
					$getTitle = strip_tags($getTitle);


					// **************************************************
					// Get description of the news
					//													st">			&nbsp
					$getDesc = get_string_between($sGetDescInBetween, 'f&quot;&gt;', '&amp;nbsp');

					// Got a string, but check if it is valid
					if (strlen($getDesc) > 20)
					{
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
						// Check if the substring is in string
						if (strlen($needle) > 0)
						{
							$bFindDesc = strpos($shortData, $needle);
						}

						// If true, Find and replace the specified URL with the substring
						if ($bFindDesc != false)
						{
							// Replace the used description with a unique value
							$replace = $sValueDesc . (string)$iCount;
							// Remove any text before the first detection of the description
							$shortData = remove_string_front($shortData, $replace, $bFindDesc, $needle);
						}
					}
					// There is not description for this article
					else
					{
						$getDesc = '';
					}

					// **************************************************
					// Input the proper article into the array for publishing
					// initialize the array
					$articles = array(); //initialize variable articles as an array

					// Put data inside the array in order
					if (strlen($getTitle) > 1)
					{
						$articles[] = array(
							//point to string
							//key				value
							'title'			=> (string)$getTitle,
							'description'	=> (string)$getDesc,
							'link'			=> (string)$fullURL,
							'website'		=> (string)$sGetWebsiteURL,
							);
					}
					
					// Put the article inside the array
					$totalArray = array_merge($totalArray, $articles);
				}

				$iCount = $iCount + 1;
			}
		}
		else
		{
			// **************************************************
			// Input the proper article into the array for publishing
			// initialize the array
			$articles = array(); //initialize variable articles as an array

			// Put data inside the array in order
				$articles[] = array(
					//point to string
					//key				value
					'title'			=> '',
					'description'	=> 'Your search results will be displayed here.',
					'link'			=> '',
					'website'		=> '',
					);
			
			// Put the article inside the array
			$totalArray = array_merge($totalArray, $articles);
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

	// This function performs removing part of a string
	function remove_string_front($shortData, $replace, $bFindString,  $sOGString)
	{
		// Perform the string removal process
		$shortData = substr_replace($shortData, $replace, $bFindString, strlen($sOGString));
		// Remove any text before the first detection of the URL
		$shortData = strstr($shortData, $replace);
		return $shortData;
	}
?>