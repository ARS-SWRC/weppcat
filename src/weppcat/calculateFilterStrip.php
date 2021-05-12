<?php
session_start();
?>
<!--  WEPP Internet model interface: Calculate Filter Strip
  --
  --  April 2007
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
-->

<html><head>
<title>WEPP Calculate Filter Strip</title>

<!-- Style for Result -->

<style type="text/css">
h2 {text-align: center}
</style>

</head>
<SCRIPT LANGUAGE="JavaScript">

<!-- Opens progress window and close it if progress ends -->

function init()
{
	
}

</SCRIPT>

<body onLoad="init();">

<?php

// Checks if Baseline scenario was run with a filter strip
if ($_SESSION['fiWi'][0] == ""){
	print ("<table width=\"100%\" height=\"100%\"><tr><td style=\"vertical-align:middle\">");
	print ("<h2>You must run a baseline scenario with a filter strip first<br><br> in order to use this function.</h2>");
	print ("</td></tr></table>");
	exit;
}

// Checks if Modified Climate was saved
if (!isset($_SESSION['useModCli'])){
    print ("<table width=\"100%\" height=\"100%\"><tr><td style=\"vertical-align:middle\">");
    print ("<h2>Please save modified Climate in Assess Filter Strip first.<br><br>(Press \"Use these values\" in Modify Climate dialog)</h2>");
	print ("</td></tr></table>");
	exit;
}

// Creates Arrays to save session data for later comparison if arrays not already exist.
if(!isset($_SESSION['sceNameAF'])){
  $_SESSION['sceNameAF']=array();
  $_SESSION['stateAF']=array();
  $_SESSION['stationAF']=array();
  $_SESSION['manAF']=array();
  $_SESSION['soilAF']=array();
  $_SESSION['slShAF']=array();
  $_SESSION['slLeAF']=array();
  $_SESSION['slWiAF']=array();
  $_SESSION['fiMaAF']=array();
  $_SESSION['fiWiAF']=array();
  $_SESSION['aaPeAF']=array();
  $_SESSION['aaRuAF']=array();
  $_SESSION['aaSoAF']=array();
  $_SESSION['aaSeAF']=array();
}

// Gets senario Name
$scenName = $HTTP_GET_VARS["scenario"];

// Returns Error if scession name already exists.
if ($scenName == "Baseline" || in_array($scenName, $_SESSION['sceNameAF'])) {
	echo "<table width=\"100%\" height=\"100%\"><tr><td style=\"vertical-align:middle\">";
	echo "<h2>This senario name already exists.";
	echo "<br><br>";
	echo "Please enter another name.</h2>";
	echo "</td></tr></table>";
	exit;
}


// Run WEPPCAT and show result


// Take over attributes form input form 
$stateLong = $HTTP_GET_VARS["StateList"];
$station = $HTTP_GET_VARS["CLIN"];
$id = $HTTP_GET_VARS["CLI"];
$man = $HTTP_GET_VARS["MAN"];
$soil = $HTTP_GET_VARS["SL"];
$length = $HTTP_GET_VARS["LEN"];
$lengthmeter = $length * 0.3048; // feet to meters
$width = $HTTP_GET_VARS["WID"];
$widthmeter = $width * 0.3048; // feet to meters
$shape = $HTTP_GET_VARS["SHP"];
$steep = $HTTP_GET_VARS["STP"];
$years = 100;
$useBuffer = $HTTP_GET_VARS["USE_BUF"];
$bufWidth = $_SESSION['fiWi'][0];
$bufWidthMeter = $bufWidth * 0.3048;
$bufMan = $_SESSION['fiMa'][0];

// Includes funcs and runwepp php
include ('funcs.php');
include ('runwepp.php');

// Sets state abbreviation
$state = toStateAbbr($stateLong);
// Makes string lowerwase
$state = strtolower($state);

// Set workingDir to current session
$workingDir = "/home/wepp/" . session_id();


// Sets Working Dir for file paths for used programs
setWorkingDir();

// Connnect to database
if (!($Connection = mysql_connect("127.0.0.1", "weppcat"))) {
	print ("Could not establish connection.<BR>\n");
	exit;
}

/*
**------------------------------------------------------------------------
** Step 1: Build the climate file input to wepp
**------------------------------------------------------------------------
*/

// Make string uppercase
$state = strtoupper($state);

// Creates *.PAR file name
$id = $_SERVER["REMOTE_ADDR"];
$id = str_replace('.', '_', $id);
$id = $id . "_" . '.PAR';
// Gets the cligen PAR file into the correct place
$parFile = "/usr/local/apache2/cgi-bin/fswepp/working/" . $id;

