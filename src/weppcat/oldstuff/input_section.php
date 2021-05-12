<?php
session_start();
?>
<!--  WEPP Internet model interface: Input section
  --
  --  January 2004
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
  --  Customized for WEPPCAT
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
  --
  --
  --  Input section for interaction with the user.
  --  Seperated in 4 sections
  --  - Baseline Conditon
  --  - Assess Change
  --  - Assess Filter Strip
  --  - Start Over
-->

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>WEPPCAT Input</title>

<style type="text/css">

  body
  {
  	background-color: #FFE066;
  }
  span
  {
	  font:14px/1 sans-serif;
	  font-weight:bold;
  }
  fieldset.chapter
  {
	  padding: 5px;
	  font:12px/1 sans-serif;
	  border:2px solid black;
	  font-weight:bold;
	  width:344px;
  }
  fieldset 
  {
	  padding: 5px;
	  font:12px/1 sans-serif;
	  border:1px solid black;
	  font-weight:bold;
	  width:330px;
  }
  label 
  {
	  float:left;
	  width:104px;
	  margin-right:5px;
	  padding-top:2px;
	  text-align:right;
	  font-weight:bold;
  }
  label.long 
  {
	  float:left;
	  width:300px;
	  margin-right:5px;
	  padding-top:2px;
	  text-align:left;
	  font-weight:bold;
  }
  legend 
  {
	  padding: 2px 5px;
	  color:black;
	  text-align:right;
	  font-weight:bold;
  }
  a.header:link {color:black; text-decoration:none;} 
  a.header:visited {color:black; text-decoration:none;} 
  a.header:hover {color:black; text-decoration:none;} 
  a.header:active {color:black; text-decoration:none;}

</style>

<?php

// Attributs for Toolbar
// Name State
$stateLong = $HTTP_GET_VARS["ST"];
if (!isset ($stateLong))
	$stateLong = "Alabama";
// Index State
$theIndex = $HTTP_GET_VARS["IX"];
if (!isset ($theIndex))
	$theIndex = 0;
// Name Climate Station
$cli = $HTTP_GET_VARS["CL"];
if (!isset ($cli))
	$cli = "";
// Index Climate Station
$cliIndex = $HTTP_GET_VARS["CLiIx"];
if (!isset ($cliIndex))
	$cliIndex = 0;
// Field Length (ft)
$slpLen = $HTTP_GET_VARS["Len"];
if (!isset ($slpLen))
	$slpLen = 100;
// Field Width (ft)
$slpWid = $HTTP_GET_VARS["Wid"];
if (!isset ($slpWid))
	$slpWid = 100;
// Index Slope Shape
$shpIndex = $HTTP_GET_VARS["ShpIx"];
if (!isset ($shpIndex))
	$shpIndex = 0;
// Index Steepness
$stpIndex = $HTTP_GET_VARS["StpIx"];
if (!isset ($stpIndex))
	$stpIndex = 5;
// Index Soil
$slIndex = $HTTP_GET_VARS["SLIx"];
if (!isset ($slIndex))
	$slIndex = 0;
// Index Management
$manIndex = $HTTP_GET_VARS["ManIx"];
if (!isset ($manIndex))
	$manIndex = 0;
// Index Management Assess Change
$manAsIndex = $HTTP_GET_VARS["ManAsIx"];
if (!isset ($manAsIndex))
	$manAsIndex = 0;
// Senario Name
$scenName = $HTTP_GET_VARS["scenario"];
if (!isset ($scenName))
	$scenName = "";
// Baseline Condition open
$testBC = $HTTP_GET_VARS["testBC"];
if (!isset ($testBC))
	$testBC = "none";
// Assess Change open
$testAC = $HTTP_GET_VARS["testAC"];
if (!isset ($testAC))
	$testAC = "none";
// Compare scenarios open
$testAF = $HTTP_GET_VARS["testAF"];
if (!isset ($testAF))
	$testAF = "none";
// Start over open
$testSO = $HTTP_GET_VARS["testSO"];
if (!isset ($testSO))
	$testSO = "none";
// Test if baseline ran
$testBase = $HTTP_GET_VARS["testBase"];
if (!isset ($testBase))
	$testBase = "false";

// Includes funcs.php for 
include ('funcs.php');

// Shortcut currend State
$state = toStateAbbr($stateLong);

// Sets file paths for used programs
makeWorkingDir();
$workingdir = "/home/wepp/" . session_id();
if (!file_exists($workingdir . "/runs")) {
	mkdir($workingdir . "/runs");
}
$workingdir = $workingdir . "/runs/";
?>

</head>

<body onLoad="init();">

<script language="javascript" src="js/PopupWindow.js"></script>
<script type="text/javascript">

<!-- Initialization of Toolbar -->

