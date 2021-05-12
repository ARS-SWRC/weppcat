#! /usr/bin/perl
#! /fsapps/fssys/bin/perl
#
# modpar.pl
#

   $version = "2008.05.22";
   $debug = 0;

# This is a highly modified version of Rock:Clime and FSWEPP :
#  FSWEPP, USDA Forest Service, Rocky Mountain Research Station, Soil & Water Engineering
#  Science by Bill Elliot et alia                    Code by David Hall and Dayna Scheele
# This code was originally modified for WEPPCAT by Daniel Esselbrugge
# of the Southwest Watershed Research Center ARS-USDA
# It was further greatly modified by Eric Anson also of SWRC

# The purpose of these files is to provide a web interface for a user to change the PAR
# file that is fed as input to Cligen.  modpar.pl is the entry point for these files
# It can be invoked via a post or a 'exec' type call.  There are three parameters to be
# passed that can passed as command line vars, form vars (from post), or
# as address line parameters.

#  example usage:
#    exec modpar.pl $workingdir $CL $units
#    
#  arguments:
#    $workingdir  directory where the new PAR file should be written
#    $CL          climate file name of PAR file to be modified
#    $units       'm' or 'ft'

#  Note the workingdir must be already created.  Currently the new parameter file written
#  to that directory is the ip address of the user (with '.' replaced by '_') followed by
#  .PAR  This is for historical reasons and I may change it in the future.
#  $CL should include the path and the file name, but no extension (.PAR gets added by modpar)
#                (I may change this too)
#  There are subdirectories of the directory modpar resides in which consist of the two letter
#  id of the state (lowercase) which contain the PAR files for the US stations so ...
#  most often the PAR files used by the program for the base will be 
#  <state>/<STATE><station>.PAR
#  where <state> is the 2 letter state in lowercase and <STATE> in uppercase

#  modpar can post to prisform.pl which posts back to modpar
#  prisform.pl modifies the lat, long, elev, and mean parcipitation, so that info read
#  modpar discovers that it is being called by prism by the existance of a form
#  var called "prism". If and only if that form parmeter exists does modpar use
#  pars from the calling program for the lat, long, elev, and mean parcip.

# Read input parameter
   $workingdir=$ARGV[0];
   $CL=$ARGV[1];
   $units=$ARGV[2]; 
 
# If no parameters from command-line, try from form
   if ($CL . $units . $workingdir eq "") { 
      $parse=1;
      &ReadParse(*parameters);
      $workingdir = $parameters{'workingdir'};
      $CL = $parameters{'CL'};
      $units = $parameters{'units'};
      $prism = $parameters{"prism"};  #should be defined iff called from prism.pl and
                                      #prism.pl not exited via a retreat
   }

## If still no parameters from command-line or from form, bail

   if ($CL . $units . $workingdir eq "") {
      print "Content-type: text/html\n\n";
      print "<HTML>\n";
      print " <HEAD>\n";
      print "  <meta http-equiv=\"Refresh\" content=\"0; URL=/weppcat/weppcat_info.htm\">\n";
      print " </HEAD>\n";
      print "</html>\n";
      die;
   }

   if ($units eq '-um')  {$units = 'm'}		# DEH 07/19/00
   if ($units eq '-uft') {$units = 'ft'}	# DEH 07/19/00
 
   $wepphost="typhoon.tucson.ars.ag.gov";
   if (-e "../wepphost") {
      open HOST, "<../wepphost";
      $wepphost=lc(<HOST>);
      chomp $wepphost;
      close HOST;
   }

   $climateFile = $CL . '.PAR';
   open PAR, "<$climateFile";
   $line=<PAR>;                           # EPHRATA CAA AP WA                       452614 0
   $climate_name = substr($line,1,32);

   $line=<PAR>;                           # LATT=  47.30 LONG=-119.53 YEARS= 44. TYPE= 3
   $lat = 0 + substr($line,6,7);     #adding 0 so treated as number (no extra blanks) ELA
   $lon = 0 + substr($line,19,7);
   $line=<PAR>;	# ELEVATION = 1260. TP5 = 0.86 TP6= 2.90
   $elev = 0 + substr($line,12,6);
   $line=<PAR>;	# MEAN P   0.10  0.10  0.11  0.10  0.11  0.14  0.14  0.09  0.10  0.10  0.12  0.12
   for $ii (0..11) {$mean_p_if[$ii]= substr($line,8+$ii*6,6)}; #mean percip per event day
   $line=<PAR>;	# S DEV P  0.12  0.12  0.11  0.13  0.13  0.18  0.22  0.13  0.13  0.11  0.14  0.13
   for $ii (0..11) {$sd_p[$ii]=substr($line,8+$ii*6,6)};
   $line=<PAR>;	# SQEW  P  1.88  2.30  2.21  2.15  2.29  2.35  3.60  3.22  2.05  2.49  2.22  1.87
   $line=<PAR>;	# P(W/W)   0.47  0.50  0.39  0.32  0.33  0.30  0.27  0.28  0.40  0.41  0.42  0.48
   for $ii (0..11) {$pww[$ii]=substr($line,8+$ii*6,6)};
   $line=<PAR>;	# P(W/D)   0.20  0.16  0.15  0.13  0.13  0.11  0.05  0.06  0.08  0.12  0.23  0.23
   for $ii (0..11) {$pwd[$ii]=substr($line,8+$ii*6,6)};
   $line=<PAR>;	# TMAX AV 32.89 41.62 52.60 62.81 72.56 80.58 88.52 87.06 77.76 62.85 44.78 34.63
   for $ii (0..11) {$tmax[$ii]=substr($line,8+$ii*6,6)};
   $line=<PAR>;	# TMIN AV 20.31 26.55 32.33 39.12 47.69 55.39 61.58 60.31 51.52 40.17 30.33 22.81
   for $ii (0..11) {$tmin[$ii]=substr($line,8+$ii*6,6)};
   close PAR;

   @month_name=qw(January February March April May June July August September October November December);
   @month_days=(31,28,31,30,31,30,31,31,30,31,30,31);

#******************************************************#
# Calculation from parameter file for displayed values #
#******************************************************#

   $annual_precip = 0;
   $annual_wet_days = 0;
   for $i (0..11) {
      $pw[$i] = $pwd[$i] / (1 + $pwd[$i] - $pww[$i]);  #pw = probability of wet day
      $num_wet[$i] = sprintf '%.2f',$pw[$i] * $month_days[$i];
      $mean_p[$i] = sprintf '%.2f',$num_wet[$i] * $mean_p_if[$i];
      $tmax[$i] = $tmax[$i]*1;      # convert 'types' to numbers(stupid perl and java script)
      $tmin[$i] = $tmin[$i]*1;
        
      if ($units eq 'm') {
         $mean_p[$i] = sprintf '%.2f',25.4 * $mean_p[$i];                 # inches to mm
         $tmax[$i] = sprintf '%.2f',($tmax[$i] - 32) * 5/9;      # deg F to deg C
         $tmin[$i] = sprintf '%.2f',($tmin[$i] - 32) * 5/9;      # deg F to deg C
      }
      $annual_precip += $mean_p[$i];
      $annual_wet_days += $num_wet[$i];

      if ($prism eq "") {                 # display file values when no prism input
         $ppcp[$i] = $mean_p[$i];
      } else {                            # get prism input
      	 $j = $i + 1;
      	 $ppcp[$i]=$parameters{"ppcp$j"};
      }
   }
   if ($prism eq "") {                 # display file values when no prism input
      $ppcp = $annual_precip;
      $plat = $lat;
      $plon = $lon;
      $lathemisphere='N';
      $longhemisphere='W';
      if ($units eq "m") 
         {$pelev = $elev/3.28;}
      else 
         {$pelev = $elev;}
   } else {                             # get prism input
      $plat = $parameters{'platitude'};
      $plon = $parameters{'plongitude'};
      $pelev = $parameters{'elev'};
      $ppcp = $parameters{"ppcp"};
   }


