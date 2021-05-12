#! /usr/bin/perl
#! /fsapps/fssys/bin/perl

print "\n\nCreate select box for state climate .PAR files\n";
print "Enter state name (not abbreviation): ";
$state_name = <STDIN>;
 die if ($state_name eq "");
 print $state_name;
print "\n Enter directory name with .PAR files (state abbrev.): ";
chomp($dir_name = <>);
 die if ($dir_name eq "");
 print "Reading files in ",$dir_name,"\n";
  
  $fout = ">" . $dir_name . "_climates";
  print "Creating file ",$fout,"\n\n";
  open OUT, $fout;

  print OUT  $state_name;
  print OUT  '<input type="hidden" name="state" value="';
  print OUT  $dir_name,'">',"\n";
  print OUT  '<Select name="station" size="15">',"\n";

#  $wildcard = $dir_name . "/*.PAR";
      while (<*.PAR>) {
        $f = $_;
        open(M,$f) || die;
          if ($_ = <M>) {}
        close (M);
        $station = substr($_, $c + 1, 40);
          print OUT  '<OPTION VALUE="';
          $f = substr($f,0,length($f)-4);     # remove .ext
          print OUT  $f;
          print OUT  '"> ' . $station . "\n";
      }
print OUT '</select>',"\n";