function init()
{
	//Gets Posted PHP values
	document.weppin.StateList.selectedIndex = <?php echo $theIndex ?>;
	document.weppin.CLI.selectedIndex = <?php echo $cliIndex?>;
	if ("<?php echo $cli?>" != ""){
	    for(i = 0; document.weppin.CLI.length > i; i++){
	    	if(document.weppin.CLI.options[i].text == "<?php echo $cli?>")
	    	   document.weppin.CLI.selectedIndex = i;
	    }
	}
	document.weppin.LEN.value = <?php echo $slpLen?>;
	document.weppin.WID.value = <?php echo $slpWid?>;
	document.weppin.SHP.selectedIndex = <?php echo $shpIndex?>;
	document.weppin.STP.selectedIndex = <?php echo $stpIndex?>;
	document.weppin.SL.selectedIndex = <?php echo $slIndex?>;
	document.weppin.MAN.selectedIndex = <?php echo $manIndex?>;
	document.weppin.MANAS.selectedIndex = <?php echo $manAsIndex?>;
	document.weppin.scenario.value = "<?php echo $scenName?>";
	document.weppin.testBase.value = "<?php echo $testBase?>";
	
	// Update Sections (Open/Close)
	if("<?php echo $testBC?>" == "block"){
  	document.weppin.testBC.style.display = "block";		
  	document.weppin.imgBC.src="images/arrowDown.gif";
  	}
  	if("<?php echo $testAC?>" == "block"){
  	document.weppin.testAC.style.display = "block";		
  	document.weppin.imgAC.src="images/arrowDown.gif";
  	}
  	if("<?php echo $testAF?>" == "block"){
  	document.weppin.testAF.style.display = "block";		
  	document.weppin.imgAF.src="images/arrowDown.gif";
  	}
	if("<?php echo $testSO?>" == "block"){
	document.weppin.testSO.style.display = "block";		
  	document.weppin.imgSO.src="images/arrowDown.gif";	
	}
	<?php
	if(isset($_SESSION['ranBaseline'])){
	?>
		// Disable all Baseline inputs after run the Baseline scenario
		document.weppin.StateList.disabled=true;
		document.weppin.CLI.disabled=true;
		document.weppin.SL.disabled=true;
		document.weppin.map.disabled=true;	
		document.weppin.stateMap.disabled=true;
		document.weppin.LEN.disabled=true;	
		document.weppin.WID.disabled=true;
		document.weppin.SHP.disabled=true;	
		document.weppin.slpVw.disabled=true;	
		document.weppin.STP.disabled=true;	
		document.weppin.MAN.disabled=true;
		document.weppin.B1.disabled=true;
		document.weppin.usblf.disabled=true;
		document.weppin.BLBUF_WIDTH.disabled=true;
		document.weppin.BLBUF_MAN.disabled=true;
		
		// Do not show Baseline section
		document.weppin.testBC.style.display = "none";
		document.weppin.imgBC.src="images/arrowUp.gif";

	<?php
	}
	?>
	
}


<!-- Create PopupWindow objects for help --> 

var helppopup1 = new PopupWindow("helpdiv1");
helppopup1.offsetY = 25;
helppopup1.autoHide();

<!-- Function to display the help text --> 

function popup1(anchorname, text) {
	helppopup1.populate(text);
	helppopup1.showPopup(anchorname);
}

<!-- Shows Standard Slope Shapes --> 

function showSlope()
{
	parent.frames[2].location = "slopes.htm";
}

<!-- Shows Managment Schedule --> 

function showMan()
{
	index = document.weppin.MAN.selectedIndex;
	newman = document.weppin.MAN.options[index].text;
	parent.frames[2].location = "man_details.php?NAME=" + escape(newman);
}

<!-- Changes other attributes if new state is selected --> 

function newState()
{
	index = document.weppin.StateList.selectedIndex;
	newst = document.weppin.StateList.options[index].text;
	
	loc = "input_section.php?ST=" + escape(newst) + "&IX=" + index;
	loc = loc + "&Len=" + document.weppin.LEN.value;
	loc = loc + "&Wid=" + document.weppin.WID.value;
	loc = loc + "&ShpIx=" + document.weppin.SHP.selectedIndex;
	loc = loc + "&StpIx=" + document.weppin.STP.selectedIndex;
	loc = loc + "&ManIx=" + document.weppin.MAN.selectedIndex;
	loc = loc + "&ManAsIx=" + document.weppin.MANAS.selectedIndex;
	loc = loc + "&scenario=" + document.weppin.scenario.value;
	loc = loc + "&testBC=" + document.weppin.testBC.style.display;
	loc = loc + "&testAC=" + document.weppin.testAC.style.display;
	loc = loc + "&testAF=" + document.weppin.testAF.style.display;
	loc = loc + "&testSO=" + document.weppin.testSO.style.display;
	loc = loc + "&testBase=" + document.weppin.testBase.value;
	
	parent.frames[1].location = loc;
}


