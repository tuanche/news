<?php
//http://feeds.bbci.co.uk/news.rss.xml
function fetch_word()
{
	$data = file_get_contents('http://feeds.bbci.co.uk/news/rss.xml'); //get all contents from the chosen website
	$data = simplexml_load_string($data); //load texts in a simple format
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
		
		$text = $item->title;
		$text = strtolower($text);
		$split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);
		
		if (isset($_POST['searchWord']))
		{
			foreach ($split as $i)
			{
				if ($i == $_POST['searchWord'])
				//if ($keyWord == "video")
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
	return $articles; //print out all objects of "articles"
}
?>