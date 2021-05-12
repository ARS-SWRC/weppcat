#! /usr/bin/perl
#! /fsapps/fssys/bin/perl

# createpar.pl

# 2004.02.03 DEH Comment out errant print statement of station name
# 04/19/2001 DEH changed history headers only
# 03/05/2001 DEH	added '_' to $user_ID
# 07/19/2000 DEH	added user_ID code; worked on working directory spec
# 05/16/2000 DEH changed letter a..e to v..z

$platform="pc";
if (-e "../platform") {
  open Platform, "<../platform";
  $platform=lc(<Platform>);
  chomp $platform;
  close Platform;
}

# get user_ID and remove weirdness    # DEH 07/19/00
# get user PID for temp files

# DEH 03/05/2001
#  $cookie = $ENV{'HTTP_COOKIE'};
#  $sep = index ($cookie,"=");
#  $me = "";
#  if ($sep > -1) {$me = substr($cookie,$sep+1,1)}

#  if ($me ne "") {
#    $me = lc(substr($me,0,1));
#    $me =~ tr/a-z/ /c;
#  }
#  if ($me eq " ") {$me = ""}
# DEH 03/05/2001

&ReadParse(*parameters);

$useaf=$parameters{'useaf'};
$ftelev=$parameters{'ftelev'};
$melev=$parameters{'melev'};
$units=$parameters{'units'};
$newlat=$parameters{'latitude'};
$newlong=$parameters{'longitude'};
#    $state=$parameters{'state'};
#    $station=$parameters{'station'};
$climateFile=$parameters{'climateFile'};
    
$lathemisphere=$parameters{'lathemisphere'};
$longhemisphere=$parameters{'longhemisphere'};

for $i (1..12) {
  $mean_p[$i-1]=$parameters{"pc$i"};
  $sd_p[$i-1]=$parameters{"sd$i"};
  $tmn[$i-1]=$parameters{"tn$i"};
  $tmx[$i-1]=$parameters{"tx$i"};
  $num_wet[$i-1]=$parameters{"nw$i"};
  $pww[$i-1]=$parameters{"pww$i"};
  $pwd[$i-1]=$parameters{"pwd$i"};
}
$comefrom=$parameters{'comefrom'};
$workingdir = $parameters{'workingdir'};
$newheader=$parameters{'newname'};
$me=$parameters{'me'};		# DEH 03/05/2001
if ($me ne "") {			# DEH 03/05/2001
  $me = lc(substr($me,0,1));
  $me =~ tr/a-z/ /c;
}
if ($me eq " ") {$me = ""}

$user_ID = $ENV{REMOTE_ADDR};
$user_really=$ENV{'HTTP_X_FORWARDED_FOR'};          # DEH 11/14/2002
$user_ID=$user_really if ($user_really ne '');      # DEH 11/14/2002
$user_ID =~ tr/./_/;
     #$user_ID = $user_ID . $me . '_';	# DEH 03/05/2001
$user_ID = $user_ID . '_';	# DEH 03/05/2001
if ($user_ID eq "") {$user_ID = "custom"}

#      print "Content-type: text/html\n\n";
#     print "Header:	$newheader
#Lat:		$newlat
#Long:		$newlong
#Source:		$climateFile
#Units:		$units\n";

@num_days = (31,28,31,30,31,30,31,31,30,31,30,31);

# DEH 06/05/2000     $platform = 'pc';

	# Defining location for PAR file
if ($platform eq "pc") {
  $user_ID = 'modify';
  if (-e 'd:/fswepp/working') {$working = 'd:\\fswepp\\working'}
  elsif (-e 'c:/fswepp/working') {$working = 'c:\\fswepp\\working'}
  else {$working = '..\\working'}
  for $letter ('v' .. 'z') {
    $nextletter = $letter;
    $outfile = "$working\\" . $user_ID . '.PAR';
    if (! (-e $outfile)) {last}
  }
}
else {
  $working = '../working'      ;	# DEH 07/19/00
#         $climateFile = $state . '/' . $station . '.PAR';
#         $outfile = "../working/" . $user_ID . ".PAR";		# DEH 06/05/2000
  for $letter ('v' .. 'z') {
    $nextletter = $letter;
          # $outfile = "$working/" . $user_ID . $letter . '.PAR';
    $outfile = "$working/" . $user_ID . '.PAR';
    if (! (-e $outfile)) {last}
  }
}

