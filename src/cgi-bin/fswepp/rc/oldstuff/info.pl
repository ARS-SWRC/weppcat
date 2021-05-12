#! /usr/bin/perl
#!/fsapps/fssys/bin/perl

# showpersonal.pl  --    

# 05/16/2000 DEH added " print "Content-type: text/html\n\n"; "
# 05/16/2000 DEH moved cookies above platform

  $version='2007.01.31';

#$action
#$units
#$comefrom
#$me

# <form name="sc" ACTION="http://',$wepphost,'/cgi-bin/fswepp/rc/pclimate.cli" method="post"> [Download Describe Modify]
# <form method="post" name="RockClim" action="../rc/rockclim.pl"> [Retreat]

#  FS WEPP, USDA Forest Service, Rocky Mountain Research Station, Soil & Water Engineering
#  Science by Bill Elliot et alia                      Code by David Hall & Dayna Scheele
#  19 October 1999

    $arg0 = $ARGV[0];  chomp $arg0;
    $arg1 = $ARGV[1];  chomp $arg1;
    $arg2 = $ARGV[2];  chomp $arg2;
    $arg3 = $ARGV[3];  chomp $arg3;

    $action = $arg0;
    $units = $arg1;
    $comefrom = $arg2;
    $me = $arg3;

    $cookie = $ENV{'HTTP_COOKIE'};
    $sep = index ($cookie,"=");
    $me = "";
    if ($sep > -1) {$me = substr($cookie,$sep+1,1)}

    if ($me ne "") {
       $me = lc(substr($me,0,1));
       $me =~ tr/a-z/ /c;
    }
    if ($me eq " ") {$me = ""}

  $platform="pc";
  if (-e "../platform") {
    open Platform, "<../platform";
      $platform=lc(<Platform>);
      chomp $platform;
    close Platform;
  }

  if ($platform eq 'pc') {
    if (-e 'd:/fswepp/working') {$custCli = 'd:/fswepp/working/'}
    elsif (-e 'c:/fswepp/working') {$custCli = 'c:/fswepp/working/'}
    else {$custCli = '../working/'}
  }
  else {
    $user_ID=$ENV{'REMOTE_ADDR'};
    $user_really=$ENV{'HTTP_X_FORWARDED_FOR'};          # DEH 11/14/2002
    $user_ID=$user_really if ($user_really ne '');      # DEH 11/14/2002
    $user_ID =~ tr/./_/;
    $user_ID = $user_ID . $me;
#    $custCli = '../working/' . $user_ID . $me;
    $custCli = '../working/' . $user_ID;
  }

  $wepphost="typhoon.tucson.ars.ag.gov";
  if (-e "../wepphost") {
    open Host, "<../wepphost";
    $wepphost = <Host>;
    chomp $wepphost;
    close Host;
  }

### get personal climates, if any

    $num_cli = 0;
    @fileNames = glob($custCli . '*.PAR');
    for $f (@fileNames) {
 if ($debug) {print "Opening $f<br>\n";}
      open(M,"<$f") || die;              # cli file
      $station = <M>;
      close (M);
      $climate_file[$num_cli] = substr($f, 0, length($f)-4);
      $clim_name = "*" . substr($station, index($station, ":")+2, 40);
      $clim_name =~ s/^\s*(.*?)\s*$/$1/;
      $climate_name[$num_cli] = $clim_name;
#      $climate_year[$num_cli] = substr($year,66,5) * 1;
#      chomp $climate_year[$num_cli];
      $num_cli += 1;
    }

print "Content-type: text/html\n\n";
print '
<html>
<head>
<title>Personal Climate Stations</title>

<SCRIPT Language="JavaScript">
<!--

  function isNumber(inputVal) {
  // general purpose function to see whether a suspected numeric input
  // is a positive or negative number.
  // Ref.: JavaScript Handbook. Danny Goodman.
  oneDecimal = false                              // no dots yet
  inputStr = "" + inputVal                        // force to string
  for (var i = 0; i < inputStr.length; i++) {     // step through each char
    var oneChar = inputStr.charAt(i)              // get one char out
    if (i == 0 && oneChar == "-") {               // negative OK at char 0
      continue
    }
    if (oneChar == "." && !oneDecimal) {
      oneDecimal = true
      continue
    }
    if (oneChar < "0" || oneChar > "9") {
      return false
    }
  }
  return true
  }

  function Submit(filename) {
    document.sc.station.value=filename;
    document.sc.submit()
  }

  function displayPar() {  
   var filename = 
     document.sc.station.options[document.sc.station.selectedIndex].value
   var station = 
     document.sc.station.options[document.sc.station.selectedIndex].text
   alert(\'Watch for this feature soon: \' + station + \' \' + filename)
  }

   // -->
  </SCRIPT>
 </head>
 <body bgcolor="white">
  <font face="Arial, Geneva, Helvetica">
   <CENTER>
    <H2>Personal Climate Stations</H2>
';

#      if ($action eq '-download') {
        print '<form name="sc" ACTION="http://',$wepphost,'/cgi-bin/fswepp/rc/pclimate.cli" method="post">',"\n";
#      }
#      else {
#        print '<form name="sc" ACTION="http://',$wepphost,'/cgi-bin/fswepp/rc/copypar.pl" method="post">',"\n";
#      }

  if ($debug) {print "User_ID: $user_ID<p>$glo";};
#    print "action:  $arg0  units: $arg1  comefrom: $arg2  me: $arg3"; 

    if ($num_cli == 0) {print "No personal climates exist<p><hr>\n"}
    else {
      print ' <SELECT NAME="station" SIZE="15">
      ';
      print '         <OPTION VALUE="';
      print $climate_file[0];
      print '" selected> '. $climate_name[0] . "\n";
      for $ii (1..$num_cli-1) {
        print '        <OPTION VALUE="';
        print $climate_file[$ii];
        print '"> '. $climate_name[$ii] . "\n";
      }
     }   # bracket for if no personal climates exist
      print '
      </SELECT>
      <p>
      <input type="hidden" name="state" value="',$custCli,'">
      <input type="hidden" name="comefrom" value="',$comefrom,'">
      <input type="hidden" name="units" value="',$units,'">
      <input type="hidden" name="me" value="',$me,'">
      <input type="hidden" name="startyear" value="1">
      ';

      if ($action eq '-download') {
       print '
    Number of years of climate <input type="text" name="simyears" value="30" size=4> (approx 26K per year)
    <p>
    <input type="submit" name="submitbutton" value="DOWNLOAD CLIMATE">
    <input type="hidden" name="action" value="',$action,'">'
      }
#      else {
#        print '<input type="submit" name="submitbutton" value="ADD TO PERSONAL CLIMATES"> '
#      }
  print '
    <input type="submit" name="submitbutton" value="DESCRIBE CLIMATE">
    <input type="submit" name="submitbutton" value="MODIFY CLIMATE">
   </form>
   <form method="post" name="RockClim" action="../rc/rockclim.pl">
    <input type="hidden" name="units" value="',$units,'">
    <input type="hidden" name="action" value="',$action,'">
    <input type="hidden" name="comefrom" value="',$comefrom,'">
    <input type="submit" name="submit" value="Retreat">
   </form>
  </center>
';

#  <p><a href="javascript:displayPar()">Display climate .PAR file</a>

print "
  <font size=-1>
   '<b>Showpersonal</b>' version $version
   (a part of <b>Rock:Clime</b>)<br>
   U.S.D.A. Forest Service, Rocky Mountain Research Station
   </font>
 </body>
</html>";
