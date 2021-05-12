#! /usr/bin/perl

# display PRISM mean annual precip and elevation around 
# a given latitude longitude
# and allow selection of neighboring PRISM cell

# 2004.06.03 DEH Font face
#                handle discrepency in units reporting if not selected
# 06/15/2000 DEH Tighten up HTML code so Navigator will display properly
# 07/11/2000 DEH Fix elevation reported for PRISM location
# 07/19/2000 DEH Patch $units = '-uft' to 'ft' and '-um' to 'm'

# $debug = 1;
  $version = '2005.06.06';
# $version = '2004.06.03';

  &ReadParse(*parameters);
  $latitude = $parameters{'latitude'};
  $longitude = $parameters{'longitude'};
  $platitude = $parameters{'platitude'};
  $plongitude = $parameters{'plongitude'};
  $lathem = $parameters{'lathem'};
  $longhem = $parameters{'longhem'};
  $elev = $parameters{'elev'};
  $units = $parameters{'units'};
  $CLfile = $parameters{'CL'};
  $climate_name = $parameters{'climate_name'};
  $workingdir = $parameters{'workingdir'};

  if ($units eq '-uft') {$units = 'ft'}   # DEH 07/19/00
  if ($units eq '-um') {$units = 'm'}     # DEH 07/19/00
  if ($units ne 'ft' && $units ne 'm') {$units = 'ft'};	  # DEH 2004.06.03
 
  for $i (1..12) {
    $mean_p[$i-1]=$parameters{"pc$i"};
  }
  $mean_p[12] = $parameters{"pc"};

  if ($platitude eq "") {
    $platitude = $latitude;
    $plongitude = $longitude;
  }
  $platitude = sprintf"%4.2f", $platitude + 0;
  $plongitude = sprintf"%4.2f", $plongitude + 0;

# ********************************************

  $month_no = 0;
  for $prism_month_file ('us_ppt_01.asc', 'us_ppt_02.asc',
                         'us_ppt_03.asc', 'us_ppt_04.asc',
                         'us_ppt_05.asc', 'us_ppt_06.asc',
                         'us_ppt_07.asc', 'us_ppt_08.asc',
                         'us_ppt_09.asc', 'us_ppt_10.asc',
                         'us_ppt_11.asc', 'us_ppt_12.asc') {
    $month_no++;
    open PRI, "<$prism_month_file" || goto badfile;
      $text = <PRI>;    @ncols = split ' ',$text;
      $text = <PRI>;    @nrows = split ' ',$text;
      $text = <PRI>;    @xll = split ' ',$text;
      $text = <PRI>;    @yll = split ' ',$text;
      $text = <PRI>;    @cellsize = split ' ',$text;
      $text = <PRI>;    @nodata_value = split ' ',$text;

      $ncols=@ncols[1];
      $nrows=@nrows[1];
      $xll=@xll[1];
      $yll=@yll[1];
      $cellsize=@cellsize[1];
      $nodata_value = @nodata_value[1]+0;

      $xur = $xll + ($ncols + 1) * $cellsize;
      $yur = $yll + ($nrows + 1) * $cellsize;
      $platitude += 0;
      $plongitude += 0;
      $xlln = -$xll;
      $row = $nrows - ($platitude - $yll) / $cellsize;
      $col = ($xlln - $plongitude) / $cellsize;
      $row = int($row+.5); $col = int($col+.5);

##### edge effects
      if ($row < 0) {$row = 0}
      if ($col < 0) {$col = 0}
      if ($row > $nrows) {$row = $nrows}
      if ($col > $ncols) {$col = $ncols}

      for ($rowcount=0;$rowcount<$row;$rowcount++) {$above = <PRI>;}
      $thisisus = <PRI>;
      $below    = <PRI>;
    close PRI;

    chomp $above; chomp $thisisus; chomp $below; 		# 2005.06.06 DEH

    @pptabove = split ' ',$above;
    @ppt      = split ' ',$thisisus;
    @pptbelow = split ' ',$below;

#   mark any cells with missing values 2005.06.06 DEH

    if ($row < 1) {$nw_bad=1; $n_bad=1; $ne_bad=1};
    if ($row > $nrows) {$sw_bad=1; $s_bad=1; $se_bad=1};
    if ($col < 1) {$nw_bad=1; $w_bad=1; $sw_bad=1};
    if ($col > $ncols) {$ne_bad=1; $e_bad=1; $se_bad=1};

# if ($col<1) ...
    if (!$nw_bad) {$ppt=@pptabove[$col-1]; if ($ppt eq $nodata_value) {$nw_bad=1} else {$NW[$month_no] = ($ppt+0)/100}}
    if (!$n_bad)  {$ppt=@pptabove[$col];   if ($ppt eq $nodata_value) {$n_bad=1}  else { $N[$month_no] = ($ppt+0)/100}}
    if (!$ne_bad) {$ppt=@pptabove[$col+1]; if ($ppt eq $nodata_value) {$ne_bad=1} else {$NE[$month_no] = ($ppt+0)/100}}
    if (!$w_bad)  {$ppt=@ppt[$col-1];      if ($ppt eq $nodata_value) {$w_bad=1}  else { $W[$month_no] = ($ppt+0)/100}}
    if (!$c_bad)  {$ppt=@ppt[$col];        if ($ppt eq $nodata_value) {$c_bad=1}  else {$ppcp[$month_no] = ($ppt+0)/100}}
    if (!$e_bad)  {$ppt=@ppt[$col+1];      if ($ppt eq $nodata_value) {$e_bad=1}  else { $E[$month_no] = ($ppt+0)/100}}
    if (!$sw_bad) {$ppt=@pptbelow[$col-1]; if ($ppt eq $nodata_value) {$sw_bad=1} else {$SW[$month_no] = ($ppt+0)/100}}
    if (!$s_bad)  {$ppt=@pptbelow[$col];   if ($ppt eq $nodata_value) {$s_bad=1}  else { $S[$month_no] = ($ppt+0)/100}}
    if (!$se_bad) {$ppt=@pptbelow[$col+1]; if ($ppt eq $nodata_value) {$se_bad=1} else {$SE[$month_no] = ($ppt+0)/100}}
  }