<!-- Start start to run Cligen and WEPP for Baseline Scenario -->

function startItBC()
{

		document.weppin.testBase.value = "true";
		
		// Transvers baseline management to assess change management
		document.weppin.MANAS.selectedIndex = document.weppin.MAN.selectedIndex;
		
		index = document.weppin.CLI.selectedIndex;
		document.weppin.CLIN.value = document.weppin.CLI.options[index].text;
		document.weppin.MC.value = document.weppin.MCCB.checked;
		
		// Use to open site in right frame (because of Google Maps problem by submitting from)
		indexState = document.weppin.StateList.selectedIndex;
		indexSL = document.weppin.SL.selectedIndex;
		indexMAN = document.weppin.MAN.selectedIndex;
		indexSHP = document.weppin.SHP.selectedIndex;
		indexCLI = document.weppin.CLI.selectedIndex;
		indexBUF_MAN = document.weppin.BLBUF_MAN.selectedIndex;
		tForm = document.getElementById("weppin");
		    
		resultURL = "loadResults.php?";
		resultURL = resultURL + "StateList=" + document.weppin.StateList.options[indexState].text;
		resultURL = resultURL + "&LEN=" + document.weppin.LEN.value;
		resultURL = resultURL + "&WID=" + document.weppin.WID.value;
		resultURL = resultURL + "&SHP=" + document.weppin.SHP.options[indexSHP].text;
		resultURL = resultURL + "&STP=" + document.weppin.STP.value;
		resultURL = resultURL + "&SL=" + document.weppin.SL.options[indexSL].text;
		resultURL = resultURL + "&MAN=" + document.weppin.MAN.options[indexMAN].text;
		resultURL = resultURL + "&CLI=" + document.weppin.CLI.options[indexCLI].value;
		resultURL = resultURL + "&CLIN=" + document.weppin.CLI.options[indexCLI].text;
		resultURL = resultURL + "&MC=false";
		resultURL = resultURL + "&USE_BUF=" + document.weppin.BLUSE_BUF.value;
		resultURL = resultURL + "&BUF_WIDTH=" + document.weppin.BLBUF_WIDTH.value;
		resultURL = resultURL + "&BUF_MAN=" + document.weppin.BLBUF_MAN.options[indexBUF_MAN].text;
		resultURL = resultURL + "&scenario=Baseline";
	
		parent.frames[2].location = resultURL;	
		
		// Disable all Baseline inputs after run the Baseline scenario
		document.weppin.StateList.disabled=true;
		document.weppin.CLI.disabled=true;
		document.weppin.SL.disabled=true;
		document.weppin.map.disabled=true;	
		document.weppin.stateMap.disabled=true;
		document.weppin.LEN.disabled=true;	
		document.weppin.WID.disabled=true;
		document.weppin.SHP.disabled=true;	
		document.weppin.slpVw.disabled=true;	
		document.weppin.STP.disabled=true;	
		document.weppin.MAN.disabled=true;
		document.weppin.B1.disabled=true;
		document.weppin.usblf.disabled=true;
		document.weppin.BLBUF_WIDTH.disabled=true;
		document.weppin.BLBUF_MAN.disabled=true;
		// Do not show Baseline section anymore
		document.weppin.testBC.style.display = "none";
		document.weppin.imgBC.src="images/arrowUp.gif";

}

<!-- Start the run Cligen and WEPP for Assess Scenario -->