#############
# HTML Page #
#############

print "Content-type: text/html\n\n";
print "<HTML>\n";
print " <HEAD>\n";
print "  <TITLE>Modify Climate</TITLE>\n";

print '<style type="text/css">';
print	"</style>";
  
print '<script language="javascript" src="../../../weppcat/js/PopupWindow.js"></script>
       <script language="javascript" type="text/javascript">

// DE EDITS

// Create PopupWindow objects (right)
var helppopup1 = new PopupWindow("helpdiv1");
helppopup1.offsetY = 25;
helppopup1.autoHide();

// Create PopupWindow objects (left)
var helppopupLeft = new PopupWindow("helpdiv1");
helppopupLeft.offsetX = -200;
helppopupLeft.offsetY = 25;
helppopupLeft.autoHide();


function popup1(anchorname, text) {
	helppopup1.populate(text);
	helppopup1.showPopup(anchorname);
}

function popupLeft(anchorname, text) {
	helppopupLeft.populate(text);
	helppopupLeft.showPopup(anchorname);
}
       
       ';

print "

   var units = '$units'

   // Mean maximum Temperature
   var otx1 = parseFloat($tmax[0])
   var otx2 = parseFloat($tmax[1])
   var otx3 = parseFloat($tmax[2])
   var otx4 = parseFloat($tmax[3])
   var otx5 = parseFloat($tmax[4])
   var otx6 = parseFloat($tmax[5])
   var otx7 = parseFloat($tmax[6])
   var otx8 = parseFloat($tmax[7])
   var otx9 = parseFloat($tmax[8])
   var otx10 = parseFloat($tmax[9])
   var otx11 = parseFloat($tmax[10])
   var otx12 = parseFloat($tmax[11])

   // Mean minimum Temperature
   var otn1 = parseFloat($tmin[0])
   var otn2 = parseFloat($tmin[1])
   var otn3 = parseFloat($tmin[2])
   var otn4 = parseFloat($tmin[3])
   var otn5 = parseFloat($tmin[4])
   var otn6 = parseFloat($tmin[5])
   var otn7 = parseFloat($tmin[6])
   var otn8 = parseFloat($tmin[7])
   var otn9 = parseFloat($tmin[8])
   var otn10 = parseFloat($tmin[9])
   var otn11 = parseFloat($tmin[10])
   var otn12 = parseFloat($tmin[11])

   // Mean precipitation
   var opc1 = parseFloat($mean_p[0])
   var opc2 = parseFloat($mean_p[1])
   var opc3 = parseFloat($mean_p[2])
   var opc4 = parseFloat($mean_p[3])
   var opc5 = parseFloat($mean_p[4])
   var opc6 = parseFloat($mean_p[5])
   var opc7 = parseFloat($mean_p[6])
   var opc8 = parseFloat($mean_p[7])
   var opc9 = parseFloat($mean_p[8])
   var opc10 = parseFloat($mean_p[9])
   var opc11 = parseFloat($mean_p[10])
   var opc12 = parseFloat($mean_p[11])
   var spc = $annual_precip
   
   // Mean Standard Deviation 
   var osd1 = parseFloat($sd_p[0])
   var osd2 = parseFloat($sd_p[1])
   var osd3 = parseFloat($sd_p[2])
   var osd4 = parseFloat($sd_p[3])
   var osd5 = parseFloat($sd_p[4])
   var osd6 = parseFloat($sd_p[5])
   var osd7 = parseFloat($sd_p[6])
   var osd8 = parseFloat($sd_p[7])
   var osd9 = parseFloat($sd_p[8])
   var osd10 = parseFloat($sd_p[9])
   var osd11 = parseFloat($sd_p[10])
   var osd12 = parseFloat($sd_p[11])

   // Number of wet days
   var onw1 = parseFloat($num_wet[0])
   var onw2 = parseFloat($num_wet[1])
   var onw3 = parseFloat($num_wet[2])
   var onw4 = parseFloat($num_wet[3])
   var onw5 = parseFloat($num_wet[4])
   var onw6 = parseFloat($num_wet[5])
   var onw7 = parseFloat($num_wet[6])
   var onw8 = parseFloat($num_wet[7])
   var onw9 = parseFloat($num_wet[8])
   var onw10 = parseFloat($num_wet[9])
   var onw11 = parseFloat($num_wet[10])
   var onw12 = parseFloat($num_wet[11])
   var snw = $annual_wet_days

   // Wet day follows wet day
   opww = new MakeArray(12)
   opww[1] = parseFloat($pww[0])
   opww[2] = parseFloat($pww[1])
   opww[3] = parseFloat($pww[2])
   opww[4] = parseFloat($pww[3])
   opww[5] = parseFloat($pww[4])
   opww[6] = parseFloat($pww[5])
   opww[7] = parseFloat($pww[6])
   opww[8] = parseFloat($pww[7])
   opww[9] = parseFloat($pww[8])
   opww[10] = parseFloat($pww[9])
   opww[11] = parseFloat($pww[10])
   opww[12] = parseFloat($pww[11])

   // Dry day follows wet day
   opwd = new MakeArray(12)
   opwd[1] = parseFloat($pwd[0])
   opwd[2] = parseFloat($pwd[1])
   opwd[3] = parseFloat($pwd[2])
   opwd[4] = parseFloat($pwd[3])
   opwd[5] = parseFloat($pwd[4])
   opwd[6] = parseFloat($pwd[5])
   opwd[7] = parseFloat($pwd[6])
   opwd[8] = parseFloat($pwd[7])
   opwd[9] = parseFloat($pwd[8])
   opwd[10] = parseFloat($pwd[9])
   opwd[11] = parseFloat($pwd[10])
   opwd[12] = parseFloat($pwd[11])

   daymo = new MakeArray(12)
   daymo[1]=31; daymo[2]=28; daymo[3]=31; daymo[4]=30; daymo[5]=31; daymo[6]=30;
   daymo[7]=31; daymo[8]=31; daymo[9]=30; daymo[10]=31; daymo[11]=30; daymo[12]=31;

   function MakeArray(n) {
    this.length=n
    return this
   }
   
   function ResetFormValues() {
     document.mods.tx1.value = precision(otx1,2);
     document.mods.tx2.value = precision(otx2,2);
     document.mods.tx3.value = precision(otx3,2);
     document.mods.tx4.value = precision(otx4,2);
     document.mods.tx5.value = precision(otx5,2);
     document.mods.tx6.value = precision(otx6,2);
     document.mods.tx7.value = precision(otx7,2);
     document.mods.tx8.value = precision(otx8,2);
     document.mods.tx9.value = precision(otx9,2);
     document.mods.tx10.value = precision(otx10,2);
     document.mods.tx11.value = precision(otx11,2);
     document.mods.tx12.value = precision(otx12,2);

	   document.mods.tn1.value = precision(otn1,2);
	   document.mods.tn2.value = precision(otn2,2);
	   document.mods.tn3.value = precision(otn3,2);
	   document.mods.tn4.value = precision(otn4,2);
	   document.mods.tn5.value = precision(otn5,2);
	   document.mods.tn6.value = precision(otn6,2);
	   document.mods.tn7.value = precision(otn7,2);
	   document.mods.tn8.value = precision(otn8,2);
	   document.mods.tn9.value = precision(otn9,2);
	   document.mods.tn10.value = precision(otn10,2);
	   document.mods.tn11.value = precision(otn11,2);
	   document.mods.tn12.value = precision(otn12,2);

	   document.mods.nw1.value = precision(onw1,2);
	   document.mods.nw2.value = precision(onw2,2);
	   document.mods.nw3.value = precision(onw3,2);
	   document.mods.nw4.value = precision(onw4,2);
	   document.mods.nw5.value = precision(onw5,2);
	   document.mods.nw6.value = precision(onw6,2);
	   document.mods.nw7.value = precision(onw7,2);
	   document.mods.nw8.value = precision(onw8,2);
	   document.mods.nw9.value = precision(onw9,2);
	   document.mods.nw10.value = precision(onw10,2);
	   document.mods.nw11.value = precision(onw11,2);
	   document.mods.nw12.value = precision(onw12,2);
	   sum_nw();
	   
	   document.mods.pc1.value = precision(opc1,2);
	   document.mods.pc2.value = precision(opc2,2);
	   document.mods.pc3.value = precision(opc3,2);
	   document.mods.pc4.value = precision(opc4,2);
	   document.mods.pc5.value = precision(opc5,2);
	   document.mods.pc6.value = precision(opc6,2);
	   document.mods.pc7.value = precision(opc7,2);
	   document.mods.pc8.value = precision(opc8,2);
	   document.mods.pc9.value = precision(opc9,2);
	   document.mods.pc10.value = precision(opc10,2);
	   document.mods.pc11.value = precision(opc11,2);
	   document.mods.pc12.value = precision(opc12,2);
	   sum_pc();
	   
	   document.mods.pww1.value = opww[1];
	   document.mods.pww2.value = opww[2];
	   document.mods.pww3.value = opww[3];
	   document.mods.pww4.value = opww[4];
	   document.mods.pww5.value = opww[5];
	   document.mods.pww6.value = opww[6];
	   document.mods.pww7.value = opww[7];
	   document.mods.pww8.value = opww[8];
	   document.mods.pww9.value = opww[9];
	   document.mods.pww10.value = opww[10];
	   document.mods.pww11.value = opww[11];
	   document.mods.pww12.value = opww[12];
	   
	   document.mods.pwd1.value = opwd[1];
	   document.mods.pwd2.value = opwd[2];
	   document.mods.pwd3.value = opwd[3];
	   document.mods.pwd4.value = opwd[4];
	   document.mods.pwd5.value = opwd[5];
	   document.mods.pwd6.value = opwd[6];
	   document.mods.pwd7.value = opwd[7];
	   document.mods.pwd8.value = opwd[8];
	   document.mods.pwd9.value = opwd[9];
	   document.mods.pwd10.value = opwd[10];
	   document.mods.pwd11.value = opwd[11];
	   document.mods.pwd12.value = opwd[12];
	   
	   document.mods.txd.value = 0;
	   document.mods.tnd.value = 0;
	   document.mods.pcp.value = 0;
	   document.mods.nwp.value = 0;
   }
   
   function testframesize() {
	//alert(\"Average number of precipitation days each month == Increasing (decreasing) the mean monthly number of wet days while holding constant the monthly precipitation amount will effect a proportional decrease (increase) in the average amount of precipitation per wet day.  The climate generator includes a statistically representative, stochastic relationship between amount of precipitation in a day and precipitation intensity, which is based on historical precipitation data.  Thus the increase (decrease) in the number of wet days will cause a statistically representative decrease (increase) in peak and average event precipitation intensities.  See scientific references for more information.\")
   	wysize= (top.frames[2].document.body.clientHeight)
	wxsize= (top.frames[2].document.body.clientWidth)
   	alert(wxsize);
   	alert(wysize);
   }   

   // Consider flash rainfall
   function flashRainfall() {
     changeSD();         // calculate new sd values before exiting ELA 3/17/2008
   	 if (document.mods.flRai.value != 0){
   	   //change the standard deviation and mean to increase the intensity by flRai percent
	    	Z = document.mods.flRai.value
	    	x = Z/100	
	    	y = 0.776 * x         //This formula derived empirically
	   	    
	   	 document.mods.sd1.value = document.mods.sd1.value * (1+y)
	   	 document.mods.sd2.value = document.mods.sd2.value * (1+y)
	   	 document.mods.sd3.value = document.mods.sd3.value * (1+y)
	   	 document.mods.sd4.value = document.mods.sd4.value * (1+y)
	   	 document.mods.sd5.value = document.mods.sd5.value * (1+y)
	   	 document.mods.sd6.value = document.mods.sd6.value * (1+y)
	   	 document.mods.sd7.value = document.mods.sd7.value * (1+y)
	   	 document.mods.sd8.value = document.mods.sd8.value * (1+y)
	   	 document.mods.sd9.value = document.mods.sd9.value * (1+y)
	   	 document.mods.sd10.value = document.mods.sd10.value * (1+y)
	   	 document.mods.sd11.value = document.mods.sd11.value * (1+y)
	   	 document.mods.sd12.value = document.mods.sd12.value * (1+y)
	   	    	
	   	    
	   	 document.mods.pc1.value = document.mods.pc1.value * 0.97
	   	 document.mods.pc2.value = document.mods.pc2.value * 0.97
	   	 document.mods.pc3.value = document.mods.pc3.value * 0.97
	   	 document.mods.pc4.value = document.mods.pc4.value * 0.97
	   	 document.mods.pc5.value = document.mods.pc5.value * 0.97
	   	 document.mods.pc6.value = document.mods.pc6.value * 0.97
	   	 document.mods.pc7.value = document.mods.pc7.value * 0.97
	   	 document.mods.pc8.value = document.mods.pc8.value * 0.97
	   	 document.mods.pc9.value = document.mods.pc9.value * 0.97
	   	 document.mods.pc10.value = document.mods.pc10.value * 0.97
	   	 document.mods.pc11.value = document.mods.pc11.value * 0.97
	   	 document.mods.pc12.value = document.mods.pc12.value * 0.97
	   }
	    
	   // Rounding not until submiting the form for a better accurency
	   document.mods.sd1.value = precision(document.mods.sd1.value,2)
	   document.mods.sd2.value = precision(document.mods.sd2.value,2)
	   document.mods.sd3.value = precision(document.mods.sd3.value,2)
	   document.mods.sd4.value = precision(document.mods.sd4.value,2)
	   document.mods.sd5.value = precision(document.mods.sd5.value,2)
	   document.mods.sd6.value = precision(document.mods.sd6.value,2) 
	   document.mods.sd7.value = precision(document.mods.sd7.value,2)
	   document.mods.sd8.value = precision(document.mods.sd8.value,2)
	   document.mods.sd9.value = precision(document.mods.sd9.value,2)
	   document.mods.sd10.value = precision(document.mods.sd10.value,2)
	   document.mods.sd11.value = precision(document.mods.sd11.value,2)
	   document.mods.sd12.value = precision(document.mods.sd12.value,2)
   }

   // Test flash rainfall
   function testFlashRainfall() {
   	 if (isNumber(document.mods.flRai.value)) {
   	   if (document.mods.flRai.value < 0){
   	    	document.mods.flRai.value = 0
   		    alert('Rainfall Intensity change must be over 0%!')
       } else if (document.mods.flRai.value > 25) {
   		   document.mods.flRai.value = 0
   		   alert('Flash Rainfall must be under 25%!')
   	   }
   	 } else {
   	   document.mods.flRai.value = 0
   		 alert('Invalid entry for Flash Rainfall!')
	   }
   }

   // Increase Decrease all standard deviations by same % as the mean precipitation changes
   function changeSD() {
   
	    document.mods.sd1.value = osd1 * (document.mods.pc1.value/opc1)      
	    document.mods.sd2.value = osd2 * (document.mods.pc2.value/opc2)
	    document.mods.sd3.value = osd3 * (document.mods.pc3.value/opc3)
	    document.mods.sd4.value = osd4 * (document.mods.pc4.value/opc4)
	    document.mods.sd5.value = osd5 * (document.mods.pc5.value/opc5)
	    document.mods.sd6.value = osd6 * (document.mods.pc6.value/opc6)
	    document.mods.sd7.value = osd7 * (document.mods.pc7.value/opc7)
	    document.mods.sd8.value = osd8 * (document.mods.pc8.value/opc8)
	    document.mods.sd9.value = osd9 * (document.mods.pc9.value/opc9)
	    document.mods.sd10.value = osd10 * (document.mods.pc10.value/opc10)
	    document.mods.sd11.value = osd11 * (document.mods.pc11.value/opc11)
	    document.mods.sd12.value = osd12 * (document.mods.pc12.value/opc12)
   }
   	

   // Increase Decrease all numbers of wet days by %
   function nwpct() {
	    var ratio = 1+parseFloat(document.mods.nwp.value)*0.01
	    document.mods.nw1.value = precision(onw1 * ratio,2); mod_nw(1)
	    document.mods.nw2.value = precision(onw2 * ratio,2); mod_nw(2)
	    document.mods.nw3.value = precision(onw3 * ratio,2); mod_nw(3)
	    document.mods.nw4.value = precision(onw4 * ratio,2); mod_nw(4)
	    document.mods.nw5.value = precision(onw5 * ratio,2); mod_nw(5)
	    document.mods.nw6.value = precision(onw6 * ratio,2); mod_nw(6)
	    document.mods.nw7.value = precision(onw7 * ratio,2); mod_nw(7)
	    document.mods.nw8.value = precision(onw8 * ratio,2); mod_nw(8)
	    document.mods.nw9.value = precision(onw9 * ratio,2); mod_nw(9)
	    document.mods.nw10.value = precision(onw10 * ratio,2); mod_nw(10)
	    document.mods.nw11.value = precision(onw11 * ratio,2); mod_nw(11)
	    document.mods.nw12.value = precision(onw12 * ratio,2); mod_nw(12)
   }

   // Increase Decrease all mean precipitations by %
   function pcpct() {
	    var ratio = 1+parseFloat(document.mods.pcp.value)*0.01
	    document.mods.pc1.value = precision(opc1 * ratio,2)
	    document.mods.pc2.value = precision(opc2 * ratio,2)
	    document.mods.pc3.value = precision(opc3 * ratio,2)
	    document.mods.pc4.value = precision(opc4 * ratio,2)
	    document.mods.pc5.value = precision(opc5 * ratio,2)
	    document.mods.pc6.value = precision(opc6 * ratio,2)
	    document.mods.pc7.value = precision(opc7 * ratio,2)
	    document.mods.pc8.value = precision(opc8 * ratio,2)
	    document.mods.pc9.value = precision(opc9 * ratio,2)
	    document.mods.pc10.value = precision(opc10 * ratio,2)
	    document.mods.pc11.value = precision(opc11 * ratio,2)
	    document.mods.pc12.value = precision(opc12 * ratio,2)
	    sum_pc()
   }

   // Increase Decrease all minimum temperatures by degree
   function tndeg() {
	    var tndiff = parseFloat(document.mods.tnd.value)
	    document.mods.tn1.value = precision(otn1 + tndiff,2)
	    document.mods.tn2.value = precision(otn2 + tndiff,2)
	    document.mods.tn3.value = precision(otn3 + tndiff,2)
	    document.mods.tn4.value = precision(otn4 + tndiff,2)
	    document.mods.tn5.value = precision(otn5 + tndiff,2)
	    document.mods.tn6.value = precision(otn6 + tndiff,2)
	    document.mods.tn7.value = precision(otn7 + tndiff,2)
	    document.mods.tn8.value = precision(otn8 + tndiff,2)
	    document.mods.tn9.value = precision(otn9 + tndiff,2)
	    document.mods.tn10.value = precision(otn10 + tndiff,2)
	    document.mods.tn11.value = precision(otn11 + tndiff,2)
	    document.mods.tn12.value = precision(otn12 + tndiff,2)
   }

   // Increase Decrease all maximum temperatures by degree
   function txdeg() {
	    var txdiff = parseFloat(document.mods.txd.value)
	    document.mods.tx1.value = precision(otx1 + txdiff,2)
	    document.mods.tx2.value = precision(otx2 + txdiff,2)
	    document.mods.tx3.value = precision(otx3 + txdiff,2)
	    document.mods.tx4.value = precision(otx4 + txdiff,2)
	    document.mods.tx5.value = precision(otx5 + txdiff,2)
	    document.mods.tx6.value = precision(otx6 + txdiff,2)
	    document.mods.tx7.value = precision(otx7 + txdiff,2)
	    document.mods.tx8.value = precision(otx8 + txdiff,2)
	    document.mods.tx9.value = precision(otx9 + txdiff,2)
	    document.mods.tx10.value = precision(otx10 + txdiff,2)
	    document.mods.tx11.value = precision(otx11 + txdiff,2)
	    document.mods.tx12.value = precision(otx12 + txdiff,2)
   }

   // Increase Decrease wet days related to the annual rainfall
   function distribute_wet() {
	    var ratio = document.mods.nw.value / snw
	    document.mods.nw1.value = precision(onw1 * ratio,2); mod_nw(1)
	    document.mods.nw2.value = precision(onw2 * ratio,2); mod_nw(2)
	    document.mods.nw3.value = precision(onw3 * ratio,2); mod_nw(3)
	    document.mods.nw4.value = precision(onw4 * ratio,2); mod_nw(4)
	    document.mods.nw5.value = precision(onw5 * ratio,2); mod_nw(5)
	    document.mods.nw6.value = precision(onw6 * ratio,2); mod_nw(6)
	    document.mods.nw7.value = precision(onw7 * ratio,2); mod_nw(7)
	    document.mods.nw8.value = precision(onw8 * ratio,2); mod_nw(8)
	    document.mods.nw9.value = precision(onw9 * ratio,2); mod_nw(9)
	    document.mods.nw10.value = precision(onw10 * ratio,2); mod_nw(10)
	    document.mods.nw11.value = precision(onw11 * ratio,2); mod_nw(11)
	    document.mods.nw12.value = precision(onw12 * ratio,2); mod_nw(12)
	    document.mods.nwp.value=precision((document.mods.nw.value-snw)/snw*100,2)
	 }

   // Increase Decrease Mean monthly precipitations related to the annual precipitation
   function distribute_pcp() {
	    var ratio = document.mods.pc.value / spc
	    document.mods.pc1.value = precision(opc1 * ratio,2)
	    document.mods.pc2.value = precision(opc2 * ratio,2)
	    document.mods.pc3.value = precision(opc3 * ratio,2)
	    document.mods.pc4.value = precision(opc4 * ratio,2)
	    document.mods.pc5.value = precision(opc5 * ratio,2)
	    document.mods.pc6.value = precision(opc6 * ratio,2)
	    document.mods.pc7.value = precision(opc7 * ratio,2)
	    document.mods.pc8.value = precision(opc8 * ratio,2)
	    document.mods.pc9.value = precision(opc9 * ratio,2)
	    document.mods.pc10.value = precision(opc10 * ratio,2)
	    document.mods.pc11.value = precision(opc11 * ratio,2)
	    document.mods.pc12.value = precision(opc12 * ratio,2)
	    document.mods.pcp.value = precision((document.mods.pc.value-spc)/spc*100,2)
   }

   // Modify number of wet days (mothly)
   function mod_nw(i) {
	   // check valid number first
	   var shit = 'nw' + i;
	   var nwinput = document.getElementById(shit);
	   if (isNumber(nwinput.value)) {
	     if (nwinput.value > daymo[i]){
	       nwinput.value=daymo[i]}
	     else if (nwinput.value < 0) {
	       nwinput.value=0}
	   } else { 
	     nwinput.value=0 }

	   var pww = parseFloat(opww[i])						// if (pww < 0.001) pww = 
	   var pwd = parseFloat(opwd[i])
	   var ratio = pwd / pww                 			// pww can be zero ...
	   var pw = parseFloat(nwinput.value) / daymo[i]
	   pww = 1 / (1 - ratio + (ratio / pw))			// pw can be zero ...
	   pwd = pww * ratio
	   
	   shit = 'pww' + i;
	   var pwwinput = document.getElementById(shit);
	   pwwinput.value = precision(pww,2);
	   
	   shit = 'pwd' + i;
	   var pwdinput = document.getElementById(shit);
	   pwdinput.value = precision(pwd,2);
	   
	   sum_nw()
   }

   // Changes sum number of wet days
   function sum_nw() {
	   document.mods.nw.value=precision(
	       parseFloat(document.mods.nw1.value)+
	       parseFloat(document.mods.nw2.value)+
	       parseFloat(document.mods.nw3.value)+
	       parseFloat(document.mods.nw4.value)+
	       parseFloat(document.mods.nw5.value)+
	       parseFloat(document.mods.nw6.value)+
	       parseFloat(document.mods.nw7.value)+
	       parseFloat(document.mods.nw8.value)+
	       parseFloat(document.mods.nw9.value)+
	       parseFloat(document.mods.nw10.value)+
	       parseFloat(document.mods.nw11.value)+
	       parseFloat(document.mods.nw12.value),2)
   }

   // Modify mean minimum Temperatur (monthly)
   function mod_tmp(obj) {
	   def = 0;
	   min = -50;
	   max = 130;
	   var tmp_unit = ' deg F';
	   if (units == 'm') {
	     min = -200
	     max = 200;
	     pc_unit = ' deg C'
	   }

	   if (isNumber(obj.value)) {
	     if (obj.value < min){
		     alert('Temperature must be between ' + min + ' and ' + max + tmp_unit)
		     obj.value=min
	     }
	     else if (obj.value > max) {
		     alert('Temperature must be between ' + min + ' and ' + max + tmp_unit)
		     obj.value=max
	     }
	   } else {
		   alert('Invalid entry of ' + obj.value + ' for Temperature!')
		   obj.value=def
	   }
   }

   // Modify mean precipitation (monthly)
   function mod_pc(obj) {
  	 def = 0;
	   min = 0;
	   max = 39;
	   pc_unit = ' in';
	   if (units == 'm') {
	     max = 999;
	     pc_unit = ' mm'
	   }

	   if (isNumber(obj.value)) {
	     if (obj.value < min){
		     alert('Precipitation must be between ' + min + ' and ' + max + pc_unit)
		     obj.value=min
	     }
	     else if (obj.value > max) {
		     alert('Precipitation must be between ' + min + ' and ' + max + pc_unit)
		     obj.value=max
	     }
	   } else {
		   alert('Invalid entry for precipitation!')
		   obj.value=def
	   }
	   sum_pc()
   }
 
   // Changes sum mean precipitation
   function sum_pc() {
	   document.mods.pc.value=precision(
	       parseFloat(document.mods.pc1.value)+
	       parseFloat(document.mods.pc2.value)+
	       parseFloat(document.mods.pc3.value)+
	       parseFloat(document.mods.pc4.value)+
	       parseFloat(document.mods.pc5.value)+
	       parseFloat(document.mods.pc6.value)+
	       parseFloat(document.mods.pc7.value)+
	       parseFloat(document.mods.pc8.value)+
	       parseFloat(document.mods.pc9.value)+
	       parseFloat(document.mods.pc10.value)+
	       parseFloat(document.mods.pc11.value)+
	       parseFloat(document.mods.pc12.value),2)
   }

   // Update Latitude and Longitude (prism)
   function updateLL() {
	   document.mods.latitude.value=document.prism.latitude.value
	   document.mods.longitude.value=document.prism.longitude.value
   }

   function precision(floater,ndec) {
	   ndec=parseInt(ndec)
	   factor=Math.pow(10,ndec)
	   return Math.round(floater*factor)/factor
   }

  // Determine whether a suspected numeric input is a positive or negative number.
  function isNumber(inputVal) {
	  // Determine whether a suspected numeric input
	  // is a positive or negative number.
	  // Ref.: JavaScript Handbook. Danny Goodman. listing 15-4, p. 374.
	  // I don't like that this doesn't forgive starting or trailing whitespace ELA
	  oneDecimal = false                              // no dots yet
	  inputStr = '' + inputVal                        // force to string
	  for (var i = 0; i < inputStr.length; i++) {     // step through each char
	    var oneChar = inputStr.charAt(i)              // get one char out
	    if (i == 0 && oneChar == '-') {               // negative OK at char 0
	      continue
	    }
	    if (oneChar == '.' && !oneDecimal) {
	      oneDecimal = true
	      continue
	    }
	    if (oneChar < '0' || oneChar > '9') {
	      return false
	    }
	  }
	  return true
  }
  
   // Adjust temperature for elevation by lapse rate (from elevation input on top)
   function lapsetemp() {
	   if (document.mods.lapse.checked) {
	     var selev = $elev/3.28\n";

	 if ($units eq "m") { 
	   print " var lelev = parseFloat(document.mods.melev.value)
		 var maxlapse = -6.0 * ((selev-lelev)/1000)\n";
	 }
	 else {
	   print " var lelev = parseFloat(document.mods.ftelev.value)/3.28
		   var maxlapse = -6.0 * ((selev-lelev)/1000) * (9.0/5.0)\n";
	 }

	print "
	   document.mods.tx1.value = precision(otx1 - maxlapse,2)
	   document.mods.tx2.value = precision(otx2 - maxlapse,2)
	   document.mods.tx3.value = precision(otx3 - maxlapse,2)
	   document.mods.tx4.value = precision(otx4 - maxlapse,2)
	   document.mods.tx5.value = precision(otx5 - maxlapse,2)
	   document.mods.tx6.value = precision(otx6 - maxlapse,2)
	   document.mods.tx7.value = precision(otx7 - maxlapse,2)
	   document.mods.tx8.value = precision(otx8 - maxlapse,2)
	   document.mods.tx9.value = precision(otx9 - maxlapse,2)
	   document.mods.tx10.value = precision(otx10 - maxlapse,2)
	   document.mods.tx11.value = precision(otx11 - maxlapse,2)
	   document.mods.tx12.value = precision(otx12 - maxlapse,2)\n";

	 if ($units eq "m") { 
	   print " var lelev = parseFloat(document.mods.melev.value)
		   var minlapse = -5.0 * ((selev-lelev)/1000)\n";
	 }
	 else {
	   print " var lelev = parseFloat(document.mods.ftelev.value)/3.28
		   var minlapse = -5.0 * ((selev-lelev)/1000) * (9.0/5.0)\n";
	 }

	print"
	   document.mods.tn1.value = precision(otn1 - minlapse,2)
	   document.mods.tn2.value = precision(otn2 - minlapse,2)
	   document.mods.tn3.value = precision(otn3 - minlapse,2)
	   document.mods.tn4.value = precision(otn4 - minlapse,2)
	   document.mods.tn5.value = precision(otn5 - minlapse,2)
	   document.mods.tn6.value = precision(otn6 - minlapse,2)
	   document.mods.tn7.value = precision(otn7 - minlapse,2)
	   document.mods.tn8.value = precision(otn8 - minlapse,2)
	   document.mods.tn9.value = precision(otn9 - minlapse,2)
	   document.mods.tn10.value = precision(otn10 - minlapse,2)
	   document.mods.tn11.value = precision(otn11 - minlapse,2)
	   document.mods.tn12.value = precision(otn12 - minlapse,2)

	   }
	   else {
	   document.mods.tx1.value = precision(otx1,2)
	   document.mods.tx2.value = precision(otx2,2)
	   document.mods.tx3.value = precision(otx3,2)
	   document.mods.tx4.value = precision(otx4,2)
	   document.mods.tx5.value = precision(otx5,2)
	   document.mods.tx6.value = precision(otx6,2)
	   document.mods.tx7.value = precision(otx7,2)
	   document.mods.tx8.value = precision(otx8,2)
	   document.mods.tx9.value = precision(otx9,2)
	   document.mods.tx10.value = precision(otx10,2)
	   document.mods.tx11.value = precision(otx11,2)
	   document.mods.tx12.value = precision(otx12,2)
	   document.mods.tn1.value = precision(otn1,2)
	   document.mods.tn2.value = precision(otn2,2)
	   document.mods.tn3.value = precision(otn3,2)
	   document.mods.tn4.value = precision(otn4,2)
	   document.mods.tn5.value = precision(otn5,2)
	   document.mods.tn6.value = precision(otn6,2)
	   document.mods.tn7.value = precision(otn7,2)
	   document.mods.tn8.value = precision(otn8,2)
	   document.mods.tn9.value = precision(otn9,2)
	   document.mods.tn10.value = precision(otn10,2)
	   document.mods.tn11.value = precision(otn11,2)
	   document.mods.tn12.value = precision(otn12,2)
	   } 
   }

   var browserName=navigator.appName;
   var browserVer=parseInt(navigator.appVersion);

   // Opens lat-long calculator
   function latlong_calc(unit) {
	   var url='http://$wepphost/fswepp/milatcon.html';
	   if (units == 'm') {url='http://$wepphost/fswepp/kmlatcon.html'}
	   var title='lat-long_calculator';
	  var params='toolbar=no,location=no,status=yes,directories=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=350';
	  popupwindow=window.open(url,title,params);
	//  if (browserName == 'Netscape' && browserVer >= 3) popupwindow.creator=window;
	       if (popupwindow.creator=window) popupwindow.creator=window;
	//  alert(obj);
	//  valid_al();
   }

   // Opens degrees calculator
   function dms2dec_calc(unit) {
	  var url='http://$wepphost/fswepp/dms2dec.html';
	  var title='dms2dec_calculator';
	  var params='toolbar=no,location=no,status=yes,directories=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=350';
	  popupwindow=window.open(url,title,params);
	//  if (browserName == 'Netscape' && browserVer >= 3) popupwindow.creator=window;
	       if (popupwindow.creator=window) popupwindow.creator=window;
	//  alert(obj);
	//  valid_al();
   }

   // -->


   function noenter() {
	   return !(window.event && window.event.keyCode == 13); 
   }