####################

##### Totals
    if ($nw_bad) {$pcpinmm1='N/A'} else {$pcpinmm1 = $NW[1] + $NW[2] + $NW[3] + $NW[4] + $NW[5] + $NW[6] + $NW[7] + $NW[8] + $NW[9] + $NW[10] + $NW[11] + $NW[12]}
    if ($n_bad)  {$pcpinmm2='N/A'} else {$pcpinmm2 = $N[1] + $N[2] + $N[3] + $N[4] + $N[5] + $N[6] + $N[7] + $N[8] + $N[9] + $N[10] + $N[11] + $N[12]}
    if ($ne_bad) {$pcpinmm3='N/A'} else {$pcpinmm3 = $NE[1] + $NE[2] + $NE[3] + $NE[4] + $NE[5] + $NE[6] + $NE[7] + $NE[8] + $NE[9] + $NE[10] + $NE[11] + $NE[12]}
    if ($w_bad)  {$pcpinmm4='N/A'} else {$pcpinmm4 = $W[1] + $W[2] + $W[3] + $W[4] + $W[5] + $W[6] + $W[7] + $W[8] + $W[9] + $W[10] + $W[11] + $W[12]}
    if ($c_bad)  {$pcpinmm5='N/A'} else {$pcpinmm5 = $ppcp[1] + $ppcp[2] + $ppcp[3] + $ppcp[4] + $ppcp[5] + $ppcp[6] + $ppcp[7] + $ppcp[8] + $ppcp[9] + $ppcp[10] + $ppcp[11] + $ppcp[12]}
    if ($e_bad)  {$pcpinmm6='N/A'} else {$pcpinmm6 = $E[1] + $E[2] + $E[3] + $E[4] + $E[5] + $E[6] + $E[7] + $E[8] + $E[9] + $E[10] + $E[11] + $E[12]}
    if ($sw_bad) {$pcpinmm7='N/A'} else {$pcpinmm7 = $SW[1] + $SW[2] + $SW[3] + $SW[4] + $SW[5] + $SW[6] + $SW[7] + $SW[8] + $SW[9] + $SW[10] + $SW[11] + $SW[12]}
    if ($s_bad)  {$pcpinmm8='N/A'} else {$pcpinmm8 = $S[1] + $S[2] + $S[3] + $S[4] + $S[5] + $S[6] + $S[7] + $S[8] + $S[9] + $S[10] + $S[11] + $S[12]}
    if ($se_bad) {$pcpinmm9='N/A'} else {$pcpinmm9 = $SE[1] + $SE[2] + $SE[3] + $SE[4] + $SE[5] + $SE[6] + $SE[7] + $SE[8] + $SE[9] + $SE[10] + $SE[11] + $SE[12]}

