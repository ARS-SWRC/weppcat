<!--  WEPP Internet model interface: Extern functions for WEPPCAT 
  --
  --  Jim Frankenberger
  --  USDA-ARS, West Lafayette IN
  --
  --  Customized for WEPPCAT
  --  by Daniel Esselbrugge
  --  USDA-ARS-SWRC, Tucson AZ
-->

<?php

// Makes Working Dir for Slope Preview
function makeWorkingDir() {
	// File for output and runs folder
	$workingDir = "/home/wepp/" . session_id();
	if (!file_exists($workingDir)) {
		mkdir($workingDir);
	}
}

// Sets Working Dir for file paths for used programs
function setWorkingDir() {
	// File for output and runs folder
	$workingDir = "/home/wepp/" . session_id();
	if (!file_exists($workingDir)) {
		mkdir($workingDir);
	}
	// Create runs folder for all WEPP input files
	if (!file_exists($workingDir . "/runs")) {
		mkdir($workingDir . "/runs");
	}
	// Copies cliegen program in the runs folder
	if (!file_exists($workingDir . "/runs/cligen"))
		copy("/home/wepp/data/climates/cligen/cligen", $workingDir . "/runs/cligen");
	// Error if cliegen could not be copied
	if (chmod($workingDir . "/runs/cligen", 0755) == FALSE)
		echo ('Could not chmod<p>');
	// Creates output folder for WEPP output files
	if (!file_exists($workingDir . "/output"))
		mkdir($workingDir . "/output");
}

// List with all US States

function listStates() {
	print ("<option>Alabama</option>");
	print ("<option>Alaska</option>");
	print ("<option>Arizona</option>");
	print ("<option>Arkansas</option>");
	print ("<option>California</option>");
	print ("<option>Colorado</option>");
	print ("<option>Connecticut</option>");
	print ("<option>Delaware</option>");
	print ("<option>Florida</option>");
	print ("<option>Georgia</option>");
	print ("<option>Hawaii</option>");
	print ("<option>Idaho</option>");
	print ("<option>Illinois</option>");
	print ("<option>Indiana</option>");
	print ("<option>Iowa</option>");
	print ("<option>Kansas</option>");
	print ("<option>Kentucky</option>");
	print ("<option>Louisiana</option>\n");
	print ("<option>Maine</option>");
	print ("<option>Maryland</option>");
	print ("<option>Massachusetts</option>");
	print ("<option>Michigan</option>");
	print ("<option>Minnesota</option>");
	print ("<option>Mississippi</option>");
	print ("<option>Missouri</option>");
	print ("<option>Montana</option>\n");
	print ("<option>Nebraska</option>");
	print ("<option>Nevada</option>");
	print ("<option>New Hampshire</option>");
	print ("<option>New Jersey</option>");
	print ("<option>New Mexico</option>");
	print ("<option>New York</option>");
	print ("<option>North Carolina</option>");
	print ("<option>North Dakota</option>");
	print ("<option>Ohio</option>");
	print ("<option>Oklahoma</option>");
	print ("<option>Oregon</option>");
	print ("<option>Pennsylvania</option>");
	print ("<option>Rhode Island</option>");
	print ("<option>South Carolina</option>");
	print ("<option>South Dakota</option>");
	print ("<option>Tennessee</option>");
	print ("<option>Texas</option>");
	print ("<option>Utah</option>");
	print ("<option>Vermont</option>");
	print ("<option>Virginia</option>");
	print ("<option>Washington</option>");
	print ("<option>West Virginia</option>");
	print ("<option>Wisconsin</option>");
	print ("<option>Wyoming</option>");
};

// Sets state abbreviation

function toStateAbbr($stateLong) {
	if ($stateLong != "") {
		switch ($stateLong) {
			case "Alabama" :
				$state = "AL";
				break;
			case "Alaska" :
				$state = "AK";
				break;
			case "Arizona" :
				$state = "AZ";
				break;
			case "Arkansas" :
				$state = "AR";
				break;
			case "California" :
				$state = "CA";
				break;
			case "Colorado" :
				$state = "CO";
				break;
			case "Connecticut" :
				$state = "CT";
				break;
			case "Delaware" :
				$state = "DE";
				break;
			case "Florida" :
				$state = "FL";
				break;
			case "Georgia" :
				$state = "GA";
				break;
			case "Hawaii" :
				$state = "HI";
				break;
			case "Idaho" :
				$state = "ID";
				break;
			case "Illinois" :
				$state = "IL";
				break;
			case "Indiana" :
				$state = "IN";
				break;
			case "Iowa" :
				$state = "IA";
				break;
			case "Kansas" :
				$state = "KS";
				break;
			case "Kentucky" :
				$state = "KY";
				break;
			case "Louisiana" :
				$state = "LA";
				break;
			case "Maine" :
				$state = "ME";
				break;
			case "Maryland" :
				$state = "MD";
				break;
			case "Massachusetts" :
				$state = "MA";
				break;
			case "Michigan" :
				$state = "MI";
				break;
			case "Minnesota" :
				$state = "MN";
				break;
			case "Mississippi" :
				$state = "MS";
				break;
			case "Missouri" :
				$state = "MO";
				break;
			case "Montana" :
				$state = "MT";
				break;
			case "Nebraska" :
				$state = "NE";
				break;
			case "Nevada" :
				$state = "NV";
				break;
			case "New Hampshire" :
				$state = "NH";
				break;
			case "New Jersey" :
				$state = "NJ";
				break;
			case "New Mexico" :
				$state = "NM";
				break;
			case "New York" :
				$state = "NY";
				break;
			case "North Carolina" :
				$state = "NC";
				break;
			case "North Dakota" :
				$state = "ND";
				break;
			case "Ohio" :
				$state = "OH";
				break;
			case "Oklahoma" :
				$state = "OK";
				break;
			case "Oregon" :
				$state = "OR";
				break;
			case "Pennsylvania" :
				$state = "PA";
				break;
			case "Rhode Island" :
				$state = "RI";
				break;
			case "South Carolina" :
				$state = "SC";
				break;
			case "South Dakota" :
				$state = "SD";
				break;
			case "Tennessee" :
				$state = "TN";
				break;
			case "Texas" :
				$state = "TX";
				break;
			case "Utah" :
				$state = "UT";
				break;
			case "Vermont" :
				$state = "VT";
				break;
			case "Virginia" :
				$state = "VA";
				break;
			case "Washington" :
				$state = "WA";
				break;
			case "West Virginia" :
				$state = "WV";
				break;
			case "Wisconsin" :
				$state = "WI";
				break;
			case "Wyoming" :
				$state = "WY";
				break;
			default :
				$state = "AL";
		}
	} else
		$state = "AL";

	return $state;
}

