<?php

// NewSystem DB connect
//$newsdbhost = 'localhost';
$newsdbhost = '10.151.1.10';
$newsdbuser = 'devadmin_anime';
$newsdbpass = 'L=.zZ76[,TOqwf*&tl';
$newsdbname = 'devadmin_anime';
$x = mysql_connect($newsdbhost,$newsdbuser,$newsdbpass) or die(mysql_error());
mysql_select_db($newsdbname,$x);
?>