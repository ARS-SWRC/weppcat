<?php
session_start();
?>
<!--  WEPP Internet model interface: View Slope Graph
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
  --  Customized for WEPPCAT
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
  --
  --
  --  
-->
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Slope Editor</title>
<?php


// Views the slope graph

// Gets attributes from user input
$slopeLen = $HTTP_GET_VARS["Len"];
$slopeWid = $HTTP_GET_VARS["Wid"];
$shape = $HTTP_GET_VARS["Shp"];
$steep = $HTTP_GET_VARS["Stp"];

// Graph dimension
$widthmeter = $slopeWid * 0.3048;
$lengthmeter = $slopeLen * 0.3048;

//------------------------------------------------
// Create the slope file which needs to be viewed
//------------------------------------------------
// Sets paths
$workingDir = "/home/wepp/" . session_id();
$slopeName = "/home/wepp/" . session_id() . "/slp_view.slp";
$timestr = time();
$outfile = $workingDir . "/slp_view_" . $timestr . ".png";
$slopeGraphSh = "/wepp/" . session_id() . "/slp_view_" . $timestr . ".png";

// Write in slop file
$handle = fopen($slopeName, "w");
fwrite($handle, "97.5\n#\n#\n#\n#\n1\n");
fwrite($handle, "180.0 " . $widthmeter . "\n");
switch ($shape) {
	case "Uniform" :
		fwrite($handle, "2 " . $lengthmeter . "\n");
		fwrite($handle, "0.0, " . $steep / 100 . " 1.0, " . $steep / 100 . "\n");
		break;
	case "Convex" :
		fwrite($handle, "2 " . $lengthmeter . "\n");
		fwrite($handle, "0.0, 0.0 1.0, " . ($steep / 100) * 2 . "\n");
		break;
	case "Concave" :
		fwrite($handle, "2 " . $lengthmeter . "\n");
		fwrite($handle, "0.0, " . ($steep / 100) * 2 . " 1.0, 0.0\n");
		break;
	case "S-shaped" :
		fwrite($handle, "3 " . $lengthmeter . "\n");
		fwrite($handle, " 0.0, 0.0 0.5, " . ($steep / 100) * 2 . " 1.0, 0.0\n");
		break;
}

// Closes the slope file
fclose($handle);

// Change the current directory
chdir($workingDir);

// Sets graph arguments
$graphArgs = " \"Slope Profile Shape\" " . "\"Length(ft)\" " . "\"Elevation(ft)\" " . 400 . " " . 300 . " line ff00ff false";
// Sets position of graph program
$cmd = "/home/wepp/wepp/grph";

// running the grph program will create a PNG file of the slope profile
$rc = system($cmd . " " . $workingDir . " " . $slopeName . " " . $outfile . $graphArgs, $retval);
if ($rc === false)
	$msg = "error";
else
	$msg = "ok";
?>
</head>

<body>
<p align="center"><font face="Arial Black" size="5">Slope Preview</font><font size="6"><br>
</font><font size="2"><?php echo $shape?></font></p>
<font size="6">
<p align="center"><img border="0" src="<?php echo $slopeGraphSh?>"></p>
</body>

</html>
