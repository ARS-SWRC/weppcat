<?php
session_start();
?>
<!--  WEPP Internet model interface: Load WEPPCAT Simulation Result
  --
  --  April 2007
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
-->

<html>
<head>
<title>WEPP Load Calculate Filter Strip</title>
 <style type="text/css">
	h2 {text-align: center}
 </style>
<?php

// Take over attributes form input form 
$stateLong = $HTTP_GET_VARS["StateList"];
$station = $HTTP_GET_VARS["CLIN"];
$id = $HTTP_GET_VARS["CLI"];
$useModiClim = $HTTP_GET_VARS["MC"];
$man = $HTTP_GET_VARS["MAN"];
$soil = $HTTP_GET_VARS["SL"];
$length = $HTTP_GET_VARS["LEN"];
$width = $HTTP_GET_VARS["WID"];
$shape = $HTTP_GET_VARS["SHP"];
$steep = $HTTP_GET_VARS["STP"];
$useBuffer = $HTTP_GET_VARS["USE_BUF"];
$bufWidth = $HTTP_GET_VARS["BUF_WIDTH"];
$bufMan = $HTTP_GET_VARS["BUF_MAN"];
$scenName = $HTTP_GET_VARS["scenario"];

?>
</head>


<body onLoad="init();">
<script type="text/javascript">

<!-- Opens progress window and close it if progress ends -->

function init(){
	
	resultURL = "results.php?";
	resultURL = resultURL + "StateList=<?php echo $stateLong?>";			
	resultURL = resultURL + "&LEN=<?php echo $length?>";
	resultURL = resultURL + "&WID=<?php echo $width?>";
	resultURL = resultURL + "&SHP=<?php echo $shape?>";
	resultURL = resultURL + "&STP=<?php echo $steep?>";
	resultURL = resultURL + "&SL=<?php echo $soil?>";
	resultURL = resultURL + "&MAN=<?php echo $man?>";
	resultURL = resultURL + "&CLI=<?php echo $id?>";
	resultURL = resultURL + "&CLIN=<?php echo $station?>";
	resultURL = resultURL + "&MC=<?php echo $useModiClim?>";
	resultURL = resultURL + "&USE_BUF=<?php echo $useBuffer?>";
	resultURL = resultURL + "&BUF_WIDTH=<?php echo $bufWidth?>";
	resultURL = resultURL + "&BUF_MAN=<?php echo $bufMan?>";
	resultURL = resultURL + "&scenario=<?php echo $scenName?>";
	
	parent.frames[2].location = resultURL;
}

</script>
<table width="100%" height="100%">
	<tr>
		<td style="vertical-align:middle">
			<h2>Please be patient, the<br><br>
			WEPPCAT simulation may take awhile.<br><br>
			Running...<br><br><h2>
			<img src="images/loading.gif" width="32" height="32">
		</td>
	</tr>
</table>

</body></html>
