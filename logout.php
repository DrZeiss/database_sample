<?php
session_start(); // needs to be here so that we can unset the stored session variables

session_unset(); // free all session variables

session_destroy(); 

header('Location: ds.php'); // go back to our main page
?>