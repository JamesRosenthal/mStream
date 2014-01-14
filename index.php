<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Foundation | Welcome</title>
    <link rel="stylesheet" href="css/foundation.css" />
    <link rel="stylesheet" href="css/playexplore.css" />
    <link rel="stylesheet" href="css/master.css">
    <script src="js/modernizr.js"></script>
  </head>

<body>

<?php
session_start();

//UNCOMMENT THE NEXT LINE TO DISABLE LOGIN
$_SESSION["login"]=1;

if($_SESSION["login"]==1){
	header("Location: playexplore.php");
	exit;
}
?>

<form action="login.php" method="post">
<input type="password" name="pword" class="center" autofocus>

</br>

<input type="submit" class="submit button success" >
</form>

    <script src="js/jquery.js"></script>
    <script src="js/foundation.min.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>