// Lists requested Managements from DB

function listManagements($Connection, $state, $man) {

	// Creats SQL query
	$sqlstmt = "SELECT name from managements where (state='" . $state . "' or state='*') order by name";

	// Execute SQL query
	if (!($Result = mysql_db_query("wepp", $sqlstmt, $Connection))) {
		// Display Error message
		print ("</select>");
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
		print ("</select>");
		print ("<center>");
		print ("There are no records.");
		print ($sqlstmt);
		print ("</center>");
		mysql_free_result($Result);
		exit;
	}

	// Runs through all rows from SQL reply and write it to the management drop down
	for ($Row = 0; $Row < mysql_num_rows($Result); $Row++) {
		$name = mysql_result($Result, $Row, "name");
		if ($man != null) {
			if ($man == $name)
				print ("<option selected>" . $name . "</option>");
			else
				print ("<option>" . $name . "</option>");
		} else {
			if ($Row == 0)
				print ("<option selected>" . $name . "</option>");
			else
				print ("<option>" . $name . "</option>");
		}
	}
	// Free result memory
	mysql_free_result($Result);
}

// Lists requested Managements from DB

function listFilterManagements($Connection, $state, $man) {

	// Creats SQL query
	$sqlstmt = "SELECT name from filtermanagements where (state='" . $state . "' or state='*') order by name";

	// Execute SQL query
	if (!($Result = mysql_db_query("wepp", $sqlstmt, $Connection))) {
		// Display Error message
		print ("</select>");
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
		print ("</select>");
		print ("<center>");
		print ("There are no records.");
		print ($sqlstmt);
		print ("</center>");
		mysql_free_result($Result);
		exit;
	}

	// Runs through all rows from SQL reply and write it to the management drop down
	for ($Row = 0; $Row < mysql_num_rows($Result); $Row++) {
		$name = mysql_result($Result, $Row, "name");
		if ($man != null) {
			if ($man == $name)
				print ("<option selected>" . $name . "</option>");
			else
				print ("<option>" . $name . "</option>");
		} else {
			if ($Row == 0)
				print ("<option selected>" . $name . "</option>");
			else
				print ("<option>" . $name . "</option>");
		}
	}
	// Free result memory
	mysql_free_result($Result);
}

// List requested soils form DB

function listSoils($Connection, $state, $soil) {

	// Makes sting uppercase
	$state = strtoupper($state);

	// Creats SQL query
	$sqlstmt = "SELECT DISTINCT name,texture from soils where state = '" . $state . "' order by name";

	// Execute SQL query
	if (!($Result = mysql_db_query("wepp", $sqlstmt, $Connection))) {
		// Display Error message
		print ("</select>");
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
		print ("</select>");
		print ("<center>");
		print ("There are no records.");
		print ($sqlstmt);
		print ("</center>");
		mysql_free_result($Result);

		exit;
	}

	// Runs through all rows from SQL reply and write it to the soil drop down
	for ($Row = 0; $Row < mysql_num_rows($Result); $Row++) {
		$name = mysql_result($Result, $Row, "name");
		$texture = mysql_result($Result, $Row, "texture");
		$fullname = $name . "(" . $texture . ")";
		if ($soil != null) {
			if ($soil == $fullname)
				print ("<option selected>" . $fullname . "</option>");
			else
				print ("<option>" . $fullname . "</option>");
		} else {
			if ($Row == 0)
				print ("<option selected>" . $fullname . "</option>");
			else
				print ("<option>" . $fullname . "</option>");
		}
	}
	// Free result memory
	mysql_free_result($Result);
}

// List requested climate stations form DB

function listStations($Connection, $state, $default) {

	// Creats SQL query
	$sqlstmt = "SELECT station, id from climates where state ='" . $state . "' order by station";

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
		// If climate ID is < 6
		if (strlen($index) < 6)
		$index = '0' . $index;
		
		if ($default != null) {
			if ($station == $default)
				print ("<option value=" . '"' . $index . '"' . " SELECTED>" . $station . "</option>");
			else
				print ("<option value=" . '"' . $index . '"' . ">" . $station . "</option>");
						
		} else {
			if ($Row == 0)
				print ("<option value=" . '"' . $index . '"' . " SELECTED>" . $station . "</option>");
			else
				print ("<option value=" . '"' . $index . '"' . ">" . $station . "</option>");
		}
	}
	// Free result memory
	mysql_free_result($Result);

}

?>
