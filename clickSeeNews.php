<?php
include('init.inc.php'); //the location of this file has to be in the same folder with news.php
?>
<!DOCTYPE html PUBLIC "-/W3C//DTD XHTML 1.1 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict-dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" consent="text/html; charset=utf-8" />
		<title></title>
		<style>
		#nav
		{
			line-height:30px;
			background-color:#eeeeee;
			height:auto;
			width:200px;
			float:left;
			color:black;
			position:fixed;
			padding:5px;
		}
		#section
		{
			height:100%;
			float:left;
			margin-left:210px;
			padding:10px;
		</style>
	</head>
	<body>
		<div>
			<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
				<input type="text" name="searchWord" value="<?=isset($_POST['searchWord']) ? htmlspecialchars($_POST['searchWord']) : '' ?>" /><br>
				<input type="submit" name="submit">
			</form>
		</div>

		<div>
			<a href="news.php"> Click to see news </a>
		</div>

		<div id="nav">
			Video<br>
			<?php
				if(isset($_POST['submit']))
				{
					echo htmlspecialchars($_POST['searchWord']);
				}
			?>
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