##### Elevation
    open ELEV, '<us_25m.dem';
      $text = <ELEV>;    @ncols = split ' ',$text;
      $text = <ELEV>;    @nrows = split ' ',$text;
      $text = <ELEV>;    @xll = split ' ',$text;
      $text = <ELEV>;    @yll = split ' ',$text;
      $text = <ELEV>;    @cellsize = split ' ',$text;
      $nodata_value_elev='-999';
# 2005.06.06 DEH begin
      $ncols=@ncols[1];
      $nrows=@nrows[1];
      $xll=@xll[1];
      $yll=@yll[1];
      $cellsize=@cellsize[1];

      $xur = $xll + ($ncols + 1) * $cellsize;
      $yur = $yll + ($nrows + 1) * $cellsize;
      $platitude += 0;
      $plongitude += 0;
      $xlln = -$xll;
      $row = $nrows - ($platitude - $yll) / $cellsize;
      $col = ($xlln - $plongitude) / $cellsize;
      $row = int($row+.5); $col = int($col+.5);
# 2005.06.06 DEH end
      for ($rowcount=0;$rowcount<$row;$rowcount++) {
        $above = <ELEV>
      }
      $thisisus = <ELEV>;
      $below    = <ELEV>;
    close ELEV;
    chomp $above; chomp $thisisus; chomp $below;	# 2005.06.06 DEH
    @elevabove = split ' ',$above;
    @elev      = split ' ',$thisisus;
    @elevbelow = split ' ',$below;

#   mark any cells with missing values 2005.06.06 DEH

    if ($row < 1) {$nw_el_bad=1; $n_el_bad=1; $ne_el_bad=1};
    if ($row > $nrows) {$sw_el_bad=1; $s_el_bad=1; $se_el_bad=1};
    if ($col < 1) {$nw_el_bad=1; $w_el_bad=1; $sw_el_bad=1};
    if ($col > $ncols) {$ne_el_bad=1; $e_el_bad=1; $se_el_bad=1};

    if (!$nw_el_bad) {$ele = @elevabove[$col-1]; if ($ele eq $nodata_value_elev) {$nw_el_bad=1} else {$elev1=$ele}}
    if (!$n_el_bad)  {$ele = @elevabove[$col];   if ($ele eq $nodata_value_elev) {$n_el_bad=1}  else {$elev2=$ele}}
    if (!$ne_el_bad) {$ele = @elevabove[$col+1]; if ($ele eq $nodata_value_elev) {$ne_el_bad=1} else {$elev3=$ele}}
    if (!$w_el_bad)  {$ele = @elev[$col-1];      if ($ele eq $nodata_value_elev) {$w_el_bad=1}  else {$elev4=$ele}}
    if (!$c_el_bad)  {$ele = @elev[$col];        if ($ele eq $nodata_value_elev) {$c_el_bad=1}  else {$elev5=$ele}}
    if (!$e_el_bad)  {$ele = @elev[$col+1];      if ($ele eq $nodata_value_elev) {$e_el_bad=1}  else {$elev6=$ele}}
    if (!$sw_el_bad) {$ele = @elevbelow[$col-1]; if ($ele eq $nodata_value_elev) {$sw_el_bad=1} else {$elev7=$ele}}
    if (!$s_el_bad)  {$ele = @elevbelow[$col];   if ($ele eq $nodata_value_elev) {$s_el_bad=1}  else {$elev8=$ele}}
    if (!$se_el_bad) {$ele = @elevbelow[$col+1]; if ($ele eq $nodata_value_elev) {$se_el_bad=1} else {$elev9=$ele}}