$newfile = $workingDir . "/runs/" . $id;
// copy($parFile, $newfile);

// Changes dictionary to runs to run cligen
chdir($workingDir . "/runs");
// Opens cligen input file
$handle = fopen("clinp.txt", "w");
// Writes in cliegen input file
fwrite($handle, "\n$id\nn\n5\n1\n" . $years . "\nwepp.cl\nn\n\n");
// Closes file
fclose($handle);

// Position of cligen program
$cmd = $workingDir . "/runs/cligen";
// Deletes cligen result file if it already exists
if (file_exists("wepp.cl"))
	unlink("wepp.cl");
// Calls cliegen from php
$cli_sys = system($cmd . "< clinp.txt > cliout.txt", $retval);

// Deletes *.PAR file if it exists
if (file_exists($id)) {
	//if (!unlink($id)) {
	//}
}

/*
**---------------------------------------------------------------------------
** Step 2: Build the Soil file input to wepp
**---------------------------------------------------------------------------
*/
// Replaces "(" and ")" with "," in soil 
$soilnamecomma = str_replace('(', ',', $soil);
$soilnamecomma = str_replace(')', ',', $soilnamecomma);
// Splits soil at every "," and write it in an array
$tokens = explode(",", $soilnamecomma);
// Gets soilname from array
$soilname = $tokens[0];
// Gets texture for soil from array
$texture = $tokens[1];

// Creats SQL query (soils)
$sqlstmt = "select * from soils where state='" . $state . "' and name='" . $soilname . "' and texture='" . $texture . "'";
// Executes SQL query
if (!($Result = mysql_db_query("wepp", $sqlstmt, $Connection))) {
	// Displays Error message	
	print ("Could not execute query: ");
	print ($sqlstmt);
	print (mysql_error($Connection));
	mysql_close($Connection);
	print ("<BR>\n");
	exit;
}

// Counts number of rows form SQL reply
$Rows = mysql_num_rows($Result);
if ($Rows == 0) {
	// Displays Error Message
	echo "<center>";
	echo "There are no records.";
	echo $sqlstmt;
	echo "</center>";
	mysql_free_result($Result);
	exit;
}

// Gets result data
$soil_id = mysql_result($Result, 0, "soil_id");
$layers = mysql_result($Result, 0, "layers");
$albedo = mysql_result($Result, 0, "albedo");
$sat = mysql_result($Result, 0, "sat");
$interrill = mysql_result($Result, 0, "interrill");
$rill = mysql_result($Result, 0, "rill");
$shear = mysql_result($Result, 0, "shear");
$conduct = mysql_result($Result, 0, "conduct");

// Frees result memory
mysql_free_result($Result);

// Creats SQL query (layers)
$sqlstmt = "select * from layers where soil_id=" . $soil_id . " order by depth";
// Executes SQL query
if (!($Result = mysql_db_query("wepp", $sqlstmt, $Connection))) {
	// Displays Error message
	print ("Could not execute query: ");
	print ($sqlstmt);
	print (mysql_error($Connection));
	mysql_close($Connection);
	print ("<BR>\n");
	exit;
}

// Counts number of rows form SQL reply
$Rows = mysql_num_rows($Result);
if ($Rows == 0) {
	// Displays Error message
	echo "<center>";
	echo "There are no records.";
	echo $sqlstmt;
	echo "</center>";
	mysql_free_result($Result);
	exit;
}

// Writes in soil input file
$handle = fopen("wepp.sol", "w");
fwrite($handle, "97.5\nComments: web gen\n1 1\n");
fwrite($handle, "'" . $soilname . "' '" . $texture . "' " . $layers . "  " . $albedo . " " . $sat . " " . $interrill . " " . $rill . " " . $shear . " " . $conduct . "\n");
for ($Row = 0; $Row < mysql_num_rows($Result); $Row++) {
	fwrite($handle, "  " . mysql_result($Result, $Row, "depth") .
	"  " . mysql_result($Result, $Row, "sand") .
	"  " . mysql_result($Result, $Row, "clay") .
	"  " . mysql_result($Result, $Row, "om") .
	"  " . mysql_result($Result, $Row, "cec") .
	"  " . mysql_result($Result, $Row, "rock") . "\n");
}
// Closes soil input file input
fclose($handle);
// Frees result memory
mysql_free_result($Result);

//Loop to find best filter strip pracsise

