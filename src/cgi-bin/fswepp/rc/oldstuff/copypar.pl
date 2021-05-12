#! /usr/bin/perl
#! /fsapps/fssys/bin/perl

#  FS WEPP, USDA Forest Service, 
#  Rocky Mountain Research Station, Soil & Water Engineering
#  Science by Bill Elliot et alia         Code by David Hall
#  19 October 1999
#  07/19/2000 DEH	fix for unix platform multiple personal files
#  03/03/2001 DEH	add '_' to $user_ID

# copypar.pl
# place copy of user's newly-selected personal climate into the working
#   directory using a name based on the client IP address

#  parameters
#    'state'
#    'station'
#    'comefrom'
#    'submitbutton'	=~ /describe/    /modify/    /personal/
#    'me'
#    'units'
#  reads
#    ../wepphost
#    ../platform
#  calls
#   /describe/
#     exec "perl ../rc/descpar.pl $CL $units $iam"
#     exec "../rc/descpar.pl $CL $units $iam"
#   /modify/
#     exec "perl ../rc/modpar.pl $CL $units $state $comefrom"
#     exec "../rc/modpar.pl $CL $units $state $comefrom"
#   /personal/
#     `copy $climate_file $dest`;
#     exec "perl ../rc/rockclim.pl -server -u$units $comefrom"
#     `cp $climate_file $dest`;
#     exec "../rc/rockclim.pl -server $units $comefrom"

    &ReadParse(*parameters);
    $state=$parameters{'state'};         
    $station=$parameters{'station'};       
    $comefrom=$parameters{'comefrom'};
    $submitbutton=lc($parameters{'submitbutton'});
    $units=$parameters{'units'};
    $me=$parameters{'me'};

    if ($me ne "") {
       $me = lc(substr($me,0,1));
       $me =~ tr/a-z/ /c;
    }
    if ($me eq " ") {$me = ""}

    $wepphost="typhoon.tucson.ars.ag.gov";
    if (-e "../wepphost") {
      open Host, "<../wepphost";
      $wepphost = <Host>;
      chomp $wepphost;
      close Host;
    }

    $platform="pc";
    if (-e "../platform") {
      open Platform, "<../platform";
      $platform=lc(<Platform>);
      chomp $platform;
      close Platform;
    }

# get user_ID and remove weirdness
# get user PID for temp files

# get user_ID and remove weirdness
# get user PID for temp files

     if ($platform eq 'pc') {
       $user_ID = 'climate';
     }
     else {
       $user_ID = $ENV{REMOTE_ADDR};
       $user_really=$ENV{'HTTP_X_FORWARDED_FOR'};      # DEH 11/14/2002
       $user_ID=$user_really if ($user_really ne '');  # DEH 11/14/2002
       $user_ID =~ tr/./_/;
       $user_ID = $user_ID . $me . '_';			# DEH 03/05/2001
       if ($user_ID eq "") {$user_ID = "custom"}
     }

#  verify filename entry no .. ~ leading / etc.
#  verify state entry AL..WY or whatever
    if ($state eq "") {$state = "id"}
    if ($station eq "") {$station = "id108080"}

   # store valid states (plus) in array @states and
   # construct a hash (Wall, Programming Perl, p. 537)
   @states = ('al','az','ar','ca','co','ct','de','fl','ga','id',
              'il','in','ia','ks','ky','la','me','md','ma','mi',
              'mn','ms','mo','mt','ne','nv','nh','nj','nm','ny',
              'nc','nd','oh','ok','or','pa','ri','sc','sd','tn',
              'tx','ut','vt','va','wa','wv','wi','wy','dc','ak',
              'hi','pr','pi','vi','if','nonus');
   for (@states) {$states{$_}++;}
   # $_ contains a keyword (state) if $states{$_} is non-zero.

###################################

   if ($states{$state} && $station =~ /^[\w.]*$/) {

#  build state_ID/filename + ".PAR"

     if ($platform eq "pc") {
       if (-e 'd:/fswepp/working') {$working = 'd:\\fswepp\\working'}
       elsif (-e 'c:/fswepp/working') {$working = 'c:\\fswepp\\working'}
       else {$working = '..\\working'}
       $climate_file = $state . '\\' . $station . '.PAR';
     }
     else {
       $working = '../working';
       $climate_file = $state . '/' . $station . '.PAR';
     }

# open specified .PAR file, verify content, and close

     open CLIM, "<" . $climate_file;  # || die "can't open parameter file!";
       $title = <CLIM>;
       $station_text = substr $title,0,40;
     close (CLIM);

####################################################################
#
#  MODIFY
#
####################################################################

    if ($submitbutton =~ /modify/) {

      $CL = $state . "/" . $station;
      $iam = "http://" . $wepphost . "/cgi-bin/fswepp/rc/copypar.pl";
      if ($platform eq "pc") {
         exec "perl ../rc/modpar.pl $CL $units $state $comefrom"
      }
      else {
         exec "../rc/modpar.pl $CL $units $state $comefrom"
      }
    }

####################################################################
#
#  DESCRIBE
#
####################################################################

     if ($submitbutton =~ /describe/) {
       $CL = $state . "/" . $station;
       $iam = "http://" . $wepphost . "/cgi-bin/fswepp/rc/climate.cli";
       if ($platform eq "pc") {
         exec "perl ../rc/descpar.pl $CL $units $iam"
       }
       else {
         exec "../rc/descpar.pl $CL $units $iam"
       }
     }

####################################################################
#
#  ADD TO PERSONAL CLIMATES
#
####################################################################

     if ($submitbutton =~ /personal/) {
       for $letter ('a' .. 'e') {
         $nextletter = $letter;  
         if ($platform eq 'pc') {
           $filename = "$working\\" . $user_ID . $letter . '.PAR';
         }
         else {
           $filename = "$working/" . $user_ID . $letter . '.PAR';
         }
         if (! (-e $filename)) {last}
       }
       if ($platform eq 'pc') {
         $dest = "$working\\" . $user_ID . $nextletter . '.PAR';
        `copy $climate_file $dest`;
         exec "perl ../rc/rockclim.pl -server -u$units $comefrom"
       }
       else {
         $dest = "$working/" . $user_ID . $nextletter . '.PAR';
        `cp $climate_file $dest`;
         exec "../rc/rockclim.pl -server -u$units $comefrom"
       }
     }  # /personal/

     if ($comefrom eq "") {
       print '<a href="JavaScript:window.history.go(-1)">'
     }
     else {
       print "<a href=$comefrom>"
     }
     print '<img src="http://',$wepphost,'/fswepp/images/retreat.gif"
       alt="Retreat" border="0" align=center></a>';
     print "</body></html>\n";
   }   # end state and station OK

# ------------------------------

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
