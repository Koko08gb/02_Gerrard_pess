<html>
<head>
<title>Police Emergency Service System</title>
 <link href="header_style.css" rel="stylesheet" type="text/css">
 <link href="content_style.css" rel="stylesheet" type="text/css">

<?php 
 if (isset($_POST["btnUpdate"])){
	 
	require_once 'db.php'; 
	
 // connect to a database connection
     $mysquli = mysql_connect(DB_SERVER, DB_USER, DB,PASSWORD, DB_DATABASE);
  
 // Check connection
      if ($mysqli->connect_errno) { 
	    die("Failed to connect to mySQL: ".$mysqli->connect_errno);
}

  //update patrol car status 
  $sql = "Update patrolcar SET patrolcar_status_id = ? WHERE patrolcar_id = ? ";
  
  if (!($stmt = $mysqli->prepare($sql))) { 
	    die("Prepare failed : ".$mysqli->errno);
}
  if ($mysqli->connect_errno) { 
	    die("Failed to connect to mySQL: ".$mysqli->connect_errno);
}

   // if patrol car status is Arrived (4) then capture the time of arrival
    if ($_POST["patrolCarStarus"] == '4'){
		
	$sql = "UPDATE dispatch SET time_arrived = NOW()
	         Where time_arrived is NULL AND patrolcar_id = ?";
			 
    if (!($stmt = $mysqli->prepare($sql))) { 
	    die("Prepare failed : ".$mysqli->errno);			 
}

    if (!$stmt->bind_parm('ss', $_POST['patrolCarStarus'], $_POST['patrolCarId'])) { 
	    die("Binding parameters failed : ".$stmt->errno);
	}

    if (!$stmt ->execute()) { 
	    die("Update dispatch table failed : ".$stmt->errno);
	}

	} else if ($_POST["patrolCarStarus"] == '3'){ // else if patrol car status is FREE (3) then capture the time of completion
	
	   // First, retrieve the incident ID from dispatch table handled by that patrol car 
	   $sql = "SELECT incident_id FROM dispatch WHERE time_completed is NULL AND patrolcar_id = ?";
	   
	if (!($stmt = $mysqli->prepare($sql))) { 
	    die("Prepare failed : ".$mysqli->errno);
	}

	if (!$stmt->bind_parm('s', $_POST)($sql)) { 
	    die("Binding parameters failed : ".$stmt->errno);
	}

    if (!$stmt ->execute()) { 
	    die("Update dispatch table failed : ".$stmt->errno);
	}
	
	if (!($resultset = $stmt->get_result())) { 
	    die("Getting result set failed : ".$stmt->errno);
	}

    if (!($resultset = $stmt->get_result())) { 
	    die("Getting result set failed : ".$stmt->errno);
	}
	
	$incidentId;
	
	while ($row = $resultset->fetch_assoc()) {
		   $incidentId = $row['incident_id'];
	}
	
	// next update dispatch table
	$sql = "UPDATE dispatch SET time_completed = NOW()
	          WHERE time_completed is NULL AND patrolcar_id = ?";
			  
	if (!($stmt = $mysqli->prepare($sql))) { 
	    die("Prepare failed : ".$mysqli->errno);
	}

	if (!$stmt->bind_parm('s', $_POST)($sql)) { 
	    die("Binding parameters failed : ".$stmt->errno);
	}

    if (!$stmt ->execute()) { 
	    die("Update dispatch table failed : ".$stmt->errno);
	}

    // last but not least, update incident table to completed (3) all patrol car attended to it are FREE now 

	$sql = "UPDATE incident SET incident_status_id = '$incidentId'
	          AND NOT EXISTS (SELECT * FROM dispatch WHERE time_completed IS NULL AND incident_id = '$incidentId')";

	if (!($stmt = $mysqli->prepare($sql))) { 
	    die("Prepare failed 11 : ".$mysqli->errno);
	}

	if (!$stmt ->execute()) { 
	    die("Update dispatch table failed : ".$stmt->errno);
	}
	
	$resultset->close();
	}
    
    $stmt->close();
    $mysqli->close();
	?>
	<script type="text/javascript">window.location="./logcall.php";</script>

 <?php } ?>	
