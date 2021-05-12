<?php
    session_start();
?>

<!--  WEPP Internet model interface: Management Schedule
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
-->

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Management Schedule</title>
<base target="_self">
</head>
<?php
	$name = $HTTP_GET_VARS["NAME"];
?>
<body>

<h2 align="center">Management Schedule<br>
<?php echo $name?> </h2>
<p align="left"><font color="#0000FF"><b>Description:</b></font><b><font color="#0000FF"><br>
Landuse: </font><font color="#000000">Cropland&nbsp; </font><font color="#0000FF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Drainage: </font><font color="#000000">None</font></b></p>
<table border="1" width="100%">
  <tr>
    <th width="10%" bgcolor="#C0C0C0"><font size="3"><b>Date</b></font></th>
    <th width="30%" bgcolor="#C0C0C0"><font size="3"><b>Operation Type</b></font></th>
    <th width="60%" bgcolor="#C0C0C0"><font size="3"><b>Operation Name</b></font></th>
  </tr>
<?php
	$outFile = "/home/wepp/" . session_id() . "/mandetails.txt";
	if (file_exists($outFile))
	   unlink($outFile);

	$rotName = "/home/wepp/data/managements/" . $name . ".rot";
	$cmd = "/home/wepp/wepp/wepprotation ";
	$msgs = " > /home/wepp/" . session_id() . "/msgs.txt"; 
	if (file_exists($rotName))
	   system($cmd . session_id() . " \"" . $rotName . "\" " . $outFile . $msgs);
	else
	   print("<p>Could not open file: '" . $rotName . "'<p>");	

	if (file_exists($outFile)) {
	   $handle = fopen($outFile,"r");
	   $s = fgets($handle,1024);    // skip initial conditions line
	   while ($s = fgets($handle,1024)) {
		$entries = explode('|',$s);

?>
		<tr>
			<td width="10%" bgcolor="#FFFFCC"><font size="2"><b><?php echo $entries[0]?></b></font></td>
			<td width="30%" bgcolor="#FFFFCC"><font size="2"><b><?php echo $entries[1]?></b></font></td>
			<td width="60%" bgcolor="#FFFFCC"><font size="2"><b><?php echo $entries[2]?></b></font></td>
		</tr>
<?php
	   }
	   fclose($handle);
       }
?>
</table>

</body>

</html>
