<!--  WEPP Internet model interface: Management Scenario Names
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
<title>Management Scenario Names</title>
<base target="_self">
</head>

<body>

<div align="left">
  <table border="1" width="100%" height="32">
    <tr>
      <td width="1%" bgcolor="#CCCCFF" height="21"><b><font size="2">Num</font></b></td>

      <td width="43%" bgcolor="#CCCCFF" height="21"><b><font size="2">Management
        Name</font></b></td>
    </tr>
<?php
    // DE_EDIT Connnect to database
    if (!($Connection = mysql_connect("127.0.0.1", "weppcat")))
         {
            print("Could not establish connection.<BR>\n");
            exit;
         }  
    $sqlstmt = "SELECT name from managements order by name";

    if (!($Result = mysql_db_query("wepp", $sqlstmt, $Connection )))
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
                print("<center>");
                print("There are no records.");
                print($sqlstmt);
                print("</center>");
		mysql_free_result($Result);

                exit;
	}
	$color = "#FFFFCC";

	for ($Row=0; $Row < mysql_num_rows($Result); $Row++) {
           $name = mysql_result($Result,$Row,"name");
	   $ename = $name;
		    		    
?>

    <tr>
      <td width="1%" height="21" bgcolor="<?php echo $color?>"><?php echo $Row+1?></td>

      <td width="43%" height="21" bgcolor="<?php echo $color?>"><a href="man_details.php?NAME=<?php echo $ename?>"><?php echo $name?></a></td>
    </tr>
<?php
 		if ($color == "#FFFFCC")
 	   	   $color = "#FFFFFF";
 		else
 	   	   $color = "#FFFFCC";
     }
     mysql_free_result($Result);
     // DE_EDIT
     mysql_close($Connection);

?>
   </table>
</div>

</body>

</html>
