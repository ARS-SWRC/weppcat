<?php
session_start();
?>
<!--  WEPPCAT Internet model interface: Compare Results
  --
  --  January 2007
  --  Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson Arizona
-->

<html><head>
<title>Compare Result</title>
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

if (1<sizeof($_SESSION['sceName'])){

// Set workingDir to current session
$workingDir = "/home/wepp/" . session_id();
// Changes dictionary to runs to save file
chdir($workingDir . "/runs");
// Opens compare result csv file
$handle = fopen("compare.csv", "w");
// Writes in the compare result csv file
fwrite($handle, "Comparison of Assess Change Results\n");
fwrite($handle, "\n");
fwrite($handle, "Scenario:");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['sceName'][$i]);
fwrite($handle, "\n");
fwrite($handle, "State:");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['state'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Climate Station:");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," .  str_replace(',', '/',$_SESSION['station'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Management:");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," .  str_replace(',', '/', $_SESSION['man'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Soil:");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," .  str_replace(',', '/',$_SESSION['soil'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Slope Shape:");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['slSh'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Slope Length (ft):");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['slLe'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Slope Width (ft):");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['slWi'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Filter Strip Width (ft):");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
  	fwrite($handle, "," . $_SESSION['fiWi'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Filter Strip Management:");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . str_replace(',', '/', $_SESSION['fiMa'][$i]));
fwrite($handle, "\n");
fwrite($handle, "Average Annual Precipitation (in/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaPe'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Average Annual Runoff (in/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaRu'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Average Annual Soil Loss (ton/A/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaSo'][$i]);
fwrite($handle, "\n");
fwrite($handle, "Average Annual Sediment Yield (ton/A/yr):");
for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++) 
	fwrite($handle, "," . $_SESSION['aaSe'][$i]);
fwrite($handle, "\n");

// Closes compare result csv file
fclose($handle);

?>

<body>
<p style="text-align: center; font:28px/1 times;">Comparison of Assess Change Results</p>
<div>
 <center>
  <!-- Table with input information -->
  
  <table border="1" bgcolor="#FFFFCC">
    <tr>
      <td style="width: 300px"><p><b>Scenario:</b></td>
         <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)	
  			print("<td><p><b>" . $_SESSION['sceName'][$i] . "</b></p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>State:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>" . $_SESSION['state'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Climate Station:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>" . $_SESSION['station'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Management:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>" . $_SESSION['man'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Soil:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>" . $_SESSION['soil'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Slope Shape:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>" . $_SESSION['slSh'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Slope Length (ft):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>" . $_SESSION['slLe'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Slope Width (ft):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>" . $_SESSION['slWi'][$i] . "</p></td>");
  		?>
    </tr>	
    <tr>
      <td style="width: 300px"><p><b>Filter Strip Width (ft):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			if ($_SESSION['fiWi'][$i] == "")
  				print("<td><p>&nbsp;</p></td>");
  			else
  				print("<td><p>" . $_SESSION['fiWi'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Filter Strip Management:</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			if ($_SESSION['fiMa'][$i] == "")
  				print("<td><p>&nbsp;</p></td>");
  			else
  				print("<td><p>" . $_SESSION['fiMa'][$i] . "</p></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>&nbsp;</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><p>&nbsp;</p></td>");
  		?>
    </tr> 
    <tr>
      <td style="width: 300px"><p><b>Average Annual Precipitation (in/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><center><span><b>" . $_SESSION['aaPe'][$i] . "</b></span></center></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Average Annual Runoff (in/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><center><span><b>" . $_SESSION['aaRu'][$i] . "</b></span></center></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Average Annual Soil Loss (ton/A/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><center><span><b>" . $_SESSION['aaSo'][$i] . "</b></span></center></td>");
  		?>
    </tr>
    <tr>
      <td style="width: 300px"><p><b>Average Annual Sediment Yield (ton/A/yr):</b></td>
        <?php
  		for ($i=0; $i<=sizeof($_SESSION['sceName'])-1; $i++)
  			print("<td><center><span><b>" . $_SESSION['aaSe'][$i] . "</b></span></center></td>");
  		?>
    </tr>
  </table>
  </br>	
  <a href="/wepp/<?php echo session_id()?>/runs/compare.csv">Open as comma separated compare.csv file</a>
  <p>(To download file please right click on the link and choose "Save Target (Link) as..." (File can be open with Excel))</p>
 </center>
</div>

<?php
}
// Error message if user ran less then one senario
else {
  echo "<body>";
  echo "<table width=\"100%\" height=\"100%\"><tr><td style=\"vertical-align:middle\">";
  echo "<h2>You need to run at least two scenarios to compare the results.";
  echo "<br><br>";
  echo "Please run the next scenario.</h2>";
  echo "</td></tr></table>";	
}
?>
</tr>
</body></html>
