<?php
session_start();
?>
<!--  WEPPCAT Internet model interface: State Map
  --
  --  February 2007
  --  Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson Arizona
  --
  --
  --  Displays Google map from the state with all climate stations
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>Climate Station Map</title>
<style type="text/css">
  p
  {
  	text-align: center;
  	padding: 5px;
  	font:16px/1 sans-serif;
  	font-weight:bold;
  }
</style>
<?php

$latlon = "";

// Gets attributes from user input
$st = $HTTP_GET_VARS["State"];
// Index State
$theIndex = $HTTP_GET_VARS["IX"];
// Index Climate Station
$cliIndex = $HTTP_GET_VARS["CLiIx"];
// Field Length (ft)
$slpLen = $HTTP_GET_VARS["Len"];
// Field Width (ft)
$slpWid = $HTTP_GET_VARS["Wid"];
// Index Slope Shape
$shpIndex = $HTTP_GET_VARS["ShpIx"];
// Index Steepness
$stpIndex = $HTTP_GET_VARS["StpIx"];
// Index Soil
$slIndex = $HTTP_GET_VARS["SLIx"];
// Index Management
$manIndex = $HTTP_GET_VARS["ManIx"];
// Index Management Assess Change
$manAsIndex = $HTTP_GET_VARS["ManAsIx"];
// Senario Name
$scenName = $HTTP_GET_VARS["scenario"];
// Baseline Condition open
$testBC = $HTTP_GET_VARS["testBC"];
// Assess Change open
$testAC = $HTTP_GET_VARS["testAC"];
// Compare scenarios open
$testAF = $HTTP_GET_VARS["testAF"];
// Start over open
$testSO = $HTTP_GET_VARS["testSO"];
// Test if baseline ran
$testBase = $HTTP_GET_VARS["testBase"];

// Includes funcs.php for
include ('funcs.php');

$latm = 0;
$lonm = 0;
$zoom = 8;

// Set coordinates to state
switch ($st)
{
case "Alabama":
  $latm = 32.85;
  $lonm = -86.85;
  $zoom = 7;
  break;
case "Alaska":
  $latm = 63.30;
  $lonm = -162.00;
  $zoom = 4;
  break;
case "Arizona":
  $latm = 34.25;
  $lonm = -111.60;
  $zoom = 6;
  break;
case "Arkansas":
  $latm = 34.90;
  $lonm = -92.50;
  $zoom = 7;
  break;
case "California":
  $latm = 37.50;
  $lonm = -119.70;
  $zoom = 6;
  break;
case "Colorado":
  $latm = 39.00;
  $lonm = -105.50;
  $zoom = 7;
  break;
case "Connecticut":
  $latm = 41.55;
  $lonm = -72.70;
  $zoom = 8;
  break;
case "Delaware":
  $latm = 39.10;
  $lonm = -75.40;
  $zoom = 8;
  break;
case "Florida":
  $latm = 28.25;
  $lonm = -82.50;
  $zoom = 6;
  break;
case "Georgia":
  $latm = 32.85;
  $lonm = -83.50;
  $zoom = 7;
  break;
case "Hawaii":
  $latm = 20.75;
  $lonm = -156.00;
  $zoom = 7;
  break;
case "Idaho":
  $latm = 45.40;
  $lonm = -114.75;
  $zoom = 6;
  break;
case "Illinois":
  $latm = 40.00;
  $lonm = -89.20;
  $zoom = 6;
  break;
case "Indiana":
  $latm = 39.85;
  $lonm = -86.30;
  $zoom = 7;
  break;
case "Iowa":
  $latm = 42.10;
  $lonm = -93.50;
  $zoom = 7;
  break;
case "Kansas":
  $latm = 38.50;
  $lonm = -98.35;
  $zoom = 7;
  break;
case "Kentucky":
  $latm = 37.65;
  $lonm = -85.30;
  $zoom = 7;
  break;
case "Louisiana":
  $latm = 31.20;
  $lonm = -91.35;
  $zoom = 7;
  break;
case "Maine":
  $latm = 45.40;
  $lonm = -69.25;
  $zoom = 7;
  break;
case "Maryland":
  $latm = 39.00;
  $lonm = -76.85;
  $zoom = 7;
  break;
case "Massachusetts":
  $latm = 42.10;
  $lonm = -71.85;
  $zoom = 8;
  break;
case "Michigan":
  $latm = 44.80;
  $lonm = -85.50;
  $zoom = 6;
  break;
case "Minnesota":
  $latm = 46.30;
  $lonm = -93.85;
  $zoom = 6;
  break;
case "Mississippi":
  $latm = 32.75;
  $lonm = -89.75;
  $zoom = 7;
  break;
case "Missouri":
  $latm = 38.35;
  $lonm = -92.50;
  $zoom = 7;
  break;
case "Montana":
  $latm = 47.00;
  $lonm = -109.50;
  $zoom = 6;
  break;
case "Nebraska":
  $latm = 41.50;
  $lonm = -99.75;
  $zoom = 7;
  break;
case "Nevada":
  $latm = 39.00;
  $lonm = -116.75;
  $zoom = 6;
  break;
case "New Hampshire":
  $latm = 43.85;
  $lonm = -71.60;
  $zoom = 7;
  break;
case "New Jersey":
  $latm = 40.00;
  $lonm = -74.65;
  $zoom = 7;
  break;
case "New Mexico":
  $latm = 34.40;
  $lonm = -106.00;
  $zoom = 6;
  break;
case "New York":
  $latm = 43.00;
  $lonm = -75.55;
  $zoom = 7;
  break;
case "North Carolina":
  $latm = 35.50;
  $lonm = -79.75;
  $zoom = 7;
  break;
case "North Dakota":
  $latm = 47.45;
  $lonm = -100.50;
  $zoom = 7;
  break;
case "Ohio":
  $latm = 40.25;
  $lonm = -82.75;
  $zoom = 7;
  break;
case "Oklahoma":
  $latm = 35.50;
  $lonm = -97.40;
  $zoom = 7;
  break;
case "Oregon":
  $latm = 44.20;
  $lonm = -120.50;
  $zoom = 7;
  break;
case "Pennsylvania":
  $latm = 41.00;
  $lonm = -77.85;
  $zoom = 7;
  break;
case "Rhode Island":
  $latm = 41.65;
  $lonm = -71.50;
  $zoom = 9;
  break;
case "South Carolina":
  $latm = 33.85;
  $lonm = -81.00;
  $zoom = 7;
  break;
case "South Dakota":
  $latm = 44.50;
  $lonm = -100.25;
  $zoom = 7;
  break;
case "Tennessee":
  $latm = 35.85;
  $lonm = -86.25;
  $zoom = 7;
  break;
case "Texas":
  $latm = 31.50;
  $lonm = -99.50;
  $zoom = 6;
  break;
case "Utah":
  $latm = 39.35;
  $lonm = -111.75;
  $zoom = 6;
  break;
case "Alabama":
  $latm = 32.85;
  $lonm = -86.85;
  $zoom = 7;
  break;
case "Vermont":
  $latm = 44.10;
  $lonm = -72.65;
  $zoom = 7;
  break;
case "Virginia":
  $latm = 37.65;
  $lonm = -79.20;
  $zoom = 7;
  break;
case "Washington":
  $latm = 47.35;
  $lonm = -120.50;
  $zoom = 7;
  break;
case "West Virginia":
  $latm = 38.65;
  $lonm = -80.75;
  $zoom = 7;
  break;
case "Wisconsin":
  $latm = 44.80;
  $lonm = -90.00;
  $zoom = 7;
  break;
case "Wyoming":
  $latm = 43.00;
  $lonm = -107.50;
  $zoom = 7;
  break;
default:
  ;
}

