<!--  WEPP Internet model interface: Functions to run the WEPP model
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
  --  Customized for WEPPCAT
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
-->
<?php
$precip = 0;
$runoff = 0;
$soilLoss = 0;
$sedYield = 0;

// Parses File (4 WEEP output parameters)
function parseResultFile($filename)
{
   // Attributes from file (filename)	
   global $precip;
   global $runoff;
   global $soilLoss;
   global $sedYield;

   $precip = "?";
   $runoff = "?";
   $soilLoss = "?";
   $sedYield = "?";

   // Reads the file and save values into the attributes
   if (file_exists($filename)) {
      $handle = fopen($filename,"r");
      while ($s = fgets($handle,1024)) {
          if (!strncmp($s,"<PRECIP>",8)) {
	     $precip = substr($s,8);
	     $precip = round($precip,1);
	  }
	  else if (!strncmp($s,"<LOSS>",6)) {
	    $soilLoss = substr($s,6);
	    $soilLoss = round($soilLoss,1);
	  }
	  else if (!strncmp($s,"<SEDYIELD>",10)) {
	     $sedYield = substr($s,10);
	     $sedYield = round($sedYield,1);
	  }
	  else if (!strncmp($s,"<RUNOFF>",8)) {
	     $runoff = substr($s,8);
	     $runoff = round($runoff,1);
	 }
      }
      // Closes file
      fclose($handle);
   }
}

// Creates the climate file
function makeClimateFile($Connection, $workingDir, $state, $station,$years)
{
	$sqlstmt = "SELECT id from climates where state ='" . $state . "' and station ='" . $station . "'";
	if(!($Result = mysql_db_query("wepp", $sqlstmt, $Connection )))
        {
                print("Could not execute query: ");
                print($sqlstmt);
                print(mysql_error($Connection));
                mysql_close($Connection);
                print("<BR>\n");
                exit;
        }

        $Rows = mysql_num_rows($Result);
        if ($Rows == 0) {
                echo "<center>";
                echo "There are no records.";
                echo $sqlstmt;
                echo "</center>";
		mysql_free_result($Result);

                exit;
	}

	$id = mysql_result($Result,0,"id");

	if (strlen($id) < 6)
	   $id = '0' . $id;

	$state = strtoupper($state);
	$id = $state . $id . '.PAR';

	// get the cligen PAR file into the correct place
	$parFile = "/home/wepp/data/climates/cligen/" . $id;
	$newfile = $workingDir . "/runs/" . $id;
	//print("copy: '" . $parFile . "' to '" . $newfile ."'\n");
	copy($parFile,$newfile);

	chdir($workingDir . "/runs");
	$handle = fopen("clinp.txt","w");
	fwrite($handle,"\n$id\nn\n5\n1\n" . $years . "\nwepp.cl\nn\n\n");
	fclose($handle);

	$cmd = $workingDir . "/runs/cligen";
	if (file_exists("wepp.cl"))
	   unlink("wepp.cl");
	   
	$retval = "?";

	$cli_sys = system($cmd . "< clinp.txt > cliout.txt",$retval);

	if (file_exists($id)) {
	  if (!unlink($id)) {

	   }
	}

	mysql_free_result($Result);
}

// Creates the soil file
function makeSoilFile($Connection, $workingDir, $state, $soil, $outName)
{
	$theFile = $workingDir . "/runs/" . $outName;

	$state = strtoupper($state);
	$soilnamecomma = str_replace('(',',',$soil);
	$soilnamecomma = str_replace(')',',',$soilnamecomma);
	$tokens = explode(",",$soilnamecomma);
	$soilname = $tokens[0];
	$texture = $tokens[1];

	$sqlstmt = "select * from soils where state='" . $state . "' and name='" . $soilname . "' and texture='" . $texture ."'";
	if(!($Result = mysql_db_query("wepp", $sqlstmt, $Connection )))
        {
                print("Could not execute query: ");
                print($sqlstmt);
                print(mysql_error($Connection));
                mysql_close($Connection);
                print("<BR>\n");
                exit;
        }

        $Rows = mysql_num_rows($Result);
        if ($Rows == 0) {
                echo "<center>";
                echo "There are no records.";
                echo $sqlstmt;
                echo "</center>";
		mysql_free_result($Result);

                exit;
	}

	$soil_id = mysql_result($Result,0,"soil_id");
	$layers = mysql_result($Result,0,"layers");
	$albedo = mysql_result($Result,0,"albedo");
	$sat = mysql_result($Result,0,"sat");
	$interrill = mysql_result($Result,0,"interrill");
	$rill = mysql_result($Result,0,"rill");
	$shear = mysql_result($Result,0,"shear");
	$conduct = mysql_result($Result,0,"conduct");

	mysql_free_result($Result);

	$sqlstmt = "select * from layers where soil_id=" . $soil_id . " order by depth";
	if(!($Result = mysql_db_query("wepp", $sqlstmt, $Connection )))
        {
                print("Could not execute query: ");
                print($sqlstmt);
                print(mysql_error($Connection));
                mysql_close($Connection);
                print("<BR>\n");
                exit;
        }

        $Rows = mysql_num_rows($Result);
        if ($Rows == 0) {
                echo "<center>";
                echo "There are no records.";
                echo $sqlstmt;
                echo "</center>";
		mysql_free_result($Result);

                exit;
	}

	$handle = fopen($theFile,"w");
	fwrite($handle,"97.5\nComments: web gen\n1 1\n");
	fwrite($handle,"'" . $soilname . "' '". $texture . "' " . $layers . "  " . $albedo . " " . $sat . " " . $interrill . " " . $rill . " " . $shear . " " . $conduct . "\n");
	for ($Row=0; $Row < mysql_num_rows($Result); $Row++) {
	   fwrite($handle,"  " . mysql_result($Result,$Row,"depth") .
			  "  " . mysql_result($Result,$Row,"sand") .
			  "  " . mysql_result($Result,$Row,"clay") .
			  "  " . mysql_result($Result,$Row,"om") .
			  "  " . mysql_result($Result,$Row,"cec") .
			  "  " . mysql_result($Result,$Row,"rock") . "\n");
	}

	fclose($handle);

	mysql_free_result($Result);
}

// Creates the slope file
function makeSlopeFile($workingDir,$len,$wid,$shape,$steep,$outFile)
{
	$theFile = $workingDir . "/runs/" . $outFile;;

	$lengthmeter = $len * 0.3048;  // feet to meters
	$widthmeter = $wid * 0.3048;  // feet to meters

	$handle = fopen($theFile,"w");
	fwrite($handle,"97.5\n#\n#\n#\n#\n1\n");
	fwrite($handle,"180.0 " . $widthmeter . "\n");
	switch($shape) {
	   case "Uniform":
		fwrite($handle,"2 " . $lengthmeter . "\n");
		fwrite($handle,"0.0, " . $steep/100 . " 1.0, " . $steep/100 . "\n");
		break;
	   case "Convex":
		fwrite($handle,"2 " . $lengthmeter . "\n");
		fwrite($handle,"0.0, 0.0 1.0, " . ($steep/100)*2 . "\n");
		break;
	   case "Concave":
		fwrite($handle,"2 " . $lengthmeter . "\n");
		fwrite($handle,"0.0, " .  ($steep/100)*2 . " 1.0, 0.0\n");
		break;
	   case "S-shaped":
		fwrite($handle,"3 " . $lengthmeter . "\n");
		fwrite($handle," 0.0, 0.0 0.5, " . ($steep/100)*2 . " 1.0, 0.0\n");
		break;
	}

	fclose($handle);
}
