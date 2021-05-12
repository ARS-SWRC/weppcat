#! /usr/bin/perl
#!/fsapps/fssys/bin/perl

# showclimates.pl  --  

# 19 February 2003 DEH added "USDA FS Rock:Clime" and specified font
# 19 July 2002 DEH fixed $title"; error - caught in some browsers...

#  usage:
#    <form ACTION="http://host/cgi-bin/fswepp/rc/showclimates.pl" method="post">
#  parameters:
#    state
#    units		'm' or 'ft'
#    action		'-download' or '-server'
#    comefrom		'http://host/cgi-bin/fswepp/wr/wepproad.pl' ...
#    me
#  reads:
#    ../wepphost
#    $state_file
#  calls:
#    <form name="sc" ACTION="http://host/cgi-bin/fswepp/rc/climate.cli" method="post">
#    <form name="sc" ACTION="http://host/cgi-bin/fswepp/rc/copypar.pl" method="post">

#  FSWEPP, USDA Forest Service, Rocky Mountain Research Station, Soil & Water Engineering
#  Science by Bill Elliot et alia                                      Code by David Hall
#  2005.12.29 DEH Change from <H1> to <H3>
#  2003.02.19
#  17 April 2002 DEH    prepare to allow .PAR file d/l (calls showpar.pl)
#  05 March 2001 DEH 	add 'me' to retreat button
#		 DEH	finish off HTML body for successful page
#  19 October 1999

    $wepphost="typhoon.tucson.ars.ag.gov";
    if (-e "../wepphost") {
      open Host, "<../wepphost";
      $wepphost = <Host>;
      chomp $wepphost;
      close Host;
    }

    &ReadParse(*parameters);

    $state=$parameters{'state'};       
    $units=$parameters{'units'};
    $action=$parameters{'action'};
    $comefrom=$parameters{'comefrom'};
    $me=$parameters{'me'};

    $unixserver="false";

################################
# Go to Show Personal Climates #
################################

    if ($state eq "personal") {
      if ($unixserver eq "false") {
         exec "perl ../rc/showpersonal.pl $action $units $comefrom $me"
      }
      else {
         exec "../rc/showpersonal.pl $action $units $comefrom $me"
      }
    }  

#########################################
# Verify state entry AL..WY or whatever #
#########################################

    if ($state eq "") {$state = "id"};
                 # store valid states (plus) in array @states and
    @states = ('al','az','ar','ca','co','ct','de','fl','ga','id',
               'il','in','ia','ks','ky','la','me','md','ma','mi',
               'mn','ms','mo','mt','ne','nv','nh','nj','nm','ny',
               'nc','nd','oh','ok','or','pa','ri','sc','sd','tn',
               'tx','ut','vt','va','wa','wv','wi','wy','dc','ak',
               'hi','pr','pi','vi','if','nonus');
    for (@states) {$states{$_}++;}
 
####################################################
#  build state climate filename and get state name #
####################################################

    if ($states{$state}) {       
      $state_file = $state . "/" . $state . "_climates";
		# open specified state climate and serve 
      print "Content-type: text/html\n\n";
      open CLIM, "<" . $state_file;  # || die "can't open file!";
        $state_name = <CLIM>;
        chomp $state_name;
      close CLIM;

#############
# HTML page #
#############

print "<html>
  <head>
   <title>Rock:Clime -- $state_name Climate Stations</title>";
print <<'EOD';
      
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
   alert('Look for this feature shortly: ' + station)
   document.displaypar.PARfilename.value = filename
//   document.displaypar.submit()
  }

// -->
  </SCRIPT>
 </HEAD>
 <body bgcolor="white">
  <font face="arial, sans serif">
   <CENTER>
EOD
      print "<H3>USDA FS Rock:Clime<br>$state_name Climate Stations</H3>\n";
#    print "$me<br>\n";
      if ($action eq '-download') {
        print '<form name="sc" ACTION="http://',$wepphost,'/cgi-bin/fswepp/rc/climate.cli" method="post">',"\n";
      }
      else {
        print '<form name="sc" ACTION="http://',$wepphost,'/cgi-bin/fswepp/rc/copypar.pl" method="post">',"\n";
      }


print "State: ", $state,"<br>\n";
print "User: " , $me,"<br>\n";
print "Units: ", $units,"<br>\n";
print "Action: ", $action,"<br>\n";
print "Comefrom: ", $comefrom,"<br>\n";
print "State File: ", $state_file,"<br>\n";


