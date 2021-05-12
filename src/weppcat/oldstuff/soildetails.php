<!--  WEPP Internet model interface: Soil Details
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
  --  Customized for WEPPCAT
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
-->
<?php
  // Connnect to database
  if (!($Connection = mysql_connect("127.0.0.1", "weppcat")))
         {
            print("Could not establish connection.<BR>\n");
            exit;
         }

  // Create SQL statement to list all members
  $id = $HTTP_GET_VARS["ID"];

  //create SQL statement to list all members
  $SQLstat = "SELECT * FROM soils where soil_id='" . $id ."'";

  // Execute the select statement
  if(!($Result = mysql_db_query("wepp", $SQLstat, $Connection )))
  {
     print("Could not execute query: ");
     print(mysql_error($Connection));
     mysql_close($Connection);
     print("<BR>\n");
     exit;
  }

  $Rows = mysql_num_rows($Result);
  if ($Rows == 1) {
      $name = mysql_result($Result, 0, "name");
      $state = mysql_result($Result, 0, "state");
      $texture =   mysql_result($Result, 0, "texture");
      $layers =   mysql_result($Result, 0, "layers");
      $albedo =   mysql_result($Result, 0, "albedo");
      $sat =   mysql_result($Result, 0, "sat");
      $interrill =   mysql_result($Result, 0, "interrill");
      $rill =   mysql_result($Result, 0, "rill");
      $shear =   mysql_result($Result, 0, "shear");
      $conduct =   mysql_result($Result, 0, "conduct");

  }

  mysql_free_result($Result);

  // Create SQL statement to list all members
  $SQLstat = "SELECT * FROM layers where soil_id=$id";

  // Execute the select statement
  if(!($Result = mysql_db_query("wepp", $SQLstat, $Connection )))
  {
     print("Could not execute query: ");
     print(mysql_error($Connection));
     mysql_close($Connection);
     print("<BR>\n");
     exit;
  }

?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>WEPP Soil Details</title>
</head>

<body>

<h2 align="center">WEPP Soil Details for <?php echo $name?> (<?php echo $state?>)</h2>
<hr>
<table border="0" width="100%">
  <tr>
    <td width="50%"><b>Soil Texture: </b><font color="#FF0000"><?php echo $texture?></font></td>
    <td width="50%"><b>Albedo:</b> <font color="#FF0000"><?php echo $albedo?></font></td>
  </tr>
  <tr>
    <td width="50%"><b>Initial Saturation Level: </b><font color="#FF0000"><?php echo $sat?></font></td>
    <td width="50%"></td>
  </tr>
  <tr>
    <td width="50%"><b>Interrill Erodibility (kg*s/m**4):</b> <font color="#FF0000"><?php echo $interrill?></font></td>
    <td width="50%"><b>Rill Erodibility: </b><font color="#FF0000"><?php echo $rill?></font></td>
  </tr>
  <tr>
    <td width="50%"><b>Critical Shear: </b><font color="#FF0000"><?php echo $shear?></font></td>
    <td width="50%"><b>Effective Hydraulic Conductivity (mm/h): </b><font color="#FF0000"><?php echo $conduct?></font></td>
  </tr>
</table>
<h2><font color="#0000FF">Layer Information</font></h2>
<table border="1" width="80%" bgcolor="#FFFFCC">
  <tr>
    <td width="7%" bgcolor="#C0C0C0"><b><font size="2">Layer</font></b></td>
    <td width="11%" bgcolor="#C0C0C0"><b><font size="2">Depth (mm)</font></b></td>
    <td width="12%" bgcolor="#C0C0C0"><b><font size="2">Sand (%)</font></b></td>
    <td width="9%" bgcolor="#C0C0C0"><b><font size="2">Clay(%)</font></b></td>
    <td width="14%" bgcolor="#C0C0C0"><b><font size="2">Organic Matter(%)</font></b></td>
    <td width="16%" bgcolor="#C0C0C0"><b><font size="2">CEC (meq/100g)</font></b></td>
    <td width="31%" bgcolor="#C0C0C0"><b><font size="2">Rock(%)</font></b></td>
  </tr>
<?php
  for ($row = 0;$row<mysql_num_rows($Result);$row++) {
    $depth = mysql_result($Result,$row,"depth");
    $sand = mysql_result($Result,$row,"sand");
    $clay = mysql_result($Result,$row,"clay");
    $om = mysql_result($Result,$row,"om");
    $cec = mysql_result($Result,$row,"cec");
    $rock = mysql_result($Result,$row,"rock");
    $layer = $row+1;
?>

  <tr>
    <td width="7%">
      <p align="center"><font size="2"><?php echo $layer?></font></td>
    <td width="11%"><font size="2"><?php echo $depth?></font></td>
    <td width="12%"><font size="2"><?php echo $sand?></font></td>
    <td width="9%"><font size="2"><?php echo $clay?></font></td>
    <td width="14%"><font size="2"><?php echo $om?></font></td>
    <td width="16%"><font size="2"><?php echo $cec?></font></td>
    <td width="31%"><font size="2"><?php echo $rock?></font></td>
  </tr>
<?php
  }

  while ($layer < 10) {
     $layer = $layer + 1;
?>
  <tr>
    <td width="7%">
      <p align="center"><font size="2"><?php echo $layer?></font></td>
    <td width="11%">&nbsp;</td>
    <td width="12%">&nbsp;</td>
    <td width="9%">&nbsp;</td>
    <td width="14%">&nbsp;</td>
    <td width="16%">&nbsp;</td>
    <td width="31%">&nbsp;</td>
  </tr>
<?php
   }
   mysql_free_result($Result);
   // DE_EDIT
   mysql_close($Connection);
?>

</table>
<h2><font color="#0000FF">Comments</font></h2>


</body>

</html>