#      $infilename = $climateFile;
if ($lathemisphere eq "S") {$newlat *=  -1}
if ($longhemisphere eq "W") {$newlong *= -1}
#
#    Return proper units
#
if ($units eq 'm') {
  $new_elev = $melev * 3.28;
}
else {
  $new_elev = $ftelev;
}

#     open DEBUG, '>../working/test';
#     print DEBUG $outfile;
#     close DEBUG;

open INPAR, "<$climateFile";
open NEWPAR, ">$outfile";

#================
$station_line = <INPAR>;
# print "$station_line\n";				# DEH 2004.02.03
$old_station_name = substr($station_line,1,40);
# $new_station_name = '! ' . $old_station_name;
$new_station_name =  $newheader . ' +';
$old_station_no = substr($station_line,43,6);

$lat_line = <INPAR>;    # ' LATT=  68.87 LONG=-166.12 YEARS= 27. TYPE= 1';
$elev_line = <INPAR>;   # ' ELEVATION =   50. TP5 =  .91 TP6= 1.28';
$mean_p_line = <INPAR>; # ' MEAN P    .06   .05   .05   .06   .05   .10   .16   .18   .13   .08   .07   .05';
$sdp_line = <INPAR>;    # ' S DEV P   .08   .07   .05   .06   .13   .13   .24   .25   .17   .09   .12   .07';
$skp_line = <INPAR>;    # ' SKEW  P  3.61  4.12  1.99  3.12  9.85  2.69  2.68  2.92  2.20  2.70  4.78  3.93';
$pww_line = <INPAR>;    # ' P(W/W)    .49   .47   .40   .51   .47   .49   .56   .66   .68   .66   .57   .47';
$pwd_line = <INPAR>;    # ' P(W/D)    .14   .11   .11   .14   .13   .13   .21   .26   .23   .24   .19   .12';
$tmx_line = <INPAR>;
$tmn_line = <INPAR>;
# for $ii (0..11) {$mean_p[$ii]=substr ($mean_p_line,8+$ii*6,6)};
# for $ii (0..11) {$pww[$ii]=substr ($pww_line,8+$ii*6,6)};
# for $ii (0..11) {$pwd[$ii]=substr ($pwd_line,8+$ii*6,6)};
# for $ii (0..11) {$tmx[$ii]=substr ($tmx_line,8+$ii*6,6)};
# for $ii (0..11) {$tmn[$ii]=substr ($tmn_line,8+$ii*6,6)};

$yrsstuff=substr($lat_line,27);
$tpstuff=substr($elev_line,19);

#================
#    Need to convert mean p to mean p for a wet day and
#         number of wet days to P(W/W) and P(W/D)
#
for $i (0..11) {
  if ($units eq 'm') {
    $mean_p[$i] /= 25.4;  # mm to in
    $tmx[$i] = $tmx[$i]*(9/5) + 32; # deg C to F
    $tmn[$i] = $tmn[$i]*(9/5) + 32; # deg C to F
  }
       # average rain/snow fall on a wet day 
  if ($num_wet[$i]==0) {$mean_p[$i] = 0}                              # <<<<<<<<<<<<<<<<<<
  else                 {$mean_p[$i] /= $num_wet[$i]}
}
for $i (0..11) {
          # probability of a wet day = num wet days / num days
  $pwet[$i] = $num_wet[$i] / $num_days[$i];
          # probability of a wet day following a wet day
          #   $pwetwet = 1 + $pwetdry * (1 - (1/$pwet))
          #   use this number if it is less than about 0.8 (else...?)
          #   "Instructions to modify a CLIGEN statistics file" Elliot 1998
          # where do we get P(W/D) ($pwetdry) ?
          # what if $pwet ~= 0?
}
$fmt_st  = ' %-40.40s%2.2s%6.6s';
$fmt_ll  = ' LATT=%7.2f LONG=%7.2f %18.18s';
$fmt_el  = ' ELEVATION =%7.1f%20.20s';
$fmt_mp  = ' MEAN P %6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f';
$fmt_sdp = ' S DEV P%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f';
$fmt_pww = ' P(W/W) %6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f';
$fmt_pwd = ' P(W/D) %6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f';
$fmt_tmx = ' TMAX AV%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f';
$fmt_tmn = ' TMIN AV%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f%6.2f';

