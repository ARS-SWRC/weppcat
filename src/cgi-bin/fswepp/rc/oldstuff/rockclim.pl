#! /usr/bin/perl
#!/fsapps/fssys/bin/perl

#  usage:
#    ROCK:CLIM climate    action                units  comefrom
#             (climatea) (-server | -download) (m|ft) ('http://localhost/wr.pl')
#  parameters:
#    $units=$parameters{'units'};
#    $me=$parameters{'me'};
#  reads
#    ../platform
#    ../wepphost
#    c:/fswepp/working
#    d:/fswepp/working
#  environment variable
#    $ENV{'REMOTE_ADDR'}
#    $ENV{'HTTPD_X_FORWARDED_FOR'}
#    $ENV{'HTTP_COOKIE'}
#  calls:
#    <form method="post" action="../rc/manageclimates.pl">
#    <form ACTION="http://',$wepphost,'/cgi-bin/fswepp/rc/showclimates.pl" method="post">
#    <form method="post" action="',$comefrom,'">

#  FSWEPP, USDA Forest Service, Rocky Mountain Research Station, Soil & Water Engineering
#  Science by Bill Elliot et alia                            Code by David Hall

# 2005.08.22 DEH add AOL connection warning
#  26 Feb 2004 DEH Replace graphic "Return" button on stand-alone w/ "button"
#                  add rockclim.pl version tag in html header
#  19 Feb 2003 DEH add diagnostic user_address, user_host, user_really report
#  23 Jul 2002 DEH removed nonfunctional "modify" button
#  20 Apr 2001 DEH changed station parse from index(":") to 0..40
#  02 Mar 2001 DEH Move user_ID check up a'la whitepine
#	       DEH remove date stamp script for HTML page
#	 	   This version uses *glob*
#  19 Oct 1999 DEH

#  $debug=1;

  $version = '2004.04.26';
# $version = '2003.02.19';
# $version = '2002.07.23';
# $version = '2001.04.20';

####################
# Get input values #
####################

    $arg0 = $ARGV[0];  chomp $arg0;
    $arg1 = $ARGV[1];  chomp $arg1;
    $arg2 = $ARGV[2];  chomp $arg2;
    $arg3 = $ARGV[3];  chomp $arg3;

    $cookie = $ENV{'HTTP_COOKIE'};      # DEH 11/28/2000 moved up
    $sep = index ($cookie,"=");
    $me = "";
    if ($sep > -1) {$me = substr($cookie,$sep+1,1)}

    if ($me ne "") {
       $me = lc(substr($me,0,1));
       $me =~ tr/a-z/ /c;
    }
    if ($me eq " ") {$me = ""}          # DEH 11/28/2000 moved up

    if ($arg0 . $arg1 . $arg2 . $argv3 eq "") {
      &ReadParse(*parameters);
      $goback=$parameters{'goback'};
      $units=$parameters{'units'};
      $action=$parameters{'action'};
      $comefrom=$parameters{'comefrom'};
      $me=$parameters{'me'};
      if ($action eq "") {$action="-download"};
    }
    else {
      if ($arg0 eq "-um" || $arg0 eq "-uft") {$units=substr($arg0,2)}
      if ($arg1 eq "-um" || $arg1 eq "-uft") {$units=substr($arg1,2)}
      if ($arg2 eq "-um" || $arg2 eq "-uft") {$units=substr($arg2,2)}
      if ($arg0 eq "-download" || $arg0 eq "-server") {$action=$arg0}
      if ($arg1 eq "-download" || $arg1 eq "-server") {$action=$arg1}
      $comefrom = "";

      if (lc($arg3) =~ /http/) {$comefrom = $arg3}
      if (lc($arg2) =~ /http/) {$comefrom = $arg2}
      if (lc($arg1) =~ /http/) {$comefrom = $arg1}
      if (lc($arg0) =~ /http/) {$comefrom = $arg0}
    }