function startIt()
{
	<?php
	if(!isset($_SESSION['ranBaseline'])){
	?>
	//Test if baseline scenario ran
	if(document.weppin.testBase.value == "true"){
	<?php
	}
	?>	
		index = document.weppin.CLI.selectedIndex;
		document.weppin.CLIN.value = document.weppin.CLI.options[index].text;
		document.weppin.MC.value = document.weppin.MCCB.checked;
		
		//Looks if scenario name is set
		if(validate_form(document.weppin)==true){
			
			// Use to open site in right frame (because of Google Maps problem by submitting from)
			indexState = document.weppin.StateList.selectedIndex;
			indexSL = document.weppin.SL.selectedIndex;
			indexMANAS = document.weppin.MANAS.selectedIndex;
			indexSHP = document.weppin.SHP.selectedIndex;
			indexCLI = document.weppin.CLI.selectedIndex;
			indexBUF_MAN = document.weppin.BUF_MAN.selectedIndex;
			tForm = document.getElementById("weppin");
	
			resultURL = "loadResults.php?";
			resultURL = resultURL + "StateList=" + document.weppin.StateList.options[indexState].text;
			resultURL = resultURL + "&LEN=" + document.weppin.LEN.value;
			resultURL = resultURL + "&WID=" + document.weppin.WID.value;
			resultURL = resultURL + "&SHP=" + document.weppin.SHP.options[indexSHP].text;
			resultURL = resultURL + "&STP=" + document.weppin.STP.value;
			resultURL = resultURL + "&SL=" + document.weppin.SL.options[indexSL].text;
			resultURL = resultURL + "&MAN=" + document.weppin.MANAS.options[indexMANAS].text;
			resultURL = resultURL + "&CLI=" + document.weppin.CLI.options[indexCLI].value;
			resultURL = resultURL + "&CLIN=" + document.weppin.CLI.options[indexCLI].text;
			resultURL = resultURL + "&MC=" + document.weppin.MC.value;
			resultURL = resultURL + "&USE_BUF=" + document.weppin.USE_BUF.value;
			resultURL = resultURL + "&BUF_WIDTH=" + document.weppin.BUF_WIDTH.value;
			resultURL = resultURL + "&BUF_MAN=" + document.weppin.BUF_MAN.options[indexBUF_MAN].text;
			resultURL = resultURL + "&scenario=" + document.weppin.scenario.value;
	
			parent.frames[2].location = resultURL;
		
		}
	<?php
	if(!isset($_SESSION['ranBaseline'])){
	?>
	}else
	alert("Please run Baseline Conditions first");
	<?php
	}
	?>	
}

<!-- Creates the Slope Preview --> 

function showSlpGraph()
{
	shpIndex = document.weppin.SHP.selectedIndex;
	stpIndex = document.weppin.STP.selectedIndex;
	slpURL = "slope_graph.php?"
	slpURL = slpURL + "Len=" + document.weppin.LEN.value; // in feet
	slpURL = slpURL + "&Wid=" + document.weppin.WID.value; // in feet
	slpURL = slpURL + "&Shp=" + document.weppin.SHP.options[shpIndex].text;
	slpURL = slpURL + "&Stp=" + document.weppin.STP.options[stpIndex].value;

	parent.frames[2].location = slpURL;
}

<!-- Compute new filter strip width -->

function compFilter()
{	
	<?php
	if(!isset($_SESSION['ranBaseline'])){
	?>
	//Test if baseline scenario ran
	if(document.weppin.testBase.value == "true"){
	<?php
	}
	?>	
		
		//Looks if scenario name is set
		if(validate_formAF(document.weppin)==true){
	
			// Transvers Baseline management to assess change management
			document.weppin.MANAS.selectedIndex = document.weppin.MAN.selectedIndex;
		
			index = document.weppin.CLI.selectedIndex;
			document.weppin.CLIN.value = document.weppin.CLI.options[index].text;
			document.weppin.MC.value = document.weppin.MCCB.checked;
		
			// Use to open site in right frame (Google Maps problem by submitting from)
			indexState = document.weppin.StateList.selectedIndex;
			indexSL = document.weppin.SL.selectedIndex;
			indexMAN = document.weppin.MAN.selectedIndex;
			indexSHP = document.weppin.SHP.selectedIndex;
			indexCLI = document.weppin.CLI.selectedIndex;
			indexBUF_MAN = document.weppin.BLBUF_MAN.selectedIndex;
			tForm = document.getElementById("weppin");
	
			resultURL = "loadCalculateFilterStrip.php?";
			resultURL = resultURL + "StateList=" + document.weppin.StateList.options[indexState].text;
			resultURL = resultURL + "&LEN=" + document.weppin.LEN.value;
			resultURL = resultURL + "&WID=" + document.weppin.WID.value;
			resultURL = resultURL + "&SHP=" + document.weppin.SHP.options[indexSHP].text;
			resultURL = resultURL + "&STP=" + document.weppin.STP.value;
			resultURL = resultURL + "&SL=" + document.weppin.SL.options[indexSL].text;
			resultURL = resultURL + "&MAN=" + document.weppin.MAN.options[indexMAN].text;
			resultURL = resultURL + "&CLI=" + document.weppin.CLI.options[indexCLI].value;
			resultURL = resultURL + "&CLIN=" + document.weppin.CLI.options[indexCLI].text;
			resultURL = resultURL + "&MC=" + document.weppin.MC.value;
			resultURL = resultURL + "&USE_BUF=" + document.weppin.BLUSE_BUF.value;
			resultURL = resultURL + "&BUF_WIDTH=" + document.weppin.BLBUF_WIDTH.value;
			resultURL = resultURL + "&BUF_MAN=" + document.weppin.BLBUF_MAN.options[indexBUF_MAN].text;
			resultURL = resultURL + "&scenario=" + document.weppin.scenarioAF.value;
	
			parent.frames[2].location = resultURL;
			
			document.weppin.testAF.style.display = "block";
			document.weppin.imgAF.src="images/arrowDown.gif";
		}
	<?php
	if(!isset($_SESSION['ranBaseline'])){
	?>
	}else
	alert("Please run Baseline Conditions first");
	<?php
	}
	?>
}