// List with all US States
$state = toStateAbbr($st);

// Request latitude and longitude coordinate for climate station
// Connnect to database
if (!($Connection = mysql_connect("127.0.0.1", "weppcat"))) {
	print ("Could not establish connection.<BR>\n");
	exit;
}

// Creats SQL query
$sqlstmt = "SELECT station, id, lat, longitude from climates where state ='" . $state . "' order by station";

// Execute SQL query
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

  	// Runs through all rows from SQL reply and write it to the climate station drop down
	for ($Row = 0; $Row < mysql_num_rows($Result); $Row++) {
		$station = mysql_result($Result, $Row, "station");
		$index = mysql_result($Result, $Row, "id");
    $lati = mysql_result($Result, $Row, "lat");
    $long = mysql_result($Result, $Row, "longitude");
		// If climate ID is < 6
		if (strlen($index) < 6)
		$index = '0' . $index;

    $latlon = $latlon . "point = new GLatLng(" . $lati . "," . $long . ");";
    $latlon = $latlon . "name = \"$station\";";
    $latlon = $latlon . "map.addOverlay(createMarker(point, name));";
    }
	// Free result memory
	mysql_free_result($Result);

?>
</head>

    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA2dQESJ1Fc23geADkKYfSJRRcWEwcopRf_ggayO1txYMblO43uhRVTqjXPIR5NJHdOmQytB2L1PpuRQ"
      type="text/javascript"></script>
    <script type="text/javascript">

    var map;

    function changeStation(name){

	      loc = "input_section.php?ST=" + "<?php echo $st?>" + "&IX=" + <?php echo $theIndex?>;

        loc = loc + "&CL=" + name;
        loc = loc + "&Len=" + <?php echo $slpLen?>;
        loc = loc + "&Wid=" + <?php echo $slpWid?>;
	      loc = loc + "&ShpIx=" + <?php echo $shpIndex?>;
        loc = loc + "&StpIx=" + <?php echo $stpIndex?>;
        loc = loc + "&SLIx=" + <?php echo $slIndex?>;
        loc = loc + "&ManIx=" + <?php echo $manIndex?>;
        loc = loc + "&ManAsIx=" + <?php echo $manAsIndex?>;
	      loc = loc + "&scenario=" + "<?php echo $scenName?>";
        loc = loc + "&testBC=" + "<?php echo $testBC?>";
        loc = loc + "&testAC=" + "<?php echo $testAC?>";
        loc = loc + "&testAF=" + "<?php echo $testAF?>";
        loc = loc + "&testSO=" + "<?php echo $testSO?>";
        loc = loc + "&testBase=" + "<?php echo $testBase?>";

       parent.frames[1].location = loc;
    }

    function load() {
     // Looks if browser is compatible for the map
      if (GBrowserIsCompatible()) {

        // Creates a marker at the given point with the given number label
        function createMarker(point, name) {
          var marker = new GMarker(point);
          GEvent.addListener(marker, "click", function() {changeStation(name);
          });
          GEvent.addListener(marker, "mouseover", function() {marker.openInfoWindowHtml("<b>" + name + "</b>");
          });
          return marker;
        }

      	//Create new map opject
        map = new GMap2(document.getElementById("map"), {mapTypes:[G_HYBRID_MAP]});

        //Add map controls
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());

        //Set standard map type to hybrid
        map.setCenter(new GLatLng(<?php echo $latm?>, <?php echo $lonm?>), <?php echo $zoom?>);
        //map.setMapType( G_HYBRID_TYPE );

        //Create point for climate station
        <?php echo $latlon?>
      }
    }

    </script>

<body onload="load()" onunload="GUnload()">
<!--<p> <?php print($st); ?></p>-->
<div id="map" style="height: 600px"></div>
</body>

</html>