printf  NEWPAR $fmt_st, $new_station_name,'99',$old_station_no;
print   NEWPAR "\n";
printf  NEWPAR $fmt_ll, $newlat,$newlong,$yrsstuff;
print   NEWPAR  "\n";
printf  NEWPAR $fmt_el, $new_elev,$tpstuff;
print   NEWPAR  "\n";
printf  NEWPAR  $fmt_mp, $mean_p[0],$mean_p[1],$mean_p[2],$mean_p[3],$mean_p[4],$mean_p[5],$mean_p[6],$mean_p[7],$mean_p[8],$mean_p[9],$mean_p[10],$mean_p[11];
print   NEWPAR  "\n";
printf  NEWPAR  $fmt_sdp, $sd_p[0],$sd_p[1],$sd_p[2],$sd_p[3],$sd_p[4],$sd_p[5],$sd_p[6],$sd_p[7],$sd_p[8],$sd_p[9],$sd_p[10],$sd_p[11];
print   NEWPAR  "\n";
print   NEWPAR $skp_line;
printf  NEWPAR  $fmt_pww, $pww[0],$pww[1],$pww[2],$pww[3],$pww[4],$pww[5],$pww[6],$pww[7],$pww[8],$pww[9],$pww[10],$pww[11];
print   NEWPAR  "\n";
printf  NEWPAR  $fmt_pwd, $pwd[0],$pwd[1],$pwd[2],$pwd[3],$pwd[4],$pwd[5],$pwd[6],$pwd[7],$pwd[8],$pwd[9],$pwd[10],$pwd[11];
print   NEWPAR  "\n";
printf  NEWPAR  $fmt_tmx, $tmx[0],$tmx[1],$tmx[2],$tmx[3],$tmx[4],$tmx[5],$tmx[6],$tmx[7],$tmx[8],$tmx[9],$tmx[10],$tmx[11];
print   NEWPAR  "\n";
printf  NEWPAR  $fmt_tmn, $tmn[0],$tmn[1],$tmn[2],$tmn[3],$tmn[4],$tmn[5],$tmn[6],$tmn[7],$tmn[8],$tmn[9],$tmn[10],$tmn[11];
print   NEWPAR  "\n";

while (<INPAR>) {   print NEWPAR $_  }

print  NEWPAR  "\nModified by Rock:Clime on ";
&printdate;
print  NEWPAR " from\n $station_line";

close INPAR;
close NEWPAR;

#print "I came from: $comefrom\n";

if ($comefrom eq "") {
  if ($platform eq 'pc') {
  	exec "perl ../rc/showpersonal.pl -download $units $useaf $comefrom $me"}
  else {
  	exec "../rc/showpersonal.pl -download -u$units $useaf $comefrom $me"}
}else {
	if ($platform eq 'pc') {
		exec "perl ../rc/rockclim.pl -server -u$units $comefrom"}
  else {
  	exec "../rc/rockclim.pl -server -u$units $comefrom"}
}
#---------------------------

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
#    printf NEWPAR "%0.2d:%0.2d ", $hour, $min;
#    print NEWPAR $ampm[$ampmi],"  ",$days[$wday],";
    print NEWPAR  $months[$mon];
    print NEWPAR " ",$mday,", ",$year+1900;
#    print NEWPAR " GMT/UTC/Zulu<br>\n";

#    $ampmi = 0;
#    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime;
#    if ($hour == 12) {$ampmi = 1}
#    if ($hour > 12) {$ampmi = 1; $hour = $hour - 12}
#    printf NEWPAR "%0.2d:%0.2d ", $hour, $min;
#    print NEWPAR $ampm[$ampmi],"  ",$days[$wday],";
#    print NEWPAR $months[$mon];
#    print NEWPAR " ",$mday,", ",$year+1900, " Pacific Time";
}
