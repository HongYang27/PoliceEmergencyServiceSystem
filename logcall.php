<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link rel="stylesheet" type="text/css" href="hongyang_pess.css">
</head>
<body>

<script>
function meaningful()
	{
		var x=document.forms["formLogCall"]["callName"].value;
		if (x==null || x=="")
		{
			alert("Please Enter Caller Name!");
			return false;
		}
	}
function meaningful() {
		var x = document.forms["formLogCall"]['contactNo'].value;
		if(x == "" || x=="") {
			alert("Contact Number Must Be In!");
			return false;
		}
	}
function meaningful() {
		var x = document.forms["formLogCall"]['description'].value;
		if(x == "") {
			alert("Description Must Be Filled Out!");
			return false;
		}
	}
</script>
<div class="container">
<?php require_once 'nav.php';?> 
<?php require_once 'db_config.php'; 

$mysqli = mysqli_connect("localhost", "root", "", "hongyang_pessdb");

if ($mysqli->connect_error)
{
	die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}

$sql = "SELECT * FROM incidenttype";
if (!($stmt = $mysqli->prepare($sql)))
{
	die("Unable to prepare: ".$mysqli->errno);
}

if (!$stmt->execute())
{
	die("Cannot be executed: ".$stmt->errno);
}
	
if (!($resultset = $stmt->get_result())) {
	die("Result set malfunction: ".$stmt->errno);
}
	
	$incidentType; 
	
while ($row = $resultset->fetch_assoc()) {
	$incidentType[$row['incidentTypeId']] = $row['incidentTypeDesc'];
				 
}

$stmt->close();
	
$resultset->close();
	
$mysqli->close();
	
?>

 <form name="formLogCall" method="post" action="dispatch.php" onSubmit="return meaningful();">
<fieldset>
<legend>Log Call</legend> 
	<table width="53%" border="4" align="center" cellpadding="6" cellspacing="6">
	<tr>
	<td width="60%">Caller Name : </td>
	<td width="50%"><input type="text" name="callName" id="callName"></td>
	</tr>
	<tr>
	<td width="50%">Contact No : </td>
	<td width="50%"><input type="text" name="contactNo" id="contactNo"></td>
	</tr>
	<tr>
	<td width="50%">Incident Type : </td>
	<td width="50%">
	<select name="incidentType" id="incidentType">
    <?php foreach($incidentType as $key=> $value) {?>
	<option value="<?php echo $key ?> " >
		<?php echo $value ?> </option>
	<?php }	?>
	</td>
	</tr>
	<tr>
	<td width="50%">Location : </td>
	<td width="50%"><input type="text" name="location" id="location"></td>
	</tr>
	<tr>
	<td width="50%">Description : </td>
	<td width="50%"><textarea name="description" id="description" cols="65" rows="10"></textarea></td>
	</tr> 
	<tr>
	<td><input type="reset" name="Cancel" id="cancel" value="Clear"</td>
	<td><input type="submit" name="Submit" id="Submit" value="Proceed"</td>
	</tr>
    <marquee behaviour="alternate" direction="down" scrollamount="3"><li><mark>[Server Update 20/4]</li></mark></marquee>
    <marquee behaviour="alternate" direction="down" scrollamount="3"><li>[Database Checked 5/4]</li></marquee>
	</table>
	</div>
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
</fieldset>
</form>
</body>
</html>