</SCRIPT>
";

   print '</HEAD>
       <body bgcolor=white link="#1603F3" vlink="#160A8C">
       <font face="Arial, Geneva, Helvetica">';
             
   print "<center>
       <table width=80%>
       <tr>
       <td width=45%>
       <CENTER>
       <H3>Climate parameters for<br>$climate_name</H3>
     ";


     print '<b>';
     printf "%.2f", abs($lat);
     print '<sup>o</sup>';

     if ($plat > 0) {
#         print 'N ';
         $lathemisphere = "N";
     } 
     else {
#         print 'S ';
         $lathemisphere = "S";
     }
     print $lathemisphere,' ';

     printf "%.2f", abs($lon);
     print '<sup>o</sup>';
     
     if ($plon > 0) {
#         print 'E';
         $longhemisphere = "E";
     }
     else {
#         print 'W';
         $longhemisphere = "W";
     }
     print $longhemisphere;
     if ($units eq 'm') {
       printf "<p>%4d m elevation",$elev / 3.28;
     }
     else {
       print "<p>$elev feet elevation";
     }
     print '</b>
   <p>
   <td width=10%>&nbsp;</td>
   <td width=45%>
    <center>
     <H3>Modify climate parameters below
     <a href="http://',$wepphost,'/fswepp/modpardoc.html" target="docs"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a></H3>
     <form name="prism" action="/cgi-bin/fswepp/prism/prisform.pl" method=post>
     <input type=text name=platitude size=7 value=';
     printf "%.2f", abs($plat);     print ' onChange="updateLL()"> <b><sup>o</sup>';
     print $lathemisphere;
     print '</b>
     <input type=text name=plongitude size=7 value=';
     printf "%.2f", abs($plon);     print ' onChange="updateLL()"> <b><sup>o</sup>';
