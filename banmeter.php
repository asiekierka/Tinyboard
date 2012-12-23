<?php
 // Kod do banmeter
 require_once("inc/functions.php");
 require_once("inc/libpaski.php");

 if(!$config['banmeter']) die("derp");

 mysql_connect($config['db']['server'],$config['db']['user'], $config['db']['password']);
 mysql_select_db($config['db']['database']);

 $min_time = time() - $config['banmeter_time'] - 1;
 $q1 = mysql_query("SELECT ip FROM bans WHERE `set`>".$min_time.";");
 if($q1 == FALSE) {
  die("Error: " . mysql_error());
 }
 $ilosc_banow = mysql_num_rows($q1);
 $ulamek_banow = $ilosc_banow/$config['banmeter_max'];
 header("Content-Type: image/png");
 header("Content-Transfer-Encoding: binary");
 imagepng(drawPasek(200,13,$ulamek_banow,"GohuFont-11"));
?>