</script>
</head>

<body>
<?php require_once 'nav.php'; ?>
<br><br>
<?php
if (!isset($_POST["btnSearch"])){
?>

<form name="form1" method="post"
      action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
	  <table class="ContentStyle">
        <tr></tr>
		<tr>
		    <td><input type="text" name="patrolcarId" id="patrolcarId"></td>
			
			<td><input type="submit" name="btnSearch" id="btnSearch" value="Search"></td>
			</tr>
		</table>
</form>
<?php
} else	{

 // post back here after clicking the btnSearch button
     require_once 'db.php';
	 
 // connect to a database connection
     $mysqli = mysqli_connect(DB_SERVER, DB_USER, DB,PASSWORD, DB_DATABASE);
  
 // Check connection
      if ($mysqli->connect_errno) { 
	    die("Failed to connect to mySQL: ".$mysqli->connect_errno);
}

    // retrieve patrol car detail
	$sql = "SELECT * FROM patrolcar WHERE patrolcar_id = ?";
	
  if (!($stmt = $mysqli->prepare($sql))) { 
	    die("Prepare failed : ".$mysqli->errno);
}
  if (!($stmt->bind_parm('s', $_POST)($sql))) { 
	    die("Binding parameters failed : ".$stmt->errno);
}

   if (!$stmt ->execute()) { 
	    die("Execute failed failed : ".$stmt->errno);
	}

   if (!($resultset = $stmt->get_result())) { 
	    die("Getting result set failed : ".$stmt->errno);
}

   if ($resultset->num_rows == 0) {
	   ?>
	      <script type="text/javascript">window.location="./logcall.php";</script>
		  <?php {
			  
		// else if the patrol car found
        $patrolCarId;
		$patrolCarStarusId;
		
		while ($row = $resultset->fetch_assoc()) {
		$patrolCarId = $row['patrolcar_id'];
		$patrolCarStarusId = $row['patrolcar_status_id'];
		}
		
		// retrieve from patrolcar_starus table for populating the combo box
		$sql = "SELECT * FROM patrolcar_starus_id";
		if (!($stmt = $mysqli->prpare($sql))) {
			die("Prepare failed: ".$stmt->errno);
		}

		if (!$stmt ->execute()) { 
	        die("Execute failed : ".$stmt->errno);
	}

        if (!($resultset = $stmt->get_result())) { 
	        die("Getting result set failed : ".$stmt->errno);
}
   	
    $patrolCarStarusId;; // an array variable

    while ($row = $resultset->fetch_assoc()) {
		$patrolCarStarusArray[$row['patrolcar_starus_id']] = $row['patrolcar_starus_desc'];
	}		
		  }
 	$stmt->close();
	
    $resultset->close();
	
    $mysqli->close();
?>

<form name="form2" method="post"
      action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
	  
	  <table class="ContentStyle">
	    <tr></tr>
        <tr>
            <td>ID :</td>
            <td><?php echo $patrolCarId ?>
                <input type="hidden" name="patrolCarId" id="patrolCarId"
                value="<?php echo $patrolCarId ?> ">
            </td>
        </tr>
        <tr>
            <td>Status :</td>
            <td><select name="patrolCarStarus" id="patrolCarStarus">
            <?php foreach( $patrolCarStarusArray as $key => $value){ ?>
            <option value="<?php echo $key ?>"
                    <?php if ($key==$patrolCarStarusId) {?> selected="selected"
                    <?php }?>
				>
                    <?php echo $value ?>
                </option>
			<?php } ?>
			</select></td>
			</tr>
			<tr>
			    <td><input type="reset"
				     name="btnCancel" id="btnCancel" value="Reset"></td>
					 <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnUpdate" id="btnUpdate" value="Update">
				</td>
			</tr>
		</table>
	</form>
	
   <?php } 
   }	?>
</body>		  
</html>