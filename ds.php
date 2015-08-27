<?php
session_start();

include_once "PHPExcel.php"; // used for creating an exportable table

include_once "db.php"; // used for database connection

include "constants.php";

// Redirect to login page if session data is not set yet
if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]) {
  header("Location: login.php"); // redirect the page
}

// Connect to database to retrieve data
$data = array();
if($_SESSION["name"] != "") {
  $con = dbConnect($_SESSION["name"], $_SESSION["pass"]);
}
else {
  $con = dbConnect();
}
// Check connection
if ($con)
{
  $result = mysqli_query($con, "SELECT * FROM " . $_SESSION["dbTable"]);
  if($result == false) {
    echo "Query error!<br>";
  }
  else {
    while($row = mysqli_fetch_array($result)) {
      array_push($data, $row);
    }
  }
}

// Exporting data in table into Excel spreadsheet
function exportData() {
  global $types, $sales, $locations, $states, $YES_NO, $data;
  // Setting up the object to be exported
  $objPHPExcel = new PHPExcel();
  $objPHPExcel->getProperties()->setCreator("Brian Tan");
  $objPHPExcel->getProperties()->setTitle("Office test document title");
  $objPHPExcel->getProperties()->setSubject("Office test document subject");
  $objPHPExcel->getProperties()->setDescription("Office test document description");
  $objPHPExcel->setActiveSheetIndex(0);
  $objPHPExcel->getActiveSheet()->setTitle("DS Marketing Research");
  $objPHPExcel->getActiveSheet()->setCellValue("A1", "Name");
  $objPHPExcel->getActiveSheet()->setCellValue("B1", "Type");
  $objPHPExcel->getActiveSheet()->setCellValue("C1", "Annual Sales");
  $objPHPExcel->getActiveSheet()->setCellValue("D1", "Location");
  $objPHPExcel->getActiveSheet()->setCellValue("E1", "City");
  $objPHPExcel->getActiveSheet()->setCellValue("F1", "State");
  $objPHPExcel->getActiveSheet()->setCellValue("G1", "Dehydrated Garlic");
  $objPHPExcel->getActiveSheet()->setCellValue("H1", "Frozen Vegetables");
  $objPHPExcel->getActiveSheet()->setCellValue("I1", "Dehydrated Vegetables");
  $objPHPExcel->getActiveSheet()->setCellValue("J1", "Organic");
  $objPHPExcel->getActiveSheet()->setCellValue("K1", "Notes");
  $row = 2;
  foreach($data as $item) {
    $col = "A";
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $item['name']);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $TYPES[$item['type']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $ANNUAL_SALES[$item['annual_sales']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $LOCATIONS[$item['location']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $item['city']);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $STATES[$item['state']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $YES_NO[$item['dehy_garlic']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $YES_NO[$item['frozen_vegetables']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $YES_NO[$item['dehy_vegetables']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $YES_NO[$item['organic']]);
    $objPHPExcel->getActiveSheet()->setCellValue($col++ . $row, $item['notes']);
    $row++;
  }
  // Set up the headers for saving out later
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment; filename="export_data.xls"');
  header('Cache-Control: max-age=0');
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  $objWriter->save("php://output"); // so that it saves on client side
}
// check if we want to export data
if(isset($_POST["export"]) && $_POST["export"] === "EXPORT") {
  exportData();
}

?>

<html>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="theme.blue.css">
<script type="text/javascript" src="jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="jquery.tablesorter.js"></script>
<script type="text/javascript" src="jquery.tablesorter.pager.js"></script>
<title>
DS Marketing Research
</title>
<body>

<form action="logout.php">
<input type="submit" class="logoutButton" value="Logout" <?php if(!$_SESSION["isLoggedIn"]) echo "hidden"; ?>>
</form>

<?php
// Only allow adding new data if Admin user is logged in
if($_SESSION["isAdmin"]) {
  echo '<br><a style="text-decoration:none" href="ds_edit.php?id="><span class="button-style-9">ADD NEW DATA</span></a>';
}
?>

<h2>List of Companies</h2>
<table id="dataTable" class="tablesorter" >
<thead>
  <tr>
    <th rowspan="2" style="width:10px"></th>
    <th rowspan="2" style="width:20%">Name</th>
    <th rowspan="2">URL</th>
    <th rowspan="2">Type</th>
    <th rowspan="2">Annual Sales&nbsp&nbsp&nbsp</th>
    <th rowspan="2">Location&nbsp&nbsp&nbsp</th>
    <th rowspan="2">City&nbsp&nbsp</th>
    <th rowspan="2">State&nbsp&nbsp</th>
    <th colspan="4" style="text-align:center">Ingredients</th>
    <th rowspan="2">Notes</th>
  </tr>
  <tr>
    <th width="60px">Dehy. Garlic</th>
    <th width="60px">Frozen Veg.</th>
    <th width="60px">Dehy. Veg.</th>
    <th width="60px">Organic&nbsp&nbsp</th>
  </tr>
</thead>
<tbody>
<?php foreach($data as $item): ?>
  <tr>
    <td>
    <?php 
        if($_SESSION["isAdmin"]) {
          echo "<button type='button' class='deleteButton' onclick='checkDelete(" . $item['id'] . ");'>DEL</button>";
        }
    ?>
    </td>
    <td>
      <?php 
        if($_SESSION["isAdmin"]) {
          echo "<a href='ds_edit.php?id=" . $item['id'] . "'>" . $item['name'] . "</a>";
        }
        else {
          echo $item['name'];
        }
      ?>
    </td>
    <td><a href="http://<?=$item['url']?>" target="_blank"><img src="open_in_new_window.png" width="32"/></a></td>
    <td><?=$TYPES[$item['type']]?></td>
    <td><?=$ANNUAL_SALES[$item['annual_sales']]?></td>
    <td><?=$LOCATIONS[$item['location']]?></td>
    <td style="max-width:60px"><?=$item['city']?></td>
    <td><?=$STATES[$item['state']]?></td>
    <td><?=$YES_NO[$item['dehy_garlic']]?></td>
    <td><?=$YES_NO[$item['frozen_vegetables']]?></td>
    <td><?=$YES_NO[$item['dehy_vegetables']]?></td>
    <td><?=$YES_NO[$item['organic']]?></td>
    <td width="10%"><?php echo $item['notes']; ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>

<div id="pager" class="pager">
  <form>
    <img src="first.png" class="first"/>
    <img src="prev.png" class="prev"/>
    <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
    <img src="next.png" class="next"/>
    <img src="last.png" class="last"/>
    <select class="pagesize">
      <option selected="selected" value="10">10</option>
      <option value="20">20</option>
      <option value="30">30</option>
      <option value="40">40</option>
    </select>
  </form>
</div>

<form action="<?=$_SERVER['PHP_SELF']?>" method="post" <?php if(!$_SESSION["isAdmin"]) echo "hidden"; ?>>
  <input class="exportButton" type="submit" name="export" value="EXPORT">
</form>

<form action="<?=$_SERVER['PHP_SELF']?>" method="post" <?php if($_SESSION["isLoggedIn"]) echo "hidden"; ?>>
  <em>Administrator login</em><br>
  Username: <input class="inputArea" type="text" name="user"><br>
  Password: <input class="inputArea" type="password" name="pass"><br>
<input class="inputArea" type="submit" name="userLogin" value="Login">
</form>

<form id="deleteForm" action="ds_add.php" method="post">
  <input type="hidden" name="id" value="" id="deleteId">
  <input type="hidden" name="mode" value="delete">
</form>

<div style="font-size:8px; font-family:Tahoma;">
  This is a work in progress...<br>
  More features coming soon:
  <ul>
    <li>pages</li>
    <li>searchable?</li>
    <li>highlights?</li>
    <li>better security?</li>
  </ul>
  Currently logged in as <?=$_SESSION["name"]?>
</div>
</body>
</html>

<script type="text/javascript">
function checkDelete(id) {
  var response = confirm("Are you sure?");
  if(response) {
    document.getElementById("deleteId").value = id;
    document.getElementById("deleteForm").submit();
  }
}

$(document).ready(function(){
  var pagerOptions = {
    // target the pager markup - see the HTML block below
    container: $(".pager"),
    // output string - default is '{page}/{totalPages}'
    // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
    output: '{startRow} to {endRow} ({totalRows})',
  };

  $('#dataTable')
    .tablesorter({
      theme: 'blue',
      widthFixed: true, 
      widgets: ['zebra'],
      headers: { 0: {sorter:false}, 8: {sorter: false} } // Disable "Ingredients" as a sortable trigger
    })

    // bind to pager events
    // *********************
    .bind('pagerChange pagerComplete pagerInitialized pageMoved', function(e, c){
      var msg = '"</span> event triggered, ' + (e.type === 'pagerChange' ? 'going to' : 'now on') +
        ' page <span class="typ">' + (c.page + 1) + '/' + c.totalPages + '</span>';
      $('#display')
        .append('<li><span class="str">"' + e.type + msg + '</li>')
        .find('li:first').remove();
    })
    // initialize the pager plugin
    // ****************************
    .tablesorterPager(pagerOptions);
});
</script>