// Remembers number of loops
$i = 0;
// Increase or decrease value for filter strip
$x = $bufWidthMeter;
// Remembers if increase or decrease in the last loop
$inc = 1;

do{

/*
**---------------------------------------------------------------
** Step 3: Build the slope input file for wepp
**---------------------------------------------------------------
*/
// Opens slope input file
$handle = fopen("wepp.slp", "w");
// Writes into slope file
fwrite($handle, "97.5\n#\n#\n#\n#\n1\n");
fwrite($handle, "180.0 " . $widthmeter . "\n");
// Switches between slope shapes
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
	// Closes slope file
	fclose($handle);

/*
**----------------------------------------------------------------
** Step 4: Get the rotation file name setup, this should be easy
**----------------------------------------------------------------
*/
$manFile = "/home/wepp/data/managements/" . $man . ".rot";
if ($useBuffer == "true")
$manFileBuf = "/home/wepp/data/managements/" . $bufMan . ".rot";

/*
**----------------------------------------------------------------
** Step 5: Build the project file that will run the whole thing (Diffrent from results1.php)
**----------------------------------------------------------------
*/
// Opens the wepp projekt file
$handle = fopen("wepp.prj", "w");
// Writes in the wepp projekts file
fwrite($handle, "Version = 98.6\nName = web\n");
fwrite($handle, "Comments {\n}\nUnits = English\nLanduse = 1\n");
fwrite($handle, "Length = " . $lengthmeter . "\n");
$slopeFile = $workingDir . "/runs/wepp.slp";
fwrite($handle, "Profile {\n  File = \"" . $slopeFile . "\"\n}\n");
$cliFile = $workingDir . "/runs/wepp.cl";
fwrite($handle, "Climate {\n  File = \"" . $cliFile . "\"\n}\n");
$soilFile = $workingDir . "/runs/wepp.sol";
fwrite($handle, "Soil {\n  Breaks = 0\n  Soil {\n    Distance = " . $lengthmeter . "\n");
fwrite($handle, "    File = \"" . $soilFile . "\"\n  }\n}\n");
$fieldWidth = $lengthmeter - $bufWidthMeter;

fwrite($handle, "Management {\n  Breaks = 1\n  Man {\n    Distance = " . $fieldWidth . "\n");
fwrite($handle, "    File = \"" . $manFile . "\"\n }\n");
fwrite($handle, "   ManStrip {\n   Distance = " . $bufWidthMeter . "\n");
fwrite($handle, "    File = \"" . $manFileBuf . "\"\n }\n}\n");
fwrite($handle, "RunOptions {\n  Version = 1\n");
fwrite($handle, "  SoilLossOutputType = 1\n  SoilLossOutputFile = AutoName\n");
fwrite($handle, "  PlotFile = AutoName\n  SimulationYears = " . $years . "\n");
fwrite($handle, "  SmallEventByPass = 1\n}\n");
fclose($handle);

/*
**------------------------------------------------------------------
** Step 6: Run the WEPP model
**------------------------------------------------------------------
*/
// Creates path for wepp project file
$projectFile = $workingDir . "/runs/wepp.prj";
// Creates path for wepp results.txt file
$resultFile = $workingDir . "/output/results.txt";
// Deletes results.txt file if it is already there
if (file_exists($resultFile))
	unlink($resultFile);
// Creates input command for WEPP 
$weppcmd = "../../wepp/weppserv " . session_id() . " " . $projectFile . " ../output/results.txt > ../output/msgs.txt";
// Sents comand to Linux command line
system($weppcmd, $rc);
// Displays error if command was not successful otherwise OK
if ($rc === false)
	$msg = "Error in system command";
else
	$msg = "OK";

/*
**------------------------------------------------------------------
** Step 7: Parse the results file to get the wepp outputs
**------------------------------------------------------------------
*/
// Creates result file path
$resultFile = $workingDir . "/output/results.txt";
// Parses result file for output (4 WEEP output parameters)
parseResultFile($resultFile);

/*
**------------------------------------------------------------------
** Step 8: Generate the graph images of profile and soil loss
**------------------------------------------------------------------
*/
// Creating Attributes which are needed for to run the graph program (graph 1)
$graphArgs = " \"Slope Profile Shape\" " . "\"Length(ft)\" " . "\"Elevation(ft)\" " . 300 . " " . 200 . " line ff0000 false";
$cmd = "/home/wepp/wepp/grph";
$timestr = time();
$outfile = $workingDir . "/slp_view_" . $timestr . ".png";
$profileImg = "/wepp/" . session_id() . "/slp_view_" . $timestr . ".png";

// Running the grph program will create a PNG file of the slope profile (graph 1)
$rc = system($cmd . " " . $workingDir . " " . $slopeFile . " " . $outfile . $graphArgs, $retval);

// Creating Attributes which are needed for to run the graph program (graph 2)
$graphArgs = " \"Soil Loss\" " . "\"Length(ft)\" " . "\"Soil Loss(ton/A)\" " . 300 . " " . 200 . " area ff0000 false";
$cmd = "/home/wepp/wepp/grph";
$timestr = time();
$outfile = $workingDir . "/loss_view_" . $timestr . ".png";
$lossImg = "/wepp/" . session_id() . "/loss_view_" . $timestr . ".png";
$lossFile = $workingDir . "/output/plot_0.txt";

// Running the grph program will create a PNG file of the slope profile (graph 2)
$rc = system($cmd . " " . $workingDir . " " . $lossFile . " " . $outfile . $graphArgs, $retval);

// Increase Filter
if($_SESSION['aaSe'][0] < $sedYield){
	if ($x < 0.0) {
	  $x = $x * -1;			// switch directions
	  $inc = 0.5;       // now we're in halving mode
	}
}
// Decrease Filter
else{
	if ($x > 0.0) {
		$x = $x * -1;
	  $inc = 0.5;
  }
}
$x = $x * $inc;

// Increase of decrease filter strip wi and field length
if ($_SESSION['aaSe'][0] != $sedYield){
	if (($bufWidthMeter == $lengthmeter) && ($x > 0.0)) {
		$i = 12;              // don't keep checking if full field isn't enough buffer
  }
	$bufWidthMeter = $bufWidthMeter + $x;
  if ($bufWidthMeter > $lengthmeter) {  // don't let bufWidthMeter exceed field length
	  $bufWidthMeter = $lengthmeter;			// set buffer to full length of field
  }
}

$i++;

	
}while ($_SESSION['aaSe'][0] != $sedYield && $i < 12);