#  English units for values
   if ($units eq 'ft') {
     if (!$c_bad) {
       for $ii (1..12) {
         $ppcp[$ii] = sprintf"%4.2f", $ppcp[$ii] / 25.4;
       }
     }
     $pcpinmm1 = sprintf"%4.2f", $pcpinmm1 / 25.4 if (!$nw_bad);
     $pcpinmm2 = sprintf"%4.2f", $pcpinmm2 / 25.4 if (!$n_bad);
     $pcpinmm3 = sprintf"%4.2f", $pcpinmm3 / 25.4 if (!$ne_bad);
     $pcpinmm4 = sprintf"%4.2f", $pcpinmm4 / 25.4 if (!$w_bad);
     $pcpinmm5 = sprintf"%4.2f", $pcpinmm5 / 25.4 if (!$c_bad);
     $pcpinmm6 = sprintf"%4.2f", $pcpinmm6 / 25.4 if (!$e_bad);
     $pcpinmm7 = sprintf"%4.2f", $pcpinmm7 / 25.4 if (!$sw_bad);
     $pcpinmm8 = sprintf"%4.2f", $pcpinmm8 / 25.4 if (!$s_bad);
     $pcpinmm9 = sprintf"%4.2f", $pcpinmm9 / 25.4 if (!$se_bad);
     $elev1 = sprintf"%5.0f", $elev1 * 3.28 if (!$nw_el_bad); 
     $elev2 = sprintf"%5.0f", $elev2 * 3.28 if (!$n_el_bad);
     $elev3 = sprintf"%5.0f", $elev3 * 3.28 if (!$ne_el_bad);
     $elev4 = sprintf"%5.0f", $elev4 * 3.28 if (!$w_el_bad);
     $elev5 = sprintf"%5.0f", $elev5 * 3.28 if (!$c_el_bad);
     $elev6 = sprintf"%5.0f", $elev6 * 3.28 if (!$e_el_bad);
     $elev7 = sprintf"%5.0f", $elev7 * 3.28 if (!$sw_el_bad);
     $elev8 = sprintf"%5.0f", $elev8 * 3.28 if (!$s_el_bad);
     $elev9 = sprintf"%5.0f", $elev9 * 3.28 if (!$se_el_bad);
   }
#--------------------------------------------------------------------
goto ok;

badfile:
  print "Content-type: text/html\n\n"; 
  print "<HTML>
 <head>
  <title>Kent, we have a problem.</title>
 </head>
 <body>
  I'm sorry -- I was unable to open PRISM file $prism_month_file
 </body>
</html>
";
die;

ok:

print "Content-type: text/html\n\n"; 
print "<HTML>
 <HEAD>
  <script language=\"JavaScript\">
   function LL(_lat, _long) {
    twopointfivemin = 1/24
    newlatitude=parseFloat(document.forms[0].platitude.value)+_lat*twopointfivemin
    newlongitude=parseFloat(document.forms[0].plongitude.value)+_long*twopointfivemin
    // alert (newlatitude)
    // alert (newlongitude)
    document.forms[0].platitude.value=newlatitude
    document.forms[0].plongitude.value=newlongitude
    document.forms[0].submit()
   }
  </script>

  <TITLE>Display PRISM Precipitation</title>
 </HEAD>

 <body bgcolor=\"white\" link=\"#1603F3\" vlink=\"#160A8C\">
  <font face=\"Arial, Geneva, Helvetica\">
";
if ($debug == 1) {print "Units: $units<br>"}       # DEH 07/19/00
print "
   <CENTER>
    <H2>
     <font color=\"red\">P</font>
     <font color=\"orange\">R</font>
     <font color=\"gold\">I</font>
     <font color=\"green\">S</font>
     <font color=\"blue\">M</font>&nbsp; Precipitation
    </H2>
    <H3>for modifying $climate_name at
     $latitude<SUP>o</SUP>$lathem
     $longitude<SUP>o</SUP>$longhem and ";
