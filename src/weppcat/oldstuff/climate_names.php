<!--  WEPP Internet model interface: Climate Stations for State
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
  --  Customized for WEPPCAT
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
-->
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Climate Stations for State</title>
</head>

<body>
<?php
	
        if (!($Connection = mysql_connect("127.0.0.1", "weppcat")))
         {
            print("Could not establish connection.<BR>\n");
            exit;
         }

	 $stateLong = $HTTP_GET_VARS["State"];

	include('funcs.php');

	$state = toStateAbbr($stateLong);

	$state = strtolower($state);    // climate table has lower case names
	
	$sqlstmt = "SELECT station,id,years,elevation from climates where state = '" . $state . "' order by station";
	
	if(!($Result = mysql_db_query("wepp", $sqlstmt, $Connection )))
  	{
     		print("Could not execute query: ");
     		print($sqlstmt);
     		print(mysql_error($Connection));
     		// DE_EDIT
     		mysql_close($Connection);
     		print("<BR>\n");
     		exit;
  	}

	 $Rows = mysql_num_rows($Result);
  	if ($Rows == 0) {
     		echo "<center>";
     		echo "There are no records.";
                echo $sqlstmt;
     		echo "</center>";
     		exit;
  	}

?>				
				
<h2 align="center">Climate Stations for <?php echo $stateLong?></h2>

<div align="center">
  <center>
  <table border="1" width="75%" bgcolor="#FFFFCC">
    <tr>
      <td width="50%" bgcolor="#C0C0C0"><b>Station</b></td>
      <td width="25%" bgcolor="#C0C0C0"><b>Elevation (ft)</b></td>
      <td width="46%" bgcolor="#C0C0C0"><b>Years of Record</b></td>
    </tr>
<?php
	for ($Row=0; $Row < mysql_num_rows($Result); $Row++) {
           $id = mysql_result($Result,$Row,"id");
	   $station =  mysql_result($Result,$Row,"station");
	   if (strlen($id) < 6)
              $id = "0" . $id;
	   
	   $elev = mysql_result($Result,$Row,"elevation");
	   $years = mysql_result($Result,$Row,"years");

   
?>
<tr>
	<td width="50%"><a href="climate_details.php?ST=<?php echo $state?>&ID=<?php echo $state?><?php echo $id?>"><?php echo $station?></a></td>
   <td width="25%"><?php echo $elev?></td>
   <td width="25%"><?php echo $years?></td>
</tr>
<?php
       }  // end for-loop

       mysql_free_result($Result);
       mysql_close($Connection);
?>
  </table>
  </center>
</div>

</body>

</html>
