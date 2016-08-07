<?php
include('init.inc.php'); //the location of this file has to be in the same folder with news.php
include('include/connection.php');
?>
<!DOCTYPE html PUBLIC "-/W3C//DTD XHTML 1.1 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict-dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head style="background-color:black">

		<meta http-equiv="Content-Type" consent="text/html; charset=utf-8" />

		<title></title>

		<style>
		#nav
		{
			line-height:30px;
			background-color:#eeeeee;
			height:420px;
			width:200px;
			float:left;
			color:black;
			position:fixed;
			padding:5px;
			overflow:auto;
			border-style:groove;
		}
		#section
		{
			height:100%;
			float:left;
			background-color:#eeeeee;
			margin-left:210px;
			padding:10px;
		}
		</style>

	</head>

	<body style="background-color:#eeeeee">

		<div>
			<?php // This division is for user to search keywords for relating news. ?>
			<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
				<input type="text" name="searchWord" value="<?=isset($_POST['searchWord']) ? htmlspecialchars($_POST['searchWord']) : '' ?>" /><br>
				<input type="submit" name="submit" value="Search">
			</form>
		</div>

		<div>
			<a href="news.php"> Click to see news </a>
		</div>

		<div id="nav">
			<?php
				// This division is for displaying news contents.
				$query = "SELECT * FROM keywords ORDER BY ID DESC";
				$result = mysql_query($query);	
				
				while ($displayInput = mysql_fetch_array($result))
				{
			?>
					<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
						<input type="submit" name="searchWord" value="<?= $displayInput['keyword'] ?>" />
					</form>
			<?php
				}
			?>
		</div>

		<div id="section">
				<?php
				foreach (fetch_word() as $article) //this function will retrieve all texts from the chosen website and display them
				{
					// Edit pictures/thumbnails so that they are limited to 100 x 100 pixels.
					?>
					<h3><a href="<?php echo $article['link'] ?>"><?php echo $article['title']; ?></a></h3>
					<img width ="100px" height="100px" src="<?php echo $article['image']['url']; ?>" alt="thumbnail" />
					<p>
						<?php echo $article['description'] ?>
					</p>
					<?php
				}
				?>
		</div>
	</body>
</html>