if ($units eq 'm') {                                   # DEH 07/11/2000
       $elevx = sprintf"%6.0f", $elev * 3.28;          # DEH 07/11/2000
       print "     $elevx m elevation";                # DEH 07/11/2000
     }                                                 # DEH 07/11/2000
     else {                                            # DEH 07/11/2000
       print "     $elev ft elevation";                # DEH 07/11/2000
     }                                                 # DEH 07/11/2000
print "     <br>Prism Location:
      $platitude<SUP>o</SUP>N
      $plongitude<SUP>o</SUP>W and "; 
if ($units eq 'm') {
       print "     $elev5 m elevation";
     }
     else {
       print "     $elev5 ft elevation";
     }
# if ($debug) ... 'hidden' to 'text'
print "    </H3>

    <img src=\"/fswepp/images/line_red2.gif\"><p>
    <FORM action=\"prisform.pl\" method=\"post\">
     <INPUT name=\"latitude\" type=hidden value=\"$latitude\">
     <INPUT name=\"longitude\" type=hidden value=\"$longitude\">
     <INPUT name=\"platitude\" type=hidden value=\"$platitude\">
     <INPUT name=\"plongitude\" type=hidden value=\"$plongitude\">
     <INPUT name=\"units\" type=hidden value=\"$units\">\n";

for $i (1..12) {
     print "     <INPUT name=\"pc$i\" type=hidden value=\"$mean_p[$i-1]\">\n";
     }

