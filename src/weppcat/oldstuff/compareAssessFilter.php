<?php
session_start();
?>
<!--  WEPPCAT Internet model interface: Compare Assess Filter Strip
  --
  --  May 2007
  --  Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson Arizona
-->

<html><head>
<title>Compare Assess Filter Strip</title>
</head>

<?php
// Includes funcs and runwepp php
include ('funcs.php');
include ('runwepp.php');
?>

<!-- Style for Result -->
<style type="text/css">

h2 {text-align: center}
div {text-align: center}
p {text-align: center}
span {color: #990000}

</style>

<?php

// Creates compare result csv file

if (1<sizeof($_SESSION['sceNameAF'])){

// Set workingDir to current session
$workingDir = "/home/wepp/" . session_id();
// Changes dictionary to runs to save file
chdir($workingDir . "/runs");
// Opens compare result csv file
$handle = fopen("compareAssFil.csv", "w");
// Writes in the compare result csv file
fwrite($handle, "CComparison of Assess Filter Strip Results\n");
fwrite($handle, "\n");
fwrite($handle, "Scenario:");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['sceNameAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "State:");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['stateAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Climate Station:");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," .  str_replace(',', '/',$_SESSION['stationAF'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Management:");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," .  str_replace(',', '/', $_SESSION['manAF'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Soil:");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," .  str_replace(',', '/',$_SESSION['soilAF'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Slope Shape:");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['slShAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Slope Length (ft):");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['slLeAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Slope Width (ft):");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['slWiAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Average Annual Precipitation (in/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaPeAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Average Annual Runoff (in/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaRuAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Average Annual Soil Loss (ton/A/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaSoAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Average Annual Sediment Yield (ton/A/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaSeAF'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Filter Strip Management:");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
	fwrite($handle, "," . str_replace(',', '/', $_SESSION['fiMaAF'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Filter Strip Width (ft):");
for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++) 
  	fwrite($handle, "," . $_SESSION['fiWiAF'][$i]);
fwrite($handle, "\n");


// Closes compare result csv file
fclose($handle);

?>

<body>
<p style="text-align: center; font:28px/1 times;">Comparison of Assess Filter Strip Results</p>
<div>
 <center>
  <!-- Table with input information -->
  
  <table border="1" bgcolor="#FFFFCC">
    <tr>
      <td style="width: 300px"><p><b>Scenario:</b></td>
         <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)	
  			print("<td><p><b>" . $_SESSION['sceNameAF'][$i] . "</b></p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>State:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['stateAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Climate Station:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['stationAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Management:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['manAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Soil:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['soilAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Slope Shape:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['slShAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Slope Length (ft):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['slLeAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Slope Width (ft):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['slWiAF'][$i] . "</p></td>");
  		?>
    </tr>	
    <tr>
      <td style="width: 300px"><p><b>Average Annual Precipitation (in/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['aaPeAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Average Annual Runoff (in/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['aaRuAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Average Annual Soil Loss (ton/A/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['aaSoAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Average Annual Sediment Yield (ton/A/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			print("<td><p>" . $_SESSION['aaSeAF'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Filter Strip Management:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			if ($_SESSION['fiMaAF'][$i] == "")
  				print("<td><p>&nbsp;</p></td>");
  			else
  				print("<td><p>" . $_SESSION['fiMaAF'][$i] . "</p></td>");
  		?>
    </tr> 
    <tr>
      <td style="width: 300px"><p><b>Filter Strip Width (ft):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceNameAF'])-1; $i++)
  			if ($_SESSION['fiWiAF'][$i] == "")
  				print("<td><p>&nbsp;</p></td>");
  			else
  				print("<td><center><span><b>" . $_SESSION['fiWiAF'][$i] . "</b></span></center></td>");
  		?>
    </tr>
  </table>
  </br>	
  <a href="/wepp/<?php echo session_id()?>/runs/compareAssFil.csv">Open as comma separated compare.csv file</a>
  <p>(To download file please right click on the link and choose "Save Target (Link) as..." (File can be open with Excel))</p>
 </center>
</div>

<?php
}
// Error message if user ran less then one scenario
else {
  echo "<body>";
  echo "<table width=\"100%\" height=\"100%\"><tr><td style=\"vertical-align:middle\">";
  echo "<h2>You need to run at least one \"Assess Filter Strip\" scenario";
  echo "<br><br>";
  echo " to compare the results.";
  echo "<br><br>";
  echo "Please run \"Compute New Filter Strip\".</h2>";
  echo "</td></tr></table>";
}
?>
</tr>
</body></html>