<!-- Shows the climate station map --> 

function showMap()
{
	index = document.weppin.CLI.selectedIndex;

	mapURL = "map.php?"
	mapURL = mapURL + "Id=" + document.weppin.CLI.options[index].value; // ID Climate Station
	
	parent.frames[2].location = mapURL;
}

<!-- Shows the state map with all climate stations -->

function showStateMap()
{
	index = document.weppin.StateList.selectedIndex;

	mapURL = "stateMap.php?"
	mapURL = mapURL + "State=" + document.weppin.StateList.options[index].text+ "&IX=" + index;
	mapURL = mapURL + "&Len=" + document.weppin.LEN.value;
	mapURL = mapURL + "&Wid=" + document.weppin.WID.value;
	mapURL = mapURL + "&ShpIx=" + document.weppin.SHP.selectedIndex;
	mapURL = mapURL + "&StpIx=" + document.weppin.STP.selectedIndex;
	mapURL = mapURL + "&SLIx=" + document.weppin.SL.selectedIndex;
	mapURL = mapURL + "&ManIx=" + document.weppin.MAN.selectedIndex;
	mapURL = mapURL + "&ManAsIx=" + document.weppin.MANAS.selectedIndex;
	mapURL = mapURL + "&scenario=" + document.weppin.scenario.value;
	mapURL = mapURL + "&testBC=" + document.weppin.testBC.style.display;
	mapURL = mapURL + "&testAC=" + document.weppin.testAC.style.display;
	mapURL = mapURL + "&testAF=" + document.weppin.testAF.style.display;
	mapURL = mapURL + "&testSO=" + document.weppin.testSO.style.display;
	mapURL = mapURL + "&testBase=" + document.weppin.testBase.value;

	parent.frames[2].location = mapURL;
}

<!-- Compares Assess Change Results form differnt runns -->

function compResults()
{
	URL = "compareResults.php";

	parent.frames[2].location = URL;
}

<!-- Compares Assess Filter Strip Results form differnt runns -->

function compAssFil()
{
	URL = "compareAssessFilter.php";

	parent.frames[2].location = URL;
}

<!-- Compares results form differnt runns -->

function startOver()
{
	function confirm_entry()
	{
		input_box=confirm("Do you really want to erase all this work?");
		if (input_box==true)
		{ 
			// Output when OK is clicked
			mapURL = "startOver.php"
			parent.location = mapURL;
		}
		else
		{
			// Output when Cancel is clicked
		}
	}
	
	confirm_entry();
	
}

<!-- Modifies Climate file and sends station id --> 

function modifCli(useaf)
{ 
	index = document.weppin.CLI.selectedIndex;
	document.RockClime.station.value = document.weppin.CLI.options[index].value;
	var uc = document.getElementById("UMC");
	uc.style.display = "block";
	document.weppin.MCCB.checked = true;
	//document.RockClime.submit();
	
	// Use to open site in right frame (otherwise Google Maps problem by submitting from)
	modCliURL = "/cgi-bin/fswepp/rc/climate.cli?";
	modCliURL = modCliURL + "state=" + document.RockClime.state.value;
	modCliURL = modCliURL + "&workingdir=" + document.RockClime.workingdir.value;
	modCliURL = modCliURL + "&station=" + document.RockClime.station.value;
	modCliURL = modCliURL + "&useaf=" + useaf;
	modCliURL = modCliURL + "&startyear=" + document.RockClime.startyear.value;
	modCliURL = modCliURL + "&simyears=" + document.RockClime.simyears.value;
	modCliURL = modCliURL + "&action=" + document.RockClime.action.value;
	modCliURL = modCliURL + "&comefrom=" + document.RockClime.comefrom.value;
	modCliURL = modCliURL + "&units=" + document.RockClime.units.value;
	modCliURL = modCliURL + "&me=" + document.RockClime.me.value;

	parent.frames[2].location = modCliURL;	
}

<!-- Display/hide Baseline Buffer --> 

function toggleBLFS()
{
	var blfs = document.getElementById("testBLFS");
 	var tForm = document.getElementById("weppin");
 	var usblf = document.getElementById("usblf");
 	
  	if (blfs.style.display == "none")
  	{
  		blfs.style.display = "block";
  		document.weppin.BLUSE_BUF.value = "true";		
  		usblf.value="Hide Filter Strip";
	}
	else if(blfs.style.display == "block")
	{
  		blfs.style.display = "none";
  		document.weppin.BLUSE_BUF.value = "false";	
  		usblf.value="Use Filter Strip";
	}
}


