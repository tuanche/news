<?php
include('init.inc.php'); //the location of this file has to be in the same folder with news.php
include('connection.php');
?>
<!DOCTYPE html PUBLIC "-/W3C//DTD XHTML 1.1 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict-dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" consent="text/html; charset=utf-8" />
		<title></title>
	</head>
	<body>
		<div>
			<h2> GROUPS HERE </h2>
		</div>
		<div id="section">
				<?php
				foreach (fetch_word() as $article) //this function will retrieve all texts from the chosen website and display them
				{
					?>
					<h3><a href="<?php echo $article['link'] ?>"><?php echo $article['title']; ?></a></h3>
					<img src="<?php echo $article['image']['url']; ?>" alt="" />
					<p>
						<?php echo $article['description'] ?>
					</p>
					<?php
				}
				?>
		</div>
	</body>
</html>