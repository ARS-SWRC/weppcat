<!--  WEPP Internet model interface: Soil Names
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
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Soil Names for a State</title>
<base target="_self">
</head>

<body>
<?php

	// Connnect to database
  	if (!($Connection = mysql_connect("127.0.0.1", "weppcat")))
         {
            print("Could not establish connection.<BR>\n");
            exit;
         }

	$state = $HTTP_GET_VARS["ST"];
	$letter = $HTTP_GET_VARS["IN"];
    $stateLong = $HTTP_GET_VARS["State"];

	if (($state == "") && ($stateLong == "")) {
	}
	
	include 'funcs.php';

    if ($state == null)
	   $state = toStateAbbr($stateLong);

 	if ($letter == null)
	   $letter = "A";
	
	print($stateLong . $state);
				
?>				
				
<h2 align="center">Soil Records for <?php echo $state?></h2>
<p align="left"><font size="2"><a href="soil_names.php?IN=A&ST=<?php echo $state?>">A</a>
<a href="soil_names.php?IN=B&ST=<?php echo $state?>">B</a>
<a href="soil_names.php?IN=C&ST=<?php echo $state?>">C</a>
<a href="soil_names.php?IN=D&ST=<?php echo $state?>">D</a>
<a href="soil_names.php?IN=E&ST=<?php echo $state?>">E</a>
<a href="soil_names.php?IN=F&ST=<?php echo $state?>">F</a>
<a href="soil_names.php?IN=G&ST=<?php echo $state?>">G</a>
<a href="soil_names.php?IN=H&ST=<?php echo $state?>">H</a>
<a href="soil_names.php?IN=I&ST=<?php echo $state?>">I</a> 
<a href="soil_names.php?IN=J&ST=<?php echo $state?>">J</a> 
<a href="soil_names.php?IN=K&ST=<?php echo $state?>">K</a>
<a href="soil_names.php?IN=L&ST=<?php echo $state?>">L</a>
<a href="soil_names.php?IN=M&ST=<?php echo $state?>">M</a> 
<a href="soil_names.php?IN=N&ST=<?php echo $state?>">N</a>
<a href="soil_names.php?IN=O&ST=<?php echo $state?>">O</a> 
<a href="soil_names.php?IN=P&ST=<?php echo $state?>">P</a> 
<a href="soil_names.php?IN=Q&ST=<?php echo $state?>">Q</a>
<a href="soil_names.php?IN=R&ST=<?php echo $state?>">R</a> 
<a href="soil_names.php?IN=S&ST=<?php echo $state?>">S</a> 
<a href="soil_names.php?IN=T&ST=<?php echo $state?>">T</a>
<a href="soil_names.php?IN=U&ST=<?php echo $state?>">U</a>
<a href="soil_names.php?IN=V&ST=<?php echo $state?>">V</a>
<a href="soil_names.php?IN=W&ST=<?php echo $state?>">W</a>
<a href="soil_names.php?IN=X&ST=<?php echo $state?>">X</a>
<a href="soil_names.php?IN=Y&ST=<?php echo $state?>">Y</a> 
<a href="soil_names.php?IN=Z&ST=<?php echo $state?>">Z</a>
<a href="soil_names.php?IN=*&ST=<?php echo $state?>"><font color="#008080">All Records</font></a></font></p>
<?php

  if ($letter != "*") 
     $sqlstmt = "SELECT name,soil_id from soils where state='" . $state . "' and name" . " LIKE '" . $letter . "%' order by name";
  else
     $sqlstmt = "SELECT name,soil_id from soils where state='" . $state . "' order by name"; 

  //execute the select statement
  if(!($Result = mysql_db_query("wepp", $sqlstmt, $Connection )))
  {
     print("Could not execute query: ");
     print($sqlstmt);
     print(mysql_error($Connection));
     mysql_close($Connection);
     print("<BR>/n");
     exit;
  }  
 
  $Rows = mysql_num_rows($Result);
  if ($Rows == 0) {
     echo "<center>";
     echo "There are no records.";
     echo "</center>";
     exit;
  }

?>

<hr>
<?php if (letter != "*") ?>
Soils starting with <?php echo letter?>

<table border="0" width="100%">
<?php
	$col = 1;
	for ($Row=0; $Row < mysql_num_rows($Result); $Row++) {
           $name = mysql_result($Result,$Row,0);
	   $id = mysql_result($Result,$Row,1);
?>


<?php if ($col == 1)  {?>
  <tr>
<?php } ?>
    <td width="25%"><font size="1"><a href="soildetails.php?ID=<?php echo $id?>"><?php echo $name?></a></font></td>
<?php if ($col == 4) {
     $col = 0;
?> 
  </tr>
<?php
   }
   $col = $col + 1;     
   }
   if ($col != 1) 
?>
   </tr>
  
</table>
<?php
   mysql_free_result($Result);
   mysql_close($Connection);
?>
<hr>

</body>
</html>