#     printf "%.2f", $plon;     print ' onChange="updateLL()"> <b><sup>o</sup>';
     print $longhemisphere;
     print '</b>
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input type=image src="http://',$wepphost,'/fswepp/images/prism.gif" value=PRISM align=middle>
<!-- DEH 23 Jul 2002 -->
     <br>(<a href="Javascript:latlong_calc(\'',$units,'\')">lat-long calculator</a>)
     (<a href="Javascript:dms2dec_calc(\'',$units,'\')">degrees calculator</a>)
<!-- -->
     <input type=hidden name=elev value=';
     if ($units eq 'm') {printf "%4d", $pelev / 3.28}
     else {print $pelev}
     print '>
     <input type=hidden name=latitude value=',abs($lat),'>
     <input type=hidden name=lathem value=',$lathemisphere,'>
     <input type=hidden name=longitude value=',abs($lon),'>
     <input type=hidden name=longhem value=',$longhemisphere,'>
     <input type=hidden name=units value=',$units,'>
     <input type=hidden name=CL value=',$CL,'>
     <input type=hidden name=climate_name value="',$climate_name,'">';
        for $i (0..11) {
        print "\n     ";
        print '<input type=hidden name="pc',$i+1,'" value="',$mean_p[$i],'">'
        }
     print '
     <input type=hidden name="pc" value="',$annual_precip,'">
     <input type=hidden name="workingdir" value="',$workingdir,'">
     </form>

     <form name="mods" action="/cgi-bin/fswepp/rc/createpar.pl" method=post>
     ';
     if ($units eq 'm') {
       print "\n     ";
       print '<input type=text name=melev size=7 onChange="lapsetemp()" value=';
       printf " %4d",$pelev;
       print '> <b>m elevation</b>';
     }
     else {
       print '<input type=text name=ftelev size=7 onChange="lapsetemp()" value=';
       printf " %4d",$pelev;
       print '> <b>ft elevation</b>';
     }
