<?php
session_start(); // to be able to get $_SESSION data from other page
include_once "db.php";
include "constants.php";

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
if ($con && $_GET["id"] != "") {
  // escape variables for security
  $id = mysqli_real_escape_string($con, $_GET["id"]);
  $result = mysqli_query($con, "SELECT * FROM " . $_SESSION["dbTable"] . " WHERE id = '$id'");
  if($result == false) {
    echo "Query error!<br>";
    echo "Error message: " . mysqli_error($con) . "<br>";
  }
  // assign the data
  $data = mysqli_fetch_array($result);
}
mysqli_close($con);

?>

<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="jquery-1.11.1.min.js"></script>

<title>
DS Marketing Research
</title>
<body>
<h2>
<?php
  if(isset($_GET["id"])) {
    if($_GET["id"] == "") 
      echo "Add information"; 
    else
      echo "Edit information";
  }
  else {
    header("Location: login.php"); // redirect the page
  }
?>
</h2>
<form action="ds_add.php" method="post">
<p>
Name: <input class="inputArea" type="text" name="name" value="<?php if(isset($data["name"])) echo $data["name"]; ?>">
</p>
<p>
URL: <input class="inputArea" type="text" name="url" value="<?php if(isset($data["url"])) echo $data["url"]; ?>">
</p>
<p>
Type: <select name="type">
      <?php 
        foreach($TYPES as $key=>$value) {
          echo "<option value='" . $key . "' " . ((isset($data["type"]) && $data["type"] == $key) ? "selected" : "") . ">" . $value . "</option>";
        } 
      ?>
      </select>
</p>
<p>
Annual sales: <select name="annual_sales">
              <?php 
                foreach($ANNUAL_SALES as $key=>$value) {
                  echo "<option value='" . $key . "' " . ((isset($data["annual_sales"]) && $data["annual_sales"] == $key) ? "selected" : "") . ">" . $value . "</option>";
                } 
              ?>
              </select>
</p>
<p>
Location: <select name="location">
          <?php 
            foreach($LOCATIONS as $key=>$value) {
              echo "<option value='" . $key . "' " . ((isset($data["location"]) && $data["location"] == $key) ? "selected" : "") . ">" . $value . "</option>";
            } 
          ?>
          </select>
</p>
<p>
City: <input class="inputArea" type="text" name="city" value="<?php if(isset($data["city"])) echo $data["city"]; ?>">
</p>
<p>
State: <select name="state">
        <?php 
          foreach($STATES as $key=>$value) {
            echo "<option value='" . $key . "' " . ((isset($data["state"]) && $data["state"] == $key) ? "selected" : "") . ">" . $value . "</option>";
          } 
        ?>
       </select>
</p>
<h4>Ingredients</h4>
<table>
  <tr>
    <td>Dehydrated Garlic:</td>
    <td><input type="radio" name="dehy_garlic" value="0" checked>Not sure</td>
    <td><input type="radio" name="dehy_garlic" value="1" <?php if(isset($data["dehy_garlic"]) && $data["dehy_garlic"] == 1) echo "checked"?> >Yes</td>
    <td><input type="radio" name="dehy_garlic" value="2" <?php if(isset($data["dehy_garlic"]) && $data["dehy_garlic"] == 2) echo "checked"?> >No</td>
  </tr>
  <tr>
    <td>Frozen Vegetables:</td>
    <td><input type="radio" name="frozen_vegetables" value="0" checked>Not sure</td>
    <td><input type="radio" name="frozen_vegetables" value="1" <?php if(isset($data["frozen_vegetables"]) && $data["frozen_vegetables"] == 1) echo "checked"?> >Yes</td>
    <td><input type="radio" name="frozen_vegetables" value="2" <?php if(isset($data["frozen_vegetables"]) && $data["frozen_vegetables"] == 2) echo "checked"?> >No</td>
  </tr>
  <tr>
    <td>Dehydrated Vegetables:</td>
    <td><input type="radio" name="dehy_vegetables" value="0" checked>Not sure</td>
    <td><input type="radio" name="dehy_vegetables" value="1" <?php if(isset($data["dehy_vegetables"]) && $data["dehy_vegetables"] == 1) echo "checked"?> >Yes</td>
    <td><input type="radio" name="dehy_vegetables" value="2" <?php if(isset($data["dehy_vegetables"]) && $data["dehy_vegetables"] == 2) echo "checked"?> >No</td>
  </tr>
  <tr>
    <td>Organic:</td>
    <td><input type="radio" name="organic" value="0" checked>Not sure</td>
    <td><input type="radio" name="organic" value="1" <?php if(isset($data["organic"]) && $data["organic"] == 1) echo "checked"?> >Yes</td>
    <td><input type="radio" name="organic" value="2" <?php if(isset($data["organic"]) && $data["organic"] == 2) echo "checked"?> >No</td>
  </tr>
</table>
<p>
Notes:<br>
<textarea class="textArea" name="notes" rows="4" cols="50"><?php if(isset($data["notes"])) echo $data["notes"]?></textarea>
</p>
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
<input type="hidden" name="mode" value="<?php if(isset($_GET['id']) && $_GET['id'] != "") echo 'edit'; else echo 'add'; ?>">
<input class="button-style-9" type="submit" value="SAVE">
</form>






