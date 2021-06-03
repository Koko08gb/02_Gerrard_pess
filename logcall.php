<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
 <link href="header_style.css" rel="stylesheet" type="text/css">
 <link href="content_style.css" rel="stylesheet" type="text/css">
 <script type="text/javascript">
 // test
 function validateForm()
 {
	 
     var x=document.forms["frmLogCall"]["contact no"].value;
	 if(x == null || x=="")
	 {
		 alert("Contact No. is required");
		 return false;
	 }
	 // may add code for validating other inputs
 }
 </script>
</head>
<body class="ContentStyle">
<?php // import nav.php
require_once 'nav.php';
// import db.php
require_once 'db.php';

// Create datatbase connection
 $mysqli =  mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
// Create connection
if ($mysqli->connect_errno) 
{
	die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}

$sql = "SELECT * FROM incident_type";

if (!($stmt = $mysqli->prepare($sql))){
	die("Prepare FAILED: ".$mysql->errno);
}
if (!$stmt->execute()) {
     die("Execute failed: ".$stmt->errno);
}

if (!($resultset = $stmt->get_result())) {
     die("Getting result set failed: ".$stmt->errno);
}

$incidentType; // an array variable

while ($row = $resultset->fetch_assoc()) {
     // create an associative array of $incidentType {incident_type_id, incident_type_desc]
	 $incidentType[$row['incident_type_id']] = $row['incident_type_desc'];
	 }	 
	 
$stmt->close();
$resultset->close();
$mysqli->close();





?>



<br><br>
<form name="frmLogCall" method="post"
    onSubmit="return validateForm()" action="dispatch.php">
<table class="ContactStyle">
  <tr>
     <td colspan="2">Log call Panel</td>
  </tr>

  <tr>  
      <td>Caller's Name :</td>
	  <td><input type="text" name="callerName" id="callerName">
		  
	  </td>
	  </tr>
	<tr>  
      <td>Contact No :</td>
	  <td><input type="text" name="contactNo" id="contactNo">
		  
	  </td>
	  </tr>  
	  <tr>  
      <td>Location :</td>
	  <td><input type="text" name="location" id="location">		  
	  </td>
	  </tr>
	  <tr>  
      <td>Incident Type :</td>
	  
	  <td><select name="incidentType" id="incidentType">
	  <?php // populate a combo box with $incidentType
	        foreach ( $incidentType as $key => $value) {
		?>
                    <option value="<?php echo $key ?>">
                            <?php echo $value ?>
                    </option>
        <?php
            }
        ?>			
	  </select></td>
	      
	  </tr>
	  <tr>  
      <td>Description :</td>
	  <td><textarea name="incidentDesc" id="incidentDesc" cols="45"
	        rows="5"></textarea>
	   </td>		
	  </tr>
	  <tr>
	    <td><input type="reset" name="btnCancel" id="btnCancel" value="Reset">
		</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnProcessCall" id="btnProcessCall" value="Process Call...">
		
		
		</td>
		</tr>
		</table>
		</form>
		</body>
		</html>
		
		
		
		