print '  
</tr>
</table>
<p>
    <input type="hidden" name="units" value="',$units,'">
    <input type="hidden" name="climateFile" value="',$climateFile,'">
    <input type="hidden" name="latitude" value="';
     printf "%.2f", abs($plat);  print '">
    <input type="hidden" name="longitude"  value="';
     printf "%.2f", abs($plon);  print '">
    <input type="hidden" name="workingdir" value="',$workingdir,'">
    <input type="hidden" name="lathemisphere" value="',$lathemisphere,'">
    <input type="hidden" name="longhemisphere" value="',$longhemisphere,'">
    <table border=1 bgcolor="white">
<tr>';
     if ($units eq 'm') {
      print '
        <th bgcolor=85D2a2>Mean<br>Maximum<br>Temperature<br>(<sup>o</sup>C)
        <th bgcolor=85D2b2>Mean<br>Minimum<br>Temperature<br>(<sup>o</sup>C)
        <th bgcolor=85D2c2>Mean<br>Precipitation<br>(mm)
        <th bgcolor=85D2D2>Number<br>of wet days
        <th bgcolor=85D2f2>Month
        <th bgcolor=85D2a2>Mean<br>Maximum<br>Temperature<br>(<sup>o</sup>C)
        <th bgcolor=85D2b2>Mean<br>Minimum<br>Temperature<br>(<sup>o</sup>C)
        <th bgcolor=85D2c2>';
        if ($prism eq "") {
            print 'Mean';
        }
        else {
            print '<font color=red>P</font>
                   <font color=orange>R</font>
                   <font color=gold>I</font>
                   <font color=green>S</font>
                   <font color=blue>M</font>';
        }
      print '<br>Precipitation<br>(mm)
        <th bgcolor=85D2D2>Number<br>of wet days
        <!-- <th bgcolor=gold>P(W:W)
        <th bgcolor=gold>P(W:D) -->';
      } 
      else {
       print '<th bgcolor=85D2a2><a href="#" onClick="popup1(\'help1\', \'Average of the daily maximum temperature\');return false;" name="help1" id="help1"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Mean<br>Maximum<br>Temperature<br>(<sup>o</sup>F)
        <th bgcolor=85D2b2><a href="#" onClick="popup1(\'help2\', \'Average of the daily minimum temperature\');return false;" name="help2" id="help2"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Mean<br>Minimum<br>Temperature<br>(<sup>o</sup>F)
        <th bgcolor=85D2c2><a href="#" onClick="popup1(\'help3\', \'Average monthly precipitation\');return false;" name="help3" id="help3"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Mean<br>Precipitation<br>(in)
        <th bgcolor=85D2d2><a href="#" onClick="popup1(\'help4\', \'Average number of precipitation days each month\');return false;" name="help4" id="help4"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Number<br>of wet days
        <th bgcolor=85D2f2>Month
        <th bgcolor=85D2a2><a href="#" onClick="popupLeft(\'help5\', \'Average of the daily maximum temperature\');return false;" name="help5" id="help5"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Mean<br>Maximum<br>Temperature<br>(<sup>o</sup>F)
        <th bgcolor=85D2b2><a href="#" onClick="popupLeft(\'help6\', \'Average of the daily minimum temperature\');return false;" name="help6" id="help6"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Mean<br>Minimum<br>Temperature<br>(<sup>o</sup>F)
        <th bgcolor=85D2c2>';
        if ($prism eq "") {
            print '<a href="#" onClick="popupLeft(\'help7\', \'Average monthly precipitation == Increasing (decreasing) the mean monthly precipitation while holding constant the number of wet (precipitation) days will effect a proportional increase (decrease) in the average amount of precipitation per wet day.  The climate generator includes a statistically representative, stochastic relationship between amount of precipitation in a day and precipitation intensity, which is based on historical precipitation data.  Thus the increase (decrease) in the precipitation amount will cause a statistically representative increase (decrease) in peak and average event precipitation intensities.  See scientific references for more information.\');return false;" name="help7" id="help7"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Mean';
        }
        else {
            print '<font color=red>P</font>
                   <font color=orange>R</font>
                   <font color=gold>I</font>
                   <font color=green>S</font>
                   <font color=blue>M</font>';
        }
      print '<br>Precipitation<br>(in)
        <th bgcolor=85D2d2><a href="#" onClick="popupLeft(\'help8\', \'Average number of precipitation days each month == Increasing (decreasing) the mean monthly number of wet days while holding constant the monthly precipitation amount will effect a proportional decrease (increase) in the average amount of precipitation per wet day.  The climate generator includes a statistically representative, stochastic relationship between amount of precipitation in a day and precipitation intensity, which is based on historical precipitation data.  Thus the increase (decrease) in the number of wet days will cause a statistically representative decrease (increase) in peak and average event precipitation intensities.  See scientific references for more information.\');return false;" name="help8" id="help8"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a> Number<br>of wet days';
      }