###############################################
# Get platform, working, userID, and wepphost #
###############################################

  $platform="pc";
  if (-e "../platform") {
    open Platform, "<../platform";
      $platform=lc(<Platform>);
      chomp $platform;
    close Platform;
  }

 if ($platform eq 'pc') {
    if (-e 'c:/fswepp/working') {$custCli = 'c:/fswepp/working/'}
    else {$custCli = '../working/'}
 }
 else {
    $user_ID=$ENV{'REMOTE_ADDR'};
    $remote_address=$user_ID;				# DEH 02/19/2003
    $remote_host=$ENV{'REMOTE_HOST'};			# DEH 02/19/2003
    $user_really=$ENV{'HTTP_X_FORWARDED_FOR'};          # DEH 11/14/2002
    $user_ID=$user_really if ($user_really ne '');      # DEH 11/14/2002
    $user_ID =~ tr/./_/;
    $user_ID = $user_ID . $me;            	# DEH 03/05/2001
    $custCli = '../working/' . $user_ID . '_';	# DEH 03/05/2001
 }

  $wepphost="typhoon.tucson.ars.ag.gov";
  if (-e "../wepphost") {
    open Host, "<../wepphost";
    $wepphost = <Host>;
    chomp $wepphost;
    close Host;
  }

if ($action ne "-server") {$action="-download"}

#############
# HTML page #
#############

print "Content-type: text/html\n\n";
print '<html>
 <head>
  <title>Rock:Clime</title>
  <!-- rockclim.pl version ',$version,' -->
 </head>
 <BODY bgcolor="white" link="#1603F3" vlink="160A8C">
 <font face="Arial, Geneva, Helvetica">
';

if ($action ne '-download') {

### get custom climates, if any ###

    $num_cli = 0;
    @fileNames = glob($custCli . '*.PAR');
    for $f (@fileNames) {
 if ($debug) {print "Opening $f<br>\n";}
      open(M,"<$f") || die;              # cli file
      $station = <M>;
      close (M);
      $climate_file[$num_cli] = substr($f, 0, length($f)-4);
#      $clim_name = "*" . substr($station, index($station, ":")+2, 40);
      $clim_name = "*" . substr($station, 0, 40);
      $clim_name =~ s/^\s*(.*?)\s*$/$1/;
      $climate_name[$num_cli] = $clim_name;
      $num_cli += 1;
    }

}   # end if ($action ne '-download')

  if ($action eq '-download') {
    print '  <a href="http://',$wepphost,'/fswepp/">
    <IMG src="http://',$wepphost,'/fswepp/images/fsweppic2.jpg"
    align="left" height=75 width=75 alt="Return to FSWEPP menu" border=0></a>
       <A HREF="http://',$wepphost,'/fswepp/docs/rcimg.html" target="_documentation">
       <IMG src="http://',$wepphost,'/fswepp/images/ipage.gif"
        align="right" alt="Read the documentation" border=0></a>';
  }
  else {
     if ($comefrom =~ /road/) {
            print '
            <a href="JavaScript:document.retreat.submit()">
            <img src="http://',$wepphost,'/fswepp/images/road4.gif"
            alt="Return to WEPP:Road" border=2
            align=left width=50 height=50
            onMouseOver="window.status=\'Return to WEPP:Road input screen\'; return true"
            onMouseOut="window.status=\' \'; return true"
            onClick="retreat.submit()">
            ';
     }
     elsif ($comefrom =~ /ermit/) {
            print '
            <a href="JavaScript:document.retreat.submit()">
            <img src="http://',$wepphost,'/fswepp/images/ermit.gif"
            alt="Return to ERMiT" border=2
            align=left width=50 height=50
            onMouseOver="window.status=\'Return to ERMiT input screen\'; return true"
            onMouseOut="window.status=\' \'; return true"
            onClick="retreat.submit()">
            ';
     }
     elsif ($comefrom =~ /fume/) {
            print '
            <a href="JavaScript:document.retreat.submit()">
            <img src="http://',$wepphost,'/fswepp/images/fume.jpg"
            alt="Return to WEPP FuME" border=2
            align=left width=50 height=50
            onMouseOver="window.status=\'Return to WEPP FuME input screen\'; return true"
            onMouseOut="window.status=\' \'; return true"
            onClick="retreat.submit()">
            ';
     }
     else { print '
            <a href="JavaScript:document.retreat.submit()">
            <img src="http://',$wepphost,'/fswepp/images/disturb.gif"
            alt="Return to Disturbed WEPP" border=2
            align=left width=50 height=50
            onMouseOver="window.status=\'Return to Disturbed WEPP input screen\'; return true"
            onMouseOut="window.status=\' \'; return true"
            onClick="retreat.submit()">
            ';
     }
     print '
     <A HREF="http://',$wepphost,'/fswepp/docs/rcwrimg.html">
     <IMG src="http://',$wepphost,'/fswepp/images/ipage.gif"
     align="right" alt="Read the documentation" border=0
     onMouseOver="window.status=\'Read the documentation\'; return true"
     onMouseOut="window.status=\' \'; return true"></a>
     ';
  }

