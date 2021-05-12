<?php
session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>Result Modify Climate</title>
</head>
<?php
// Checks if modivied Climate is saved
$_SESSION['useModCli']=true;
// Get use of Assess Filter Strip
$_SESSION['useaf']= $HTTP_GET_VARS["useaf"];

// If request comes form Assess Filter Strip
If ($_SESSION['useaf'] == "true"){
?>
<body>
<table width="100%" height="100%">
	<tr>
		<td style="vertical-align:middle; text-align:center">	
		    <h2>Modified Climate has been successfully saved
		    <br><br>
		    is ready for use with this Assess Filter Strip.
		    <br><br>
		    User may continue with "Compute New Filter Strip".</h2>
		</td>
	</tr>
</table>
</body>
<?php
}
// If request comes form Assess Change Scenario
else {
?>
<body>
<table width="100%" height="100%">
	<tr>
		<td style="vertical-align:middle; text-align:center">	
		    <h2>Modified Climate has been successfully saved 
		    <br><br>
		    and is ready for use with this Assess Change Scenario.
		    <br><br>
		    User may continue to choose Management inputs and/or 
		    <br><br>
		    "Run Assess Change Scenario".</h2>
		</td>
	</tr>
</table>
</body>
<?php
}
?>
</html>