for $i (0..11) {print '
<tr><td align=right> ',$tmax[$i],'
    <td align=right> ',$tmin[$i],'
    <td align=right> ',$mean_p[$i],'
    <td align=right> ',$num_wet[$i],'
    <th bgcolor=85D2f2>',$month_name[$i],'
    <td align=right><input type=text name="tx',$i+1,'" size=8 value="',$tmax[$i],'" onChange="mod_tmp(document.mods.tx',$i+1,')">
    <td align=right><input type=text name="tn',$i+1,'" size=8 value="',$tmin[$i],'" onChange="mod_tmp(document.mods.tn',$i+1,')">
    <td align=right><input type=text name="pc',$i+1,'" size=8 value="',$ppcp[$i],'" onChange="mod_pc(document.mods.pc',$i+1,')">
    <input type=hidden name="sd',$i+1,'" size=8 value="',$sd_p[$i],'">
    <td align=right><input type=text name="nw',$i+1,'" id="nw',$i+1,'" size=8 value="',$num_wet[$i],'" onChange="mod_nw(',$i+1,')">
    <input type=hidden name="pww',$i+1,'" id="pww',$i+1,'" size=8 value="',$pww[$i],'">
    <input type=hidden name="pwd',$i+1,'" id="pwd',$i+1,'" size=8 value="',$pwd[$i],'">
'
}
print '
<tr><td align=right><br>
   <td align=right><br>
   <td align=right> ',$annual_precip,'
   <td align=right> ',$annual_wet_days,'
   <th bgcolor=85D2f2>Annual
   <td align=right><br>
   <td align=right><br>
   <td align=right><input type=text name="pc" size=8 value="',$ppcp,'" onChange="distribute_pcp()">
   <td align=right><input type=text name="nw" size=8 value="',$annual_wet_days,'" onChange="distribute_wet()">

