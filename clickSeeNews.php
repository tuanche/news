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
		#keywords
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
			margin-top:100px;
		}
		#contents
		{
			height:100%;
			float:left;
			background-color:#eeeeee;
			margin-left:210px;
			padding:10px;
		}
		#search
		{
			position:fixed;
			margin-top:60px;
		}
		</style>

	</head>

	<body style="background-color:#eeeeee">

		<div>
			<?php // This division is for user to log in.
			if (isset($_COOKIE["SavedUserInfo"])
				&& $_COOKIE["SavedUserInfo"] != "999999999")
			{
			?>
				<form>
					<label for="LoggedInUsername"> Welcome, <?php echo GetUserName($_COOKIE["SavedUserInfo"]); ?>!</label> <br>
					<label>Have a great time. </label>
				</form>
				<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
					<input type="submit" name="submitLogOut" value="Log Out">
				</form>
			<?php
			}
			else
			{
			?>
				<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
					<input type="text" name="txtUsername" placeholder="Username" value="<?=isset($_POST['txtUsername']) ? htmlspecialchars($_POST['txtUsername']) : '' ?>" /><br>
					<input type="text" name="txtPassword" placeholder="Password" value="<?=isset($_POST['txtPassword']) ? htmlspecialchars($_POST['txtPassword']) : '' ?>" /><br>
					<input type="submit" name="submitLogIn" value="Log In">
				</form>
			<?php
			}
			?>
		</div>

		<div id="search">
			<?php // This division is for user to search keywords for relating news. ?>
			<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
				<input type="text" name="searchWord" /><br>
				<input type="submit" name="submit" value="Search">
			</form>
		</div>

		<div>
			<a href="news.php"> Click to see news </a>
		</div>

		<div id="keywords">
			<?php
				if (!isset($_POST['submitLogIn']) && isset($_COOKIE["SavedUserInfo"]) && $_COOKIE["SavedUserInfo"] != "999999999")
				{
					$sUser = $_COOKIE["SavedUserInfo"];
					$result = mysql_query("SELECT KEYWORD FROM accounts WHERE ACCOUNT_NUMBER=$sUser ORDER BY ID DESC") or die(mysql_error());

					while ($displayInput = mysql_fetch_array($result))
					{
			?>
						<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
							<input type="submit" name="searchWord" value="<?= $displayInput['KEYWORD'] ?>" />
						</form>
			<?php
					}
				}
				else if (isset($_POST['submitLogIn']) && $_POST['txtUsername'] != '' && $_POST['txtPassword'] != '')
				{
					$sUsername = $_POST['txtUsername'];
					$sPassword = $_POST['txtPassword'];
					if (LogIn($sUsername, $sPassword) == true)
					{
						$sSavedUser = $_COOKIE["SavedUserInfo"];
						$result = mysql_query("SELECT KEYWORD FROM accounts WHERE ACCOUNT_NUMBER='$sSavedUser'") or die(mysql_error());

						while ($displayInput = mysql_fetch_array($result))
						{
			?>
							<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
								<input type="submit" name="searchWord" value="<?= $displayInput['KEYWORD'] ?>" />
							</form>
			<?php
						}
						header("Location: clickSeeNews.php");
						exit();
					}
				}
				else
				{
					// This division is for displaying news contents.
					$query = "SELECT * FROM keywords ORDER BY ID DESC";
					$result = mysql_query($query) or die(mysql_error());
					while ($displayInput = mysql_fetch_array($result))
					{
			?>
						<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
							<input type="submit" name="searchWord" value="<?= $displayInput['keyword'] ?>" />
						</form>
			<?php
					}
				}
			?>
		</div>

		<div id="contents">
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