#print "User agent: ', $ENV{'HTTP_USER_AGENT'};



#     print "User agent: ', $ENV{'HTTP_USER_AGENT'};
##    $browser =  $ENV{'HTTP_USER_AGENT'};
##    $unixserver = "true";
##    if ($browser =~ /MSIE/   {
         
##}

      print '<input type="hidden" name="comefrom" value="',$comefrom,'">
<input type="hidden" name="me" value="',$me,'">
<input type="hidden" name="units" value="',$units,'">
<input type="hidden" name="startyear" value="1"><br>
';
          if ($unixserver eq 'true') {
           open M, "<" . $state_file;
           $st=<M>;    # California
#          print $st,"\n";
           $st=<M>;    # <input type="hidden" name="state" value="ca">
           print $st,"\n";
           $_=<M>;     # <select name="station">
           print '<input type="hidden" name="station">',"\n";
           while (<M>) {
             chomp;
              if ($_ ne '</select>') {
               ($optval, $file, $stat) = split /\"/;
               $stat = substr($stat,1);
               chomp $stat;
               print '<a href="/fswepp/need_js.html" onClick="Submit(';
               print "'",$file,"'); return false",'">',$stat,"</a><br>\n";
              }
           }
           close M;
          }        #  $unixserver true
          else {
            open M, "<" . $state_file;
            $st=<M>;
              while (<M>) {
              print;
              } 
            close M;
          }      # $unixserver false


      print "<P>\n";

# <a href="none" onClick="Submit('ca049866'); return false">YREKA CA</a>

      if ($action eq '-download') {
      print 'Number of years of climate <input type="text" name="simyears" value="30" size=4> (approx 26K per year)
  <p>
  <input type="submit" name="submitbutton" value="DOWNLOAD CLIMATE">
  <input type="hidden" name="action" value="',$action,'">'
      }
      else {
        print '<input type="submit" name="submitbutton" value="ADD TO PERSONAL CLIMATES"> '
      }
  print '
  <input type="submit" name="submitbutton" value="DESCRIBE CLIMATE">
  <input type="submit" name="submitbutton" value="MODIFY CLIMATE">
</form>

<!--
<form name="displaypar" method="post" action="showpar.pl">
  <input type="text" name="parfilename">
</form>
-->

<form method="post" name="RockClim" action="../rc/rockclim.pl">
<input type="hidden" name="units" value="',$units,'">
<input type="hidden" name="action" value="',$action,'">
<input type="hidden" name="me" value="',$me,'">
<input type="hidden" name="comefrom" value="',$comefrom,'">
<input type="submit" name="submit" value="Retreat">
</form> 
';

#<a href="JavaScript:window.history.go(-1)">
#<img src="http://',$wepphost,'/fswepp/images/retreat.gif"
#  alt="Return to input screen" border="0" align=center></A>
#				DEH 03/05/2001
 print '</CENTER>';
#PAR   <p><a href="javascript:displayPar()">Display climate .PAR file</a> 
#PAR -- for power WEPP users only!
#PAR <p>
 print '
</body>
</html>
';

      print "\n";
 }
 else {
      print "Content-type: text/html\n\n";
      print "<HTML>\n";
      print "<HEAD>\n";
      print "<TITLE>Something is wrong!</TITLE>\n";
      print "</HEAD>\n";
      print '<BODY background="http://',$wepphost,'/fswepp/images/rcdraft.gif">',"\n";
      print "<CENTER><H2>Excuse me!</H2></CENTER>\n";
      print "I don't like the values of some of the parameters I received.<p>\n";
      print "state:   ", $state,"<br>\n";
      print "</body></html>\n";
 }

# ------------------------------------------------------------

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
    $in[$i] =~ s/\+/ /g;    # Convert pluses to spaces
    ($key, $val) = split(/=/,$in[$i],2);   # Split into key and value
    $key =~ s/%(..)/pack("c",hex($1))/ge;  # Convert %XX from hex numbers to alphanumeric
    $val =~ s/%(..)/pack("c",hex($1))/ge;
    $in{$key} .= "\0" if (defined($in{$key}));  # \0 is the multiple separator
    $in{$key} .= $val;
  }
  return 1;
 }

