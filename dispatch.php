<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link rel="stylesheet" type="text/css" href="hongyang_pess.css">
</head>
<body>
<div class="container">
<!-- display the incident information passed from logcall.php -->
<form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
<table width="50%" border="2" align="center" cellpadding"5" cellspacing="5">

<?php require_once 'nav.php';?> 
	
<?php 
if (isset($_POST["btnDispatch"]))
{
	require_once 'db_config.php';
	
	//create database connection
	$mysqli = mysqli_connect("localhost", "root", "", "hongyang_pessdb");
	// Check connection
	if ($mysqli->connect_errno)
	{
		die("Failed to connect to MySQL: ".$mysqli->connect_errno);
	}
	
	$patrolcarDispatched = $_POST["chkPatrolcar"];
	$numofPatrolcarDispatched = count($patrolcarDispatched);
	
	// insert new incident
	$incidentStatus;
	if ($numofPatrolcarDispatched > 0) {
		$incidentStatus='2';
	} else {
		$incidentStatus='1';
	}
	
	$sql = "INSERT INTO incident (callerName, phoneNumber, incidentTypeId, incidentLocation, incidentDesc,
	incidentStatusId) VALUES (?, ?, ?, ?, ?, ?)";
	
	if (!($stmt = $mysqli->prepare($sql)))
	{
		die("Prepare failed: ".$mysqli->errno);
	}
	
	if (!$stmt->bind_param('ssssss', $_POST['callName'],
						  $_POST['contactNo'],
						  $_POST['incidentType'],
						  $_POST['location'],
						  $_POST['description'],
						  $incidentStatus))
	{
		die("Binding parameters failed: ".$stmt->errno);
	}
	
	if (!$stmt->execute())
	{
		die("Insert incident table failed: ".$stmt->errno);
	}
	
	// retrieve incident_id for the newly inserted incident
	$incidentId=mysqli_insert_id($mysqli);
	
	// update patrolcar status table and add into dispatch table
	for($i=0; $i < $numofPatrolcarDispatched; $i++)
	{
		// update patrol car status
		$sql = "UPDATE patrolcar SET patrolcarStatusId = '1' WHERE patrolcarId = ?";
		
		if (!($stmt = $mysqli->prepare($sql))) {
			die("Prepare failed: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('s', $patrolcarDispatched[$i])){
			die("Binding parameters failed: ".$stmt->errno);
		}
		
		if (!$stmt->execute()) {
			die("Update patrolcar_status table failed: ".$stmt->errno);
		}
		
		// insert dispatch data
		$sql = "INSERT into dispatch (incidentId, patrolcarId, timeDispatched) VALUES (?, ?, NOW())";
		
		if (!($stmt = $mysqli->prepare($sql))) {
			die ("Prepare failed: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('ss', $incidentId,
							  $patrolcarDispatched[$i])){
			die("Binding parameters failed: ".$stmt->errno);
		}
		
		if (!$stmt->execute()) {
			die("Insert dispatch table failed: ".$stmt->errno);
		}
	}
	
	$stmt->close();
	
	$mysqli->close();

} ?>
	<tr>
		<td colspan="10">Incident Detail</td>
	</tr>
	<tr>
		<td width="50%">Caller's Name : </td>
		<td><input type="text" name="callName" id="callName" value="<?php echo $_POST['callName'] ?>" readonly>
        </td>
	</tr>
	<tr>
		<td width="50%">Contact No : </td>
		<td><input type="text" name="contactNo" id="contactNo" value="<?php echo $_POST ['contactNo']?>" readonly>
        </td>
	</tr>
	<tr>
		<td width="50%">Location : </td>
		<td><input type="text" name="location" id="location" value="<?php echo $_POST ['location']?>" readonly>
        </td>
	</tr>
	<tr>
		<td width="40%">Incident Type : </td>
		<td><input type="text" name="incidentType" id="incidentType" value="<?php echo $_POST ['incidentType']?>" readonly>
		</td>
	</tr>
	<tr>
		<td width="50%">Description : </td>
		<td><textarea name="description" cols="50" rows="6" readonly id="description" readonly><?php echo $_POST['description'] ?></textarea> </td>
	</tr>
</table>
<?php
// connect to a database
require_once 'db_config.php';

// create database connection
$mysqli = mysqli_connect("localhost", "root", "", "hongyang_pessdb");
// check connection
if ($mysqli->connect_errno) {
die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}

//retrieve from patrolcar table those patrol cars that are 2:Patrol Car 3: Free
$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_status
ON patrolcar.patrolcarStatusId=patrolcar_status.StatusId
WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId='3'";

if (!($stmt = $mysqli->prepare($sql))) {
die("Prepare Failed: ".$mysqli->errno);
}

if (!$stmt->execute()) {
die("Execute Failed: ".$stmt->errno);
}

if (!($resultset = $stmt->get_result())) {
die("Gtting result set failed: ".$stmt->errno);
}

$patrolcarArray;

while ($row = $resultset->fetch_assoc()) {
$patrolcarArray[$row['patrolcarId']] = $row['statusDesc'];
}

$stmt->close();

$resultset->close();

$mysqli->close();
?>
	
<!-- populate table with patrol car data -->
<br><br><table border="1" align="center">
	<tr>
		<td colspan="3">Dispatch Patrolcar Panel</td>
	</tr>
	<?php
	foreach($patrolcarArray as $key=>$value){
	?>
	<tr>
		<td><input type="checkbox" name="chkPatrolcar[]"
				   value="<?php echo $key?>"></td>
	<td><?php echo $key ?></td>
	<td><?php echo $value ?></td>
	</tr>
	<?php  }  ?>
	<tr>
		<td><input type="reset" name="btnCancel" id="btnCancel" value="Reset"></td>
		<td colspan="2" align="center" width="80%"><input type="submit" name="btnDispatch" id="btnDispatch" value="Dispatch">
	</td>
	</tr>
	</div>
</table>
       <style>
.footer {
   position:inherit;
   left: 0;
   bottom: 0;
   width: 100%;
   background-color: black;
   color: white;
   text-align: center;
}
</style>
     <div class="footer">
  <p>Copyright © 2020 Hong Yang. All Rights Reserved.</p>
</div>
</form>
</body>
</html>