if ($_SESSION['aaSe'][0] != $sedYield){
	print ("<table width=\"100%\" height=\"100%\"><tr><td style=\"vertical-align:middle\">");
	print ("<h2>Could not find a filter strip width that resulted in same sediment yield<br><br>Please try again.</h2>");
	print ("</td></tr></table>");
	exit;
}

// If baseline is not on array
if ($_SESSION['sceNameAF'][0] != "Baseline"){
	// Adds baseline attributs to array
	array_push($_SESSION['sceNameAF'],$_SESSION['sceName'][0]);
	array_push($_SESSION['stateAF'],$_SESSION['state'][0]);
	array_push($_SESSION['stationAF'],$_SESSION['station'][0]);
	array_push($_SESSION['manAF'],$_SESSION['man'][0]);
	array_push($_SESSION['soilAF'],$_SESSION['soil'][0]);
	array_push($_SESSION['slShAF'],$_SESSION['slSh'][0]);
	array_push($_SESSION['slLeAF'],$_SESSION['slLe'][0]);
	array_push($_SESSION['slWiAF'],$_SESSION['slWi'][0]);
	array_push($_SESSION['fiMaAF'],$_SESSION['fiMa'][0]);
	array_push($_SESSION['fiWiAF'],$_SESSION['fiWi'][0]);
	
	// Adds baseline attributs (WEPP run) to array
	array_push($_SESSION['aaPeAF'],$_SESSION['aaPe'][0]);
	array_push($_SESSION['aaRuAF'],$_SESSION['aaRu'][0]);
	array_push($_SESSION['aaSoAF'],$_SESSION['aaSo'][0]);
	array_push($_SESSION['aaSeAF'],$_SESSION['aaSe'][0]);
}

// Adds scenario attributs to array
array_push($_SESSION['sceNameAF'],$scenName);
array_push($_SESSION['stateAF'],$stateLong);
array_push($_SESSION['stationAF'],$station);
array_push($_SESSION['manAF'],$man);
array_push($_SESSION['soilAF'],$soil);
array_push($_SESSION['slShAF'],$shape);
array_push($_SESSION['slLeAF'],$length);
array_push($_SESSION['slWiAF'],$width);
array_push($_SESSION['fiMaAF'],$bufMan);
array_push($_SESSION['fiWiAF'],round($bufWidthMeter/0.3048,2));

// Adds scenario attributs (WEPP run) to array
array_push($_SESSION['aaPeAF'],$precip);
array_push($_SESSION['aaRuAF'],$runoff);
array_push($_SESSION['aaSoAF'],$soilLoss);
array_push($_SESSION['aaSeAF'],$sedYield);