print "
     <INPUT name=\"pc\" type=hidden value=\"$mean_p[12]\">
     <INPUT name=\"elev\" type=hidden value=\"$elev\">
     <INPUT name=\"CL\" type=hidden value=\"$CLfile\">
     <INPUT name=\"climate_name\" type=hidden value=\"$climate_name\">
     <INPUT name=\"workingdir\" type=hidden value=\"$workingdir\">
    </form>

    <table width=\"70%\">
     <tr>
      <td>
       <font face=\"Arial, Geneva, Helvetica\">
        Select a value in the annual precipitation or elevation tables to move
        north, south, east, or west in the PRISM 2.5&nbsp;minute (approximately";
  if ($units eq 'm') {
    print " 4&nbsp;km";
  }
  else {
    print " 2.5&nbsp;mi";
  } 
  print ") grid of values. The value in the center is your current location.
      </td>
     </tr>
    </table>
    <p>";
print "
    <table width=100%>
     <tr>
      <td width=\"45%\" Rowspan=2 align=right>

       <table border=1 bgcolor=\"white\">
        <tr>
         <th bgcolor=\"#85D2D2\">
          <font face=\"Arial, Geneva, Helvetica\">
           <i>Station</i><br>
           Mean<br>
           Precipitation<br>";
  if ($units eq 'm') {
    print "(mm)";
  }
  else {
    print "(in)";
  } 
  print "          </font>
         </th>
         <th bgcolor=\"85D2D2\">
          <font face=\"Arial, Geneva, Helvetica\">
           Month
          </font>
         </th>
         <th bgcolor=\"85D2D2\">
          <font face=\"Arial, Geneva, Helvetica\">
           <i>PRISM</i><br>
           Mean<br>
           Precipitation<br>";
  if ($units eq 'm') {
    print "(mm)";
  }
  else {
    print "(in)";
  } 
  print "       </font>
            </th>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[0]</font></td> 
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">January</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[1]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[1]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">February</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[2]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[2]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">March</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[3]</font></td>
        </tr>
        <tr>
          <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[3]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">April</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[4]</font></td>
        </tr>
        <tr> 
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[4]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">May</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[5]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[5]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">June</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[6]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[6]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">July</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[7]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[7]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">August</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[8]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[8]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">September</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[9]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[9]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">October</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[10]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[10]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">November</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[11]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[11]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">December</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$ppcp[12]</font></td>
        </tr>
        <tr>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$mean_p[12]</font></td>
         <th bgcolor=\"85D2D2\"><font face=\"Arial, Geneva, Helvetica\">Annual</font></th>
         <td align=right><font face=\"Arial, Geneva, Helvetica\">$pcpinmm5</font></td>
        </tr>
       </table>

      </td>

      <td align=center><b>Annual Precipitation ";
  if ($units eq 'm') {
    print "(mm)";
  }
  else {
    print "(in)";
  }
  print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>

       <table cellpadding=10>
        <tr>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(1,1)\">$pcpinmm1</a></td>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(1,0)\">$pcpinmm2</a></td>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(1,-1)\">$pcpinmm3</a></td>
         <td rowspan=3><img src=\"/fswepp/images/nthsth.gif\"></td>
        </tr>
        <tr>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(0,1)\">$pcpinmm4</a></td>
         <td>$pcpinmm5</td>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(0,-1)\">$pcpinmm6</a></td>
        </tr>
        <tr>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(-1,1)\">$pcpinmm7</a></td>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(-1,0)\">$pcpinmm8</a></td>
         <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(-1,-1)\">$pcpinmm9</a></td>
        </tr>
        <tr>
         <td colspan=3><img align=center src=\"/fswepp/images/eastwst.gif\"></td>
        </tr>
       </table>
       <p>
      </td>
     </tr>
     <tr>
      <td align=center><b>Elevation ";
  if ($units eq 'm') {
    print "(m)";
  }
  else {
    print "(ft)";
  } 
  print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</B>

    <table cellpadding=10>
     <tr>
      <td bgcolor=\"#eeeeee\"><a href=\"JavaScript:LL(1,1)\">$elev1</a></td>
      <td bgcolor=\"#eeeeee\"><a href=\"JavaScript:LL(1,0)\">$elev2</a></td>
      <td bgcolor=\"#eeeeee\"><a href=\"JavaScript:LL(1,-1)\">$elev3</a></td>
      <td rowspan=3><img src=\"/fswepp/images/nthsth.gif\"></td>
     </tr>
     <tr>
      <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(0,1)\">$elev4</a></td>
      <td>$elev5</td>
      <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(0,-1)\">$elev6</a></td>
     </tr>
     <tr>
      <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(-1,1)\">$elev7</a></td>
      <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(-1,0)\">$elev8</a></td>
      <td bgcolor=\"eeeeee\"><a href=\"JavaScript:LL(-1,-1)\">$elev9</a></td>
     </tr>
     <tr>
     <td colspan=3><img align=center src=\"/fswepp/images/eastwst.gif\"></td>
    </table>
    <p>

   </TABLE>

    <FORM action=\"/cgi-bin/fswepp/rc/modpar.pl\" method=post>
     <INPUT name=\"platitude\" type=hidden value=\"$platitude\">
     <INPUT name=\"plongitude\" type=hidden value=\"$plongitude\">
     <INPUT name=\"elev\" type=hidden value=\"$elev5\">
     <INPUT name=\"units\" type=hidden value=\"$units\">
     <INPUT name=\"CL\" type=hidden value=\"$CLfile\">
     <INPUT name=\"climate_name\" type=hidden value=\"$climate_name\">
";
  for $i (1..12) {
    print "     <INPUT name=\"ppcp$i\" type=hidden value=\"$ppcp[$i]\">\n";
  }

  print "
     <INPUT name=\"ppcp\" type=hidden value=\"$pcpinmm5\">
     <INPUT name=\"workingdir\" type=hidden value=\"$workingdir\">
     <INPUT name=\"prism\" type=hidden value=\"prism\">
     <input type=\"submit\" value=\"Use PRISM values\">
    </form>

    <form name=\"modback\" method=\"post\" action=\"../rc/modpar.pl\">
     <INPUT name=\"units\" type=hidden value=\"$units\">
     <INPUT name=\"CL\" type=hidden value=\"$CLfile\">
     <INPUT name=\"workingdir\" type=hidden value=\"$workingdir\">
     <INPUT name=\"retreat\" type=hidden value=\"retreat\">
     <input type=\"submit\" value=\"Retreat\">
    </form>
   </center>
   <font size=-1>
    <b>prisform</b> version $version (a part of <i>Rock:Clime</i>)<br>
    USDA Forest Service, Rocky Mountain Research Station
   </font>
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
