<?php
session_start();
?>

<!--  WEPPCAT Internet model interface: Google map from climate station
  --
  --  February 2007
  --  Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson Arizona
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


// Gets attributes from user input
$id = $HTTP_GET_VARS["Id"];

// Request latitude and longitude coordinate for climate station
// Connnect to database
if (!($Connection = mysql_connect("127.0.0.1", "weppcat"))) {
	print ("Could not establish connection.<BR>\n");
	exit;
}

// Creats the SQL query
$sqlstmt = "SELECT station, lat, longitude from climates where id ='" . $id . "' order by station";

// Execute SQL query
if (!($Result = mysql_db_query("wepp", $sqlstmt, $Connection))) {
	// Displays Error message if execution failed
	print ("Could not execute query: ");
	print ($sqlstmt);
	print (mysql_error($Connection));
	mysql_close($Connection);
	print ("<BR>\n");
	exit;
}

// Get station name and coordinates
$sta = mysql_result($Result, $Row, "station");
$lat = mysql_result($Result, $Row, "lat");
$lon = mysql_result($Result, $Row, "longitude");

// Displays an error if there is no row form SQL reply
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
?>
</head>

    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA2dQESJ1Fc23geADkKYfSJRRcWEwcopRf_ggayO1txYMblO43uhRVTqjXPIR5NJHdOmQytB2L1PpuRQ"
      type="text/javascript"></script>
    <script type="text/javascript">

    //<![CDATA
    var map
    function load() {
      // Looks if browser is compatible for the map
      if (GBrowserIsCompatible()) {

      	//Create new map opject
        map = new GMap2(document.getElementById("map"));

        //Add map controls
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());

        //Set standard map type to hybrid
        map.setCenter(new GLatLng(<?php echo $lat?>, <?php echo $lon?>), 13);
        map.setMapType( G_HYBRID_TYPE );
        var icon = new GIcon();

        //Create point for climate station
        var point = new GLatLng(<?php echo $lat?>, <?php echo $lon?>);
        map.addOverlay(new GMarker(point));
      }
    }



    //]]>
    </script>

<body onload="load()" onunload="GUnload()">
	<p> <?php print($sta); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Latitude: <?php print($lat); ?> Longitude: <?php print($lon); ?>)</p>
	<div id="map" style="height: 500px"></div>
</body>

</html>