// Writes result on webpage
?>
<p style="text-align: center; font:28px/1 times;">WEPPCAT Simulation Results</p>
<hr>
<div>
  <center>
  <!-- Table with input information -->
  <table style="background-color: #ffffcc; text-align: center;" border="1" width="75%">
    <tr>
      <td style="width: 50%">
        <p><b>Scenario:</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $scenName?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>State:</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $stateLong?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>Climate Station:</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $station?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>Management:</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $man?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>Soil:</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $soil?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <b>Slope Shape</b></td>
      <td style="width: 50%">
        &nbsp;<?php print($shape . "(" . $steep . "%)"); ?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>Slope Length (ft):</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $length?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>Slope Width (ft):</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $width?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>Old Filter Strip Width (ft):</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $bufWidth?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>New Filter Strip Width (ft):</b></td>
      <td style="width: 50%">
        <p style="color: #FF0000; font-weight: bold">&nbsp;<?php echo round($bufWidthMeter/0.3048,2)?></td>
    </tr>
    <tr>
      <td style="width: 50%">
        <p><b>Filter Strip Management:</b></td>
      <td style="width: 50%">
        <p>&nbsp;<?php echo $bufMan?></td>
    </tr>
  </table>
  </center>
</div>
<HR>
<div align="center">
  <center>
  
<!-- Table with output information -->
  
<table style="background-color: #ffffcc; text-align: center;" border="2" width="75%">
  <tr>
    <td width="66%">
      <p style="color:#0000ff;"><b>Average Annual Precipitation (in/yr)</b></p>
    </td>
    <td width="34%">
      <p style="color:#800080;"><b>&nbsp;<?php echo $precip?></b></p>
    </td>
  </tr>
  <tr>
    <td width="66%">
      <p style="color:#0000ff;"><b>Average Annual Runoff (in/yr)</b></p>
    </td>
    <td width="34%">
      <p style="color:#800080;"><b>&nbsp;<?php echo $runoff?></b></p>
    </td>
  </tr>
  <tr>
    <td width="66%">
      <p style="color:#0000ff;"><b>Average Annual Soil Loss (ton/A/yr)</b></p>
    </td>
    <td width="34%">
      <p style="color:#800080;"><b>&nbsp;<?php echo $soilLoss?></b></p>
    </td>
  </tr>
  <tr>
    <td width="66%">
      <p style="color:#0000ff;"><b>Average Annual Sediment Yield (ton/A/yr)</b></p>
    </td>
    <td width="34%">
      <p style="color:#800080;"><b>&nbsp;<?php echo $sedYield?></b></p>
    </td>
  </tr>

</table>
  </center>
</div>

<BR>

<!-- Version and Data information -->

<p align="center">Version <?php echo $ver?>
<?php

$today = date("F j, Y, g:i a");
print ("Run on: " . $today);
?>

<!-- Graph images -->

</p>
<BR>
<img src="<?php echo $profileImg?>">
<img src="<?php echo $lossImg?>">
<?php
// Attributes for downloadable output files
$mainOutput = "/wepp/" . session_id() . "/output/loss_0.txt";
$slopeInput = "/wepp/" . session_id() . "/runs/p0.slp";
$soilInput = "/wepp/" . session_id() . "/runs/p0.sol";
$manInput = "/wepp/" . session_id() . "/runs/p0.man";
$runInput = "/wepp/" . session_id() . "/runs/p0.run";
$cliInput = "/wepp/" . session_id() . "/runs/wepp.cl"; 
$parInput = "/wepp/" . session_id() . "/runs/" . $id;
?>

<!-- Link to download Main WEPP Text output summary -->

<p><a href="<?php echo $mainOutput?>" target="_blank"><font size="4" color="#0000FF">Main WEPP Text
Output Summary</font></a></p>
<hr>

<!-- Links to the input files -->

<p><font size="4">WEPP Input Files used for this run<br>
</font>
<a href="<?php echo $parInput?>" target="_blank">PAR Cligen Input File</a><br>
<a href="<?php echo $cliInput?>" target="_blank">Climate Input File (Cligen Output)</a><br>
<a href="<?php echo $slopeInput?>" target="_blank">Slope Input File</a><br>
<a href="<?php echo $soilInput?>" target="_blank">Soil Input File</a><br>
<a href="<?php echo $manInput?>" target="_blank">Management Input File</a><br>
<a href="<?php echo $runInput?>" target="_blank">Run Input File</a></p>
</body></html>