<tr><th colspan=5 align=right>Change entire column (enter 0 to reset) &nbsp;&gt;&nbsp;&gt;
<th align=right>+/-<input type=text size=5 name="txd" value="0" onChange="txdeg()" onkeypress="return noenter(); txdeg();"><sup>o</sup>
<th align=right>+/-<input type=text size=5 name="tnd" value="0" onChange="tndeg()" onkeypress="return noenter()"><sup>o</sup>
<th align=right>+/-<input type=text size=5 name="pcp" value="0" onChange="pcpct()" onkeypress="return noenter()">%
<th align=right>+/-<input type=text size=5 name="nwp" value="0" onChange="nwpct()" onkeypress="return noenter()">%
</table>
<p>
<a href="#" onClick="popup1(\'help10\', \'Analyses of historical data worldwide have shown that the share of precipitation falling in heavy rainfall events has been rising over the last century.  For example, P.Y. Groisman, from the U.S. National Climatic Data Center analyzed trends in precipitation over the contiguous United States for the time period of 1910-1999.  He found that the fraction of total annual precipitation falling in heavy events (defined as the greater than 95th percentile rain event) has increased by 1.7% per decade on average across the U.S. (Soil and Water Conservation Society, 2003).  Groisman also reported that the associated increases in very heavy (99th percentile) and extreme (99.9th percentile) rainfall were even greater.  In the current application (WEPPCAT) we refer to this as ”Rainfall Intensitification”.   Rainfall Intensitification is accomplished here by altering the standard deviation of the distributions of daily precipitation used by the climate generator.  The amount of flashing is based on user input for the 95th percentile (heavy) precipitation, but higher percentile levels (very heavy and extreme) will increase slightly more that the user designated value, in approximate accordance with the results of Groisman stated above. The user may note that the mean annual precipitation that occurs with flashing may change slightly (up to 2 percent in some cases) even when change in annual precipitation is not designated.  This small effect can be accounted for in an approximate way by normalizing the endpoint variable (e.g., soil loss or sediment yield) to the total rainfall, if desired.\');return false;" name="help10" id="help10"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a>
Intensify Rainfall &nbsp; <a href="#" onClick="popup1(\'help11\', \'Enter the percentage increase in the precipitation falling in the greater than 95th percentile (heavy) rainfall events.\');return false;" name="help11" id="help11"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a>
<input type=text name="flRai" value="0" onChange="testFlashRainfall()" onkeypress="return noenter()"> &nbsp; (0.01 - 25%)
<br>
<input type=checkbox name=lapse onClick="lapsetemp()"> Adjust temperature for elevation by lapse rate 
<br>
<a href="#" onClick="popup1(\'help9\', \'Modeling studies by Pruski and Nearing (2002) (see scientific references) indicated that a combination of change in average annual (or monthly) precipitation amount and number of wet days gave the best relationship between rainfall erosivity and monthly and annual precipitation totals based on historical data. For example, under this change scenario, in order to represent a 10% increase in annual precipitation (evenly by month over the year) one would increase “Mean Precipitation” by 10% and number of wet days by 5%. </br></br> However, analysis of historical precipitation data by Groisman et al. (2005) and Karl and Knight (1998) has indicated that in mid-latitudes of the world there has been a widespread increase in the amount of precipitation occurring in heavy precipitation events over the past 50 to 100 years. See help on ”Intensify Rainfall”.</br></br>\');return false;" name="help9" id="help9"><img src="/fswepp/images/quest.gif" width="15" height="15" border="0"></a>
&nbsp;What is the most realistic way to change precipitation?
<br>
<br>

