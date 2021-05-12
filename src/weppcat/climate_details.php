<!--  WEPP Internet model interface: Climate Parameters
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
-->

<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Climate parameters</title>
</head>

<body>

<?php
	$months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	$p = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	$tmax = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	$tmin = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	$nwd = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	$pww = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
    $pwd = array(0,0,0,0,0,0,0,0,0,0,0,0,0);

	$elev = 0;
	$lat = 0;
	$longt = 0;
        $name = "";

        $id = $HTTP_GET_VARS["ID"];

	
	$filename = strtoupper($id);

	$fullname = "/home/wepp/data/climates/cligen/" . $filename . ".PAR";

	$handle = fopen($fullname,"r");

	if ($handle != false) {
	   $lineNo=0;
	   while (!feof($handle) && ($lineNo <= 10)) {
		$lineNo++;
		$buffer = fgets($handle,1024);
		if ($lineNo == 1) {
		   $buffer = substr($buffer,0,40);
		   trim($buffer);
		   $name = $buffer; 
		}
		else if ($lineNo == 2) {
		    $buffer = trim($buffer);
		    sscanf($buffer,"LATT= %f  LONG= %f",&$lat, &$longt);
		} else if ($lineNo == 3) {
		   $buffer = trim($buffer);
		   sscanf($buffer,"ELEVATION = %f",&$elev);
	           //echo(">>".$buffer . "<<");
                } else if ($lineNo == 4) {
                   $buffer = trim($buffer);
		   sscanf($buffer,"MEAN P %f %f %f %f %f %f %f %f %f %f %f %f",&$p[0],&$p[1],&$p[2],&$p[3],&$p[4],&$p[5],
                          &$p[6],&$p[7],&$p[8],&$p[9],&$p[10],&$p[11]);
		} else if ($lineNo == 7) {
		   $buffer = trim($buffer);
		   sscanf($buffer,"P(W/W) %f %f %f %f %f %f %f %f %f %f %f %f",&$pww[0],&$pww[1],&$pww[2],&$pww[3],&$pww[4],&$pww[5],
                          &$pww[6],&$pww[7],&$pww[8],&$pww[9],&$pww[10],&$pww[11]);
   
		} else if ($lineNo == 8) {
		   $buffer = trim($buffer);
                   sscanf($buffer,"P(W/D) %f %f %f %f %f %f %f %f %f %f %f %f",&$pwd[0],&$pwd[1],&$pwd[2],&$pwd[3],&$pwd[4],&$pwd[5],
                          &$pwd[6],&$pwd[7],&$pwd[8],&$pwd[9],&$pwd[10],&$pwd[11]);

		} else if ($lineNo == 9) {
		   $buffer = trim($buffer);
                   sscanf($buffer,"TMAX AV %f %f %f %f %f %f %f %f %f %f %f %f",&$tmax[0],&$tmax[1],&$tmax[2],&$tmax[3],&$tmax[4],&$tmax[5],
                          &$tmax[6],&$tmax[7],&$tmax[8],&$tmax[9],&$tmax[10],&$tmax[11]);
         
                } else if ($lineNo == 10) {
		   $buffer = trim($buffer);
                   sscanf($buffer,"TMIN AV %f %f %f %f %f %f %f %f %f %f %f %f",&$tmin[0],&$tmin[1],&$tmin[2],&$tmin[3],&$tmin[4],&$tmin[5],
                          &$tmin[6],&$tmin[7],&$tmin[8],&$tmin[9],&$tmin[10],&$tmin[11]);

	        }
	   }
	   fclose($handle);

	  for ($i=0;$i<12;$i++) {
	    $m = $i+1;
            if ($m==2)
              $mdays=28;
            else if (($m==4)||($m==6)||($m==9)||($m==11))
                   $mdays=30;
                 else
                   $mdays=31;

	    $nwd[$i] =  (int) ($mdays*($pwd[$i]/(1.0-$pww[$i]+$pwd[$i])));
            $nwd[12] += $nwd[$i];
	    $tmax[12] += $tmax[$i];
            $tmin[12] += $tmin[$i];
            $p[$i] = $p[$i]*$nwd[$i];
	    $p[12]+=$p[$i];
	  }
          if ($tmax[12] != 0)
             $tmax[12] = $tmax[12]/12;
	  if ($tmin[12] != 0)
             $tmin[12] = $tmin[12]/12;
	} else
           echo "Could not open " . $filename;
	
?>

<h2 align="center">Climate Parameters for <?php echo $name?> (<?php echo $id?>)</h2>

<!-- Fill in lat, long, elev  from ATL Object -->
<p align="center"><b><?php echo $lat?> N&nbsp; <?php echo $longt?> W<br>
<?php echo $elev?> Feet Elevation</b></p>

<div align="center">
  <center>
  <table border="1" width="80%" bgcolor="#FFFFCC">
    <tr>
      <td width="20%" bgcolor="#008080" align="center" height="50"><b><font color="#008080">Month</font><font color="#FFFFFF">Month</font></b></td>
      <td width="20%" bgcolor="#008080" height="50" align="center"><font color="#FFFFFF"><b>Mean
        Maximum Temperature (F)</b></font></td>
      <td width="20%" bgcolor="#008080" height="50" align="center"><b><font color="#FFFFFF">Mean
        Minimum Temperature (F)</font></b></td>
      <td width="20%" bgcolor="#008080" height="50" align="center"><font color="#FFFFFF"><b>Mean
        Precipitation (in)</b></font></td>
      <td width="20%" bgcolor="#008080" height="50" align="center"><font color="#FFFFFF"><b>Number
        of Wet Days</b></font></td>
    </tr>
<?php
    for ($i=0;$i<12;$i++) {
?>
    <tr>
      <td width="20%" bgcolor="#008080" align="center"><font color="#FFFFFF"><b><?php echo $months[$i]?></b></font></td>
      <td width="20%" align="center">&nbsp<b><?php echo $tmax[$i]?></b></td>
      <td width="20%" align="center">&nbsp<b><?php echo $tmin[$i]?></b></td>
      <td width="20%" align="center">&nbsp<b><?php echo $p[$i]?></b></td>
      <td width="20%" align="center">&nbsp<b><?php echo $nwd[$i]?></b></td>
    </tr>
<?php
    }
?>
    <tr>
      <td width="20%" bgcolor="#008080" align="center"><b><font color="#FFFFFF">Annual</font></b></td>
      <td width="20%" align="center">&nbsp<b></b></td>
      <td width="20%" align="center">&nbsp<b></b></td>
      <td width="20%" align="center">&nbsp<b><?php echo $p[12]?></b></td>
      <td width="20%" align="center">&nbsp<b><?php echo $nwd[12]?></b></td>
    </tr>
  </table>
  </center>
</div>

</body>

</html>
