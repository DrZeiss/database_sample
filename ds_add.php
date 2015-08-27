<?php
session_start();
include_once 'db.php';

// Redirect to login page if session data is not set yet
if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]) {
  header("Location: login.php"); // redirect the page
}

if($_SESSION["name"] != "") {
  $con = dbConnect($_SESSION["name"], $_SESSION["pass"]);
}
else {
  $con = dbConnect();
}

// Check connection
if ($con) {
  // create the sql query
  $sql = "";
  if($_POST['mode'] === "delete") {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $sql = "DELETE FROM " . $_SESSION["dbTable"] . " WHERE id = '$id'";
  }
  else {
    // escape variables for security
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $url = str_replace("http://", "", mysqli_real_escape_string($con, $_POST['url'])); // remove 'http://' prefix to shorten the text
    $type = mysqli_real_escape_string($con, $_POST['type']);
    $annual_sales = mysqli_real_escape_string($con, $_POST['annual_sales']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $dehy_garlic = mysqli_real_escape_string($con, $_POST['dehy_garlic']);
    $frozen_vegetables = mysqli_real_escape_string($con, $_POST['frozen_vegetables']);
    $dehy_vegetables = mysqli_real_escape_string($con, $_POST['dehy_vegetables']);
    $organic = mysqli_real_escape_string($con, $_POST['organic']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    // TODO: Parameterize it for better security?
    if($_POST['mode'] === "add") {
      $sql = "INSERT INTO " . $_SESSION["dbTable"] . " (name, url, type, annual_sales, location, city, state, dehy_garlic, frozen_vegetables, dehy_vegetables, organic, notes)
              VALUES ('$name','$url','$type','$annual_sales','$location','$city','$state','$dehy_garlic','$frozen_vegetables','$dehy_vegetables','$organic','$notes')";    
    }
    else if($_POST['mode'] === "edit") {
      $id = mysqli_real_escape_string($con, $_POST['id']);
      $sql = "UPDATE " . $_SESSION["dbTable"] . " 
              SET name='$name', url='$url', type='$type', annual_sales='$annual_sales', location='$location', 
                  city='$city', state='$state', dehy_garlic='$dehy_garlic', 
                  frozen_vegetables='$frozen_vegetables', dehy_vegetables='$dehy_vegetables', 
                  organic='$organic', notes='$notes' 
              WHERE id = '$id'";      
    }
    else {
      echo "Invalid mode!";
      mysqli_close($con);
      return;
    }    
  }
  // execute the query
  $result = mysqli_query($con, $sql);
  if($result == false) {
    echo "Error: " . mysqli_error($con) . "<br>";
    echo "Please double check your entries!";
  }
  else {
    $redirectURL = "ds.php";
    header('Location: ' . $redirectURL);
  }
}

?>