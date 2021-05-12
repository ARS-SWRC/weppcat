<!--  WEPP Internet model interface: Soil Data
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
<title>WEPP Soil Data</title>
<meta name="Microsoft Theme" content="none, default">
<base target="main">
</head>

<?php
include ('funcs.php');
?>

<body>

<h2><font size="2">WEPP Soil Locations</font></h2>
<form method="Get" action="soil_names.php" target="main">
  <p><font size="2"><b>State</b></font> <select size="1" name="State">
    <?php listStates(); ?>
  </select><input type="submit" value="Go" name="B1"></p>
</form>
<p>&nbsp;</p>

</body>

</html>