if ($debug) {
print "arg0: '$arg0'<br>
       arg1: '$arg1'<br>
       arg2: '$arg2'<br>
       arg3: '$arg3'<br>";
print "units: '$units' \n";
print "action: '$action'<br>\n";
}

print "units: '$units' \n";
print "action: '$action'<br>\n";
print "comefrom: '$comefrom' \n";
print "me: '$me'<br>\n";

  print '<CENTER>
  <H2>Rock:Clime<BR>
  Rocky Mountain Research Station<br> Climate Generator</H2>
  <hr>
  <p>
  ';
  print "Return climate file to $goback\n" if ($goback ne '');

  $glo = $custCli . '*.PAR';
  if ($debug) {print "User_ID: $user_ID<p>$glo";};

  if ($action ne '-download') {
    print "<h3>Personal Climates";
    if ($me ne '') {print " for personality '$me'"}
    print " <sup>1</sup></h3><p>\n";
    if ($num_cli == 0) {print "No personal climates exist<p><hr>\n"}
    else {
      print '
      <form method="post" action="../rc/manageclimates.pl">
      <SELECT NAME="Climate" SIZE="10">
      <OPTION VALUE="';
      print $climate_file[0];
      print '" selected> '. $climate_name[0] . "\n";
      for $ii (1..$num_cli-1) {
        print '        <OPTION VALUE="';
        print $climate_file[$ii];
        print '"> '. $climate_name[$ii] . "\n";
      }

      print '      </SELECT>
      <p>
      <input type="hidden" name="units" value="',$units,'">
      <input type="hidden" name="comefrom" value="',$comefrom,'">
      <input type="submit" name="manage" value="Describe">
      <input type="submit" name="manage" value="Remove">
<!--      <input type="submit" name="manage" value="Modify"> -->
      </form>
      <hr>';
    }
    print '<b>To add a climate station to your personal climates list...</b>';
  }
  else {
    print '<b>To generate and download to your computer a CLIGEN-format climate file
    for use in WEPP...</b>';
  }
print '
<h3>Select a region below</h3>
<p>
<form ACTION="http://',$wepphost,'/cgi-bin/fswepp/rc/showclimates.pl" method="post">
';

#print "I am $me";

print '
<Select name="state" size=5>';
  if ($action ne "-server") {print '<option value="personal">Personal</option>'}