<!-- Display/hide Assess Change Buffer --> 

function toggleFS()
{
	var fs = document.getElementById("testFS");
 	var tForm = document.getElementById("weppin");
 	var usf = document.getElementById("usf");
 	
  	if (fs.style.display == "none")
  	{
  		fs.style.display = "block";
  		document.weppin.USE_BUF.value = "true";		
  		usf.value="Hide Filter Strip";
	}
	else if(fs.style.display == "block")
	{
  		fs.style.display = "none";
  		document.weppin.USE_BUF.value = "false";	
  		usf.value="Use Filter Strip";
	}
}

<!-- Display/hide Baseline Conditions -->

function toggleBC()
{
	
	var testBC = document.getElementById("testBC");
	
  	if (testBC.style.display == "none")
  	{
  		testBC.style.display = "block";
  		document.weppin.imgBC.src="images/arrowDown.gif";
	}
	else if(testBC.style.display == "block")
	{
  		testBC.style.display = "none";
  		document.weppin.imgBC.src="images/arrowUp.gif";
	}
}

<!-- Display/hide Assess Change --> 

function toggleAC()
{
	
	var testAC = document.getElementById("testAC");
	
  	if (testAC.style.display == "none")
  	{
  		testAC.style.display = "block";		
  		document.weppin.imgAC.src="images/arrowDown.gif";
	}
	else if(testAC.style.display == "block")
	{
  		testAC.style.display = "none";
  		document.weppin.imgAC.src="images/arrowUp.gif";
	}
}

<!-- Display/hide Assess Filter Stirp --> 

function toggleAF()
{
	
	var testAF = document.getElementById("testAF");
	
  	if (testAF.style.display == "none")
  	{
  		testAF.style.display = "block";		
  		document.weppin.imgAF.src="images/arrowDown.gif";
	}
	else if(testAF.style.display == "block")
	{
  		testAF.style.display = "none";
  		document.weppin.imgAF.src="images/arrowUp.gif";
	}
}

<!-- Display/hide Start over --> 

function toggleSO()
{
	
	var testSO = document.getElementById("testSO");
	
  	if (testSO.style.display == "none")
  	{
  		testSO.style.display = "block";		
  		document.weppin.imgSO.src="images/arrowDown.gif";
	}
	else if(testSO.style.display == "block")
	{
  		testSO.style.display = "none";
  		document.weppin.imgSO.src="images/arrowUp.gif";
	}
}


<!-- Validation example W3schools --> 

function validate_required(field,alerttxt)
{
	with (field)
	{
	if (value==null||value=="")
  		{alert(alerttxt);return false}
	else {return true}
	}
}

<!-- Validation for the scenario name (Assess Change)--> 

function validate_form(thisform)
{
	with (thisform)
	{
	if (validate_required(scenario,"Scenario name must be filled out!")==false)
  		{scenario.focus();return false}
	else
  		return true
	}
}

<!-- Validation for the scenario name (Assess Filter Strip)--> 

function validate_formAF(thisform)
{
	with (thisform)
	{
	if (validate_required(scenarioAF,"Scenario name must be filled out!")==false)
  		{scenarioAF.focus();return false}
	else
  		return true
	}
}

</script>

<!-- Form for Rock:Clime input -->

<form method="POST" name="RockClime" action="/cgi-bin/fswepp/rc/climate.cli" target="right">
    
   <input type="hidden" name="state" value="<?php echo strtolower($state)?>">
   <input type="hidden" name="station">
   <input type="hidden" name="startyear" value="1900">
   <input type="hidden" name="simyears" value="100">
   <input type="hidden" name="action" value="-download">
   <input type="hidden" name="comefrom" value="">
   <input type="hidden" name="units" value="ft">
   <input type="hidden" name="me" value="">
   <input type="hidden" name="workingdir" value="<?php echo $workingdir ?>">
   
</form>

<!-- Form for input section -->

