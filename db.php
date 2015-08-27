<?php

if(session_status() != PHP_SESSION_ACTIVE) session_start();

// Setting up session information
if(!isset($_SESSION["name"])) {
  $_SESSION["name"] = "";
}
if(!isset($_SESSION["pass"])) {
  $_SESSION["pass"] = "";
}
if(!isset($_SESSION["isAdmin"])) {
  $_SESSION["isAdmin"] = false;
}
if(!isset($_SESSION["isLoggedIn"])) {
  $_SESSION["isLoggedIn"] = false;
}
if(!isset($_SESSION["dbTable"])) {
  $_SESSION["dbTable"] = "";
}

// Default variables
$dbhost = "localhost";
$dbuser = "btanx10m_";
$dbpass = "demo1";
$dbdatabase = "btanx10m_ds";

function dbLogin($user="", $pass="") {
  global $dbhost, $dbdatabase;

  $mysqli = @new mysqli($dbhost, "btanx10m_".$user, $pass, $dbdatabase);
  if(mysqli_connect_errno()) {
    if (mysqli_connect_errno() === 1044 || mysqli_connect_errno() === 1045) {
      echo "Username or password is incorrect. Please try again.";
    }
    else {
      echo "Error (#" . mysqli_connect_errno() . "): " . $mysqli->connect_error;
    } 
    return false;
  }
  else {
    $_SESSION["isLoggedIn"] = true;
    if($user === "demo") {
      $_SESSION["isAdmin"] = true; // get adminstrative permission for demo purposes
      $_SESSION["dbTable"] = "demo";
    }
    else {
      $_SESSION["dbTable"] = "research";
    }
  }

  $mysqli->close();
  return true;
}

function dbConnect($user = "demo",$pass = "",$db = "") {
  global $dbhost, $dbuser, $dbpass, $dbdatabase;

  $dbuser = "btanx10m_" . $user;
  if($pass !== "") {
    $dbpass = $pass;
  }

  $dbcnx = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbdatabase);
  // Check the connection
  if (mysqli_connect_errno()) {
    if (mysqli_connect_errno() === 1044 || mysqli_connect_errno() === 1045) {
      echo "Username or password is incorrect. Please try again.";
    }
    else {
      echo "Failed to connect to MySQL database: " . mysqli_connect_errno();
    }
  }
  else if ($user == "admin") {
    // Check for admin user
    $_SESSION["isAdmin"] = true;
  }

  // Handle when user selects specific database table
  if ($db !== "" && !mysqli_select_db($db)) {
    echo "The database '" . $db . "' is unavailable.<br>";
  }

  return $dbcnx;
}
?>