print <<'theEnd';
  <option value="al" selected>Alabama</option>
  <option value="ak">Alaska</option>
  <option value="az">Arizona</option> 
  <option value="ar">Arkansas</option> 
  <option value="ca">California</option>
  <option value="co">Colorado</option>
  <option value="ct">Connecticut</option> 
  <option value="de">Delaware</option>
  <option value="fl">Florida</option>
  <option value="ga">Georgia</option>
  <option value="hi">Hawaii</option>
  <option value="id">Idaho</option>
  <option value="il">Illinois</option>
  <option value="in">Indiana</option>
  <option value="ia">Iowa</option>
  <option value="ks">Kansas</option>
  <option value="ky">Kentucky</option>
  <option value="la">Louisiana</option>
  <option value="me">Maine</option>
  <option value="md">Maryland</option>
  <option value="ma">Massachusetts</option>
  <option value="mi">Michigan</option>
  <option value="mn">Minnesota</option>
  <option value="ms">Mississippi</option>  
  <option value="mo">Missouri</option>
  <option value="mt">Montana</option>
  <option value="ne">Nebraska</option>
  <option value="nv">Nevada</option>
  <option value="nh">New Hampshire</option>
  <option value="nj">New Jersey</option>
  <option value="nm">New Mexico</option>
  <option value="ny">New York</option>
  <option value="nc">North Carolina</option>
  <option value="nd">North Dakota</option>
  <option value="oh">Ohio</option>
  <option value="ok">Oklahoma</option>
  <option value="or">Oregon</option>
  <option value="pi">Pacific Islands</option>
  <option value="pa">Pennsylvania</option>
  <option value="pr">Puerto Rico</option>
  <option value="ri">Rhode Island</option>
  <option value="sc">South Carolina</option>
  <option value="sd">South Dakota</option>
  <option value="tn">Tennessee</option>
  <option value="tx">Texas</option>
  <option value="ut">Utah</option>
  <option value="vt">Vermont</option>
  <option value="vi">Virgin Islands</option>
  <option value="va">Virginia</option>
  <option value="wa">Washington</option>
  <option value="dc">Washington, D.C.</option> 
  <option value="wv">West Virginia</option>
  <option value="wi">Wisconsin</option>
  <option value="wy">Wyoming</option>
  <option value=''>====</option>
  <option value="nonus">International</option>
</select>
<P>
<input type="submit" value="SHOW ME THE CLIMATES"> 
theEnd
print '
<input type="hidden" name="me" value="',$me,'">
<input type="hidden" name="comefrom" value="',$comefrom,'">
<input type="hidden" name="units" value="',$units,'">
<input type="hidden" name="action" value="',$action,'">
</form>
';
if ($comefrom eq "") {

  print '
  <form name="null">
   <input type="button" value="Retreat" onClick="JavaScript:location=\'/fswepp/\'">
  </form>
<!--  <a href="JavaScript:location=\'/fswepp/\'">
  <img src="/fswepp/images/retreat.gif"
  alt="Return to input screen" border="0" align=center></A>
-->
';
}
else {
  print '
<form name="retreat" method="post" action="',$comefrom,'">
<input type="hidden" name="units" value="',$units,'">
<input type="hidden" name="me" value="',$me,'">
<input type="submit" value="Return to input screen">
</form>
';
}
print '</CENTER>
 <P>
  <hr>
   <font size=-1>
  <sup>1</sup> Note: Custom climates are unreliable for AOL users. We are seeking a solution.
               If you have an alternative connection to the Internet, we recommend using it.
   </font>
  <hr>
   <table width=100% border=0>
    <tr>
     <td>
      <font face="Arial, Geneva, Helvetica" size=-2>
       Rock:Clime version ', $version,'<br>
       USDA Forest Service Rocky Mountain Research Station<br>
       1221 South Main Street, Moscow, ID 83843<br>',
      "$remote_host &ndash; $remote_address ($user_really)",'
     </td>                                                                      
     <td>
        <a href="http://',$wepphost,'/fswepp/comments.html" 
         onClick="return confirm(\'You need to be connected to the Internet to e-mail comments. Continue?\')">
        <img src="http://',$wepphost,'/fswepp/images/epaemail.gif" align="right" border=0></a>
    </td>
   </tr>
  </table>
 </body>
</html>
';

#-------------------------------

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

#   read text
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