<form method="GET" name="weppin" id="weppin" action="loadResults.php" target="right"> 
   
   <!-- Baseline Conditions section -->
   
   <span><a class="header" href="#" onClick="toggleBC();"><img name="imgBC" src="images/arrowDown.gif" border="0" width="19" height="10">&nbsp;&nbsp;&nbsp;Baseline Conditions</a></span>
   <input type="hidden" name="testBase" value="false">
   
   <br>
   <fieldset class="chapter" id="testBC" name="testBC" style="display:block;">
   <fieldset>
    <legend>Location</legend>
    <!-- State list -->
    <label for="sta">State:</label>
    <select size="1" onChange="javascript:newState();" name="StateList">
      	<?php
		// List all states in the drop down list
		listStates();
		?>
    </select>
    <br />
    <!-- Climate Staion -->
    <label for="cst">Climate Station:</label>
    <select size="1" name="CLI">
		<?php
		// Connnect to database
		if (!($Connection = mysql_connect("127.0.0.1", "weppcat"))) {
			print ("Could not establish connection.<BR>\n");
			exit;
		}
		// Makes string lowercase
		$state = strtolower($state);
		// List all stations in this state
		listStations($Connection, $state, null);
		?>
    </select>
    <input type="hidden" name="CLIN"> 
    <br />
    <!-- Soil -->
    <label for="soi">Soil Type:</label>
    <select style="width: 200px;" size="1" name="SL">
   		<?php
		// Makes state uppercase
		$state = strtoupper($state);
		// Lists soils from station 
		listSoils($Connection, $state, null);
		?>
    </select>
    <!-- Climate Station Map Button -->
    <center>
    <input type="button" value="Show Climate Station on Map" name="map" onClick="showMap();">
    <!-- State Map Button -->
    <input type="button" value="Show All Climate Stations in State" name="stateMap" onClick="showStateMap();">
  </center>
  </fieldset>
  <fieldset>
    <legend>Field</legend>
    <!-- Field Length -->
    <label for="fle"><a href="#" onClick="popup1('help1', 'Field Length is the entire length of the hillslope, including the Filter Strip width if the filter strip option is used.');return false;" name="help1" id="help1"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Length (ft):</label>
    <input type="text" name="LEN" value="100"></input>
    <br />
    <!-- Field Width -->
    <label for="fwi"> Width (ft):</label>
    <input type="text" name="WID" value="100"></input>
    <br />
    <!-- Slope Shape -->
    <label for="ssh">Slope Shape:</label>
    <select size="1" width="200px" name="SHP">
    		<option>Uniform</option>
    		<option>Convex</option>
    		<option>Concave</option>
    		<option>S-shaped</option>
  	</select>
    <br />
    <!-- Steepness -->
    <label for="ste">Steepness:</label>
	<select size="1" name="STP">
    		<option value=0>0%</option>
    		<option value=1>1%</option>
    		<option value=2>2%</option>
    		<option value=3>3%</option>
    		<option value=4>4%</option>
    		<option value=5>5%</option>
    		<option value=6>6%</option>
    		<option value=7>7%</option>
    		<option value=8>8%</option>
    		<option value=9>9%</option>
    		<option value=10>10%</option>
   		 	<option value=15>15%</option>
    		<option value=20>20%</option>
    		<option value=25>25%</option>
    		<option value=30>30%</option>
    		<option value=35>35%</option>
    		<option value=40>40%</option>
    		<option value=45>45%</option>
    		<option value=50>50%</option>
  	</select><input type="button" value="View" name="slpVw" onClick="showSlpGraph();">
    <br />
    <b><div><a href="javascript:showSlope();">View Standard Slope Shapes</a></div></b>
  </fieldset>
  <fieldset>
    <legend>Field Management</legend>
     <!-- Management -->
     <label class ="long" for="man">Management:</label> 
	 <select style="width: 310px;" size="1" name="MAN">
        <?php
		// Lists all possible Managements in this state
		listManagements($Connection, $state, null);
		?>
     </select>
     <!-- Link to view possible management schedule -->
     <b><div><a href="javascript:showMan();">View Management</a></div></b>
  </fieldset>
  <input type="hidden" name="BLUSE_BUF" value="false">
  <!-- Use/Hide Filter Strip -->
  <fieldset>
    <legend>Filter Strip</legend>
  	<input id="usblf" type="button" value="Use Filter Strip" name="usblf" onClick="toggleBLFS();">	
    <div  id="testBLFS" style="display:none;">
	  	<!-- Width of Filter Strip -->
	  	<label class ="long" for="blfis">Width(ft):</label>
	  	<input type="text" name="BLBUF_WIDTH" size="7" value="10"><br>
	  	<!-- Filter Strip Management -->
	  	<label class ="long" for="blma2">Filter Strip Management:</label>
	  	<select style="width: 310px;" size="1" name="BLBUF_MAN">
	 		<?php
			// Lists all possible Managements in this state
			listFilterManagements($Connection, $state, null);
			?>
	  	</select></b></p>
  	</div>
  </fieldset>
  <br>
  <center><input type="button"  value="Run Baseline Conditions" onClick="startItBC();" name="B1"></center>
  </fieldset>
  <br>
  
  <!-- Assess Change section -->

  <span><a class="header" href="#" onClick="toggleAC();"><img name="imgAC" src="images/arrowUp.gif" border="0" width="19" height="10">&nbsp;&nbsp;&nbsp;Assess Change</a></span>
  <br>
  <fieldset class="chapter" id="testAC" name="testAC" style="display:none;">
  	<!-- Name for the Scenario -->
  	<fieldset>
  		<legend>Scenario</legend>
  		<!-- Name for the Scenario -->
  		<label for="nam">Name:</label>
  		<input type="text" name="scenario" size="20" value=""><br>
  	</fieldset>
  	<!-- Modify Climate -->
  	<fieldset>
	  	<legend>Climate</legend>
	  	<input type="button" value="Modify Climate" name="modCli" onClick="modifCli('false');">
	  	<!-- Checkbox use modified Climate -->
	  	<div id="UMC" style="display:none;"><input type="checkbox" name="MCCB" value="ON"><b>Use modified climate</b></div>
	  	<input type="hidden" name="MC" value="false"><br>
  	</fieldset>
  	<fieldset>
    	<legend>Field Management</legend>
    	<!-- Management -->
    	<label class ="long" for="manAss">Management:</label> 
		<select style="width: 310px;" size="1" name="MANAS">
        	<?php
			// Lists all possible Managements in this state
			listManagements($Connection, $state, null);
			?>
    	</select>
    	<!-- Link to view possible management schedule -->
    	<b><div><a href="javascript:showMan();">View Management</a></div></b>
  	</fieldset>
  	
  	<input type="hidden" name="USE_BUF" value="false">
  	<!-- Use/Hide Filter Strip -->
  	<fieldset>
  		<legend>Filter Strip</legend>
  		<input id="usf" type="button" value="Use Filter Strip" name="usf" onClick="toggleFS();">
  		<div id="testFS" style="display:none;">
	  		<!-- Width of Filter Strip -->
	  		<label class ="long" for="fis">Width(ft):</label>
	  		<input type="text" name="BUF_WIDTH" size="7" value="10"><br>
	  		<!-- Filter Strip Management -->
	  		<label class ="long" for="ma2">Filter Strip Management:</label>
	  		<select style="width: 310px;" size="1" name="BUF_MAN">
	 			<?php
				// Lists all possible Managements in this state
				listFilterManagements($Connection, $state, null);
				?>
	  		</select></b></p>
	  	</div>
  	</fieldset>
  	<!-- Calculate Soil Lose Button -->
  	<br><br>
  	<center><input type="button"  value="Run Assess Change Scenario" onClick="startIt();" name="B2"></center>
  	<br>
  	<!-- Compare Asses Changes Button -->
  	<fieldset>
  		<legend>Compare Assess Change Scenarios</legend>
  		<input type="button" value="Compare" name="comResBut" onClick="compResults();">
  	</fieldset>
  </fieldset>
  <br>
  
  <!-- Assess Filter Strip section-->

  <span><a class="header" href="#" onClick="toggleAF();"><img name="imgAF" src="images/arrowUp.gif" border="0" width="19" height="10">&nbsp;&nbsp;&nbsp;Assess Filter Strip</a></span>
  <br>
  <fieldset class="chapter" id="testAF" name="testAF" style="display:none;">
  	<!-- Name for the Scenario -->
  	<fieldset>
  		<legend>Scenario</legend>
  		<!-- Name for the Scenario -->
  		<label for="namAF">Name:</label>
  		<input type="text" name="scenarioAF" size="20" value=""><br>
  	</fieldset>
  	<!-- Modify Climate -->
  	<fieldset>
	  	<legend>Climate</legend>
	  	<input type="button" value="Modify Climate" name="modCliAF" onClick="modifCli('true');">
	  	<!-- Checkbox use modified Climate -->
	  	<div id="UMCAF" style="display:none;"><input type="checkbox" name="MCCBAF" value="ON"><b>Use modified climate</b></div>
	  	<input type="hidden" name="MCAF" value="false"><br>
  	</fieldset>
  	<!-- Compute New Filter Strip Button -->
  	<br><br>
  	<center><input type="button"  value="Compute New Filter Strip" onClick="compFilter();" name="B3"></center>
    <br>
    <!-- Compare Asses Filter Strips Button -->
    <fieldset>
    	<legend>Compare Assess Filter Strip Scenarios</legend>
  		<input type="button" value="Compare" name="assFilBut" onClick="compAssFil();">
  	</fieldset>
  </fieldset>
  <br>  
  
  <!-- Start over section -->
  
  <span><a class="header" href="#" onClick="toggleSO();"><img name="imgSO" src="images/arrowUp.gif" border="0" width="19" height="10">&nbsp;&nbsp;&nbsp;Start Over</a></span>
  <br>
  <fieldset class = "chapter" id="testSO" name="testSO" style="display:none;">
  	<center><input type="button"  value="Start Over" name="stOver" onClick="startOver();"></center>
  </fieldset>
</form>

<div ID="helpdiv1" STYLE="position:absolute;visibility:hidden;background-color:#FFFFCC;padding: 4px; font:14px/1 sans-serif; border:2px solid #000000;"></div>

</body>

</html>
