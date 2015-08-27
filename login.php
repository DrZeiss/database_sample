<?php
session_start();

include_once "db.php"; // used for database connection

// Handle when a user logs in
if(isset($_POST["userLogin"])) {
  $user = $_POST["user"];
  $pass = $_POST["pass"];
  $status = dbLogin($user, $pass);
  if($status) {
    $_SESSION["name"] = $user;
    $_SESSION["pass"] = $pass;
    header("Location: ds.php"); // refresh the page    
  }
}

?>

<html>
<link rel="stylesheet" type="text/css" href="style.css">
<title>
Login page
</title>
<body>
  <h2>Enter login info</h2>
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    Username: <input style="background-color:white" type="text" name="user"><br>
    Password: <input style="background-color:white" type="password" name="pass"><br>
  <input style="background-color:lightGreen; width:200px" type="submit" name="userLogin" value="Login">
  </form>
  
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    For demo purposes, please login via the button below.<br>
    <input type="hidden" name="user" value="demo">
    <input type="hidden" name="pass" value="demo1">
    <input style="background-color:lightYellow; width:200px" type="submit" name="userLogin" value="Demo Login">
  </form>
</body>
</html>



