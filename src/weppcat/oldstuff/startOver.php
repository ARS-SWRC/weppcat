<?php
session_start();
?>
<!--  WEPPCAT Internet model interface: Start Over
  --
  --  January 2007
  --  Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson Arizona
-->

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Water Erosion Prediction Project Climate Assessment Tool (WEPPCAT)</title>
</head>
<?php
// Remove all files and folders from the Directory
function removeDir($path) {
   // Add trailing slash to $path if one is not there
   if (substr($path, -1, 1) != "/") {
       $path .= "/";
   }
   // Runs throw all files and folders from the Directory
   foreach (glob($path . "*") as $file) {
       if (is_file($file) === TRUE) {
           // Remove each file in this Directory
           unlink($file);
       }
       else if (is_dir($file) === TRUE) {
           // If this Directory contains a Subdirectory, run this Function on it
           removeDir($file);
       }
   }
   // Remove Directory once Files have been removed (If Exists)
   if (is_dir($path) === TRUE) {
       rmdir($path);
   }
}
// Removes session Dir
if (file_exists("/home/wepp/" . session_id())){
  removeDir("/home/wepp/" . session_id());
}

// Destroies session
session_destroy();
?>

<body onLoad="init();">
<SCRIPT LANGAUGE="JavaScript">

<!-- Initialization of Toolbar -->

function init()
{
  	// Output when OK is clicked
	mapURL = "index.php"
	parent.location = mapURL;
}

</SCRIPT>
</body>

</html>
