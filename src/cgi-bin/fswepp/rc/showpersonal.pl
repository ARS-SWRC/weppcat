#! /usr/bin/perl
#!/fsapps/fssys/bin/perl

# showpersonal.pl  --

# I have no idea what this file did in the original Rockclime, but
# here it is the way out of these files.  It is called by createpar.pl after
# the new parameter file is created.  This file should be altered by anyone wanting to use
# these programs to return to the desired page after the PAR file is created.
# ELA - 5/28/2008    

  $version='2006.05.28';


print "Content-type: text/html\n\n";
print '
<html>
<head>
<title>Personal Climate Stations</title>

<SCRIPT Language="JavaScript">
<!--

  function init() {
  	//alert("Test");
  	parent.frames[2].location = "/weppcat/modCliRes.php";
  }

   // -->
  </SCRIPT>
 </head>
 <body onLoad="init();" bgcolor="white">
';

print "
 </body>
</html>";
