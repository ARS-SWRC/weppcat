<!--  WEPP Internet model interface: Climate Locations State select
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
-->
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>WEPP Climate Locations</title>
<base target="_self">
</head>

<?php include('funcs.php'); ?>

<body>

<h4>WEPP Climate Locations</h4>
<form method="Get" action="climate_names.php" target="right">
  
<p>State <select size="1" name="State">
   <?php listStates(); ?>
  </select><input type="submit" value="Go" name="B1"></p>
  <p>&nbsp;</p>
</form>
<p>&nbsp;</p>

</body>

</html>