<input type=submit value="Use these values" onClick="flashRainfall()">
<input type=button value="Refresh values" onClick="ResetFormValues()">

</form>

<div ID="helpdiv1" STYLE="position:absolute; visibility:hidden; background-color:#FFFFCC; padding: 4px; z-index:51; text-align: left; font:14px/1 sans-serif; border:2px solid #000000;"></div>

';

print '<BR>
</center>
';
print "
  <font size=-1>
   Click on question marks for help<br>
   <b>Modpar</b> version $version
   (a part of <b>Rock:Clime</b>)<br>                
   U.S.D.A. Forest Service, Rocky Mountain Research Station
   </font>
 </BODY>
</HTML>
";

# ************************

sub ReadParse {

# ReadParse -- from cgi-lib.pl (Steve Brenner) from Eric Herrmann's
# "Teach Yourself CGI Programming With PERL in a Week" p. 131

# Reads GET or POST data, converts it to unescaped text, and puts
# one key=value in each member of the list "@in"
# Also creates key/value pairs in %in, using '\0' to separate multiple
# selections

# If a variable-glob parameter...

  local (*in) = @_ if @_;
  local ($i, $loc, $key, $val);

  if ($ENV{'REQUEST_METHOD'} eq "GET") {
    $in = $ENV{'QUERY_STRING'};
  } elsif ($ENV{'REQUEST_METHOD'} eq "POST") {
    read(STDIN,$in,$ENV{'CONTENT_LENGTH'});
  }

  @in = split(/&/,$in);

  foreach $i (0 .. $#in) {
    # Convert pluses to spaces
    $in[$i] =~ s/\+/ /g;

    # Split into key and value
    ($key, $val) = split(/=/,$in[$i],2);  # splits on the first =

    # Convert %XX from hex numbers to alphanumeric
    $key =~ s/%(..)/pack("c",hex($1))/ge;
    $val =~ s/%(..)/pack("c",hex($1))/ge;

    # Associative key and value
    $in{$key} .= "\0" if (defined($in{$key}));  # \0 is the multiple separator
    $in{$key} .= $val;
  }
  return 1;
 }

#---------------------------

sub printdate {

    @months=qw(January February March April May June July August September October November December);
    @days=qw(Sunday Monday Tuesday Wednesday Thursday Friday Saturday);
    $ampm[0] = "am";
    $ampm[1] = "pm";

    $ampmi = 0;
    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=gmtime;
    if ($hour == 12) {$ampmi = 1}
    if ($hour > 12) {$ampmi = 1; $hour = $hour - 12}
    printf "%0.2d:%0.2d ", $hour, $min;
    print $ampm[$ampmi],"  ",$days[$wday]," ",$months[$mon];
    print " ",$mday,", ",$year+1900, " GMT/UTC/Zulu<br>\n";

    $ampmi = 0;
    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime;
    if ($hour == 12) {$ampmi = 1}
    if ($hour > 12) {$ampmi = 1; $hour = $hour - 12}
    printf "%0.2d:%0.2d ", $hour, $min;
    print $ampm[$ampmi],"  ",$days[$wday]," ",$months[$mon];
    print " ",$mday,", ",$year+1900, " Pacific Time";
}

