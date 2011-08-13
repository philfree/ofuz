<?php


  include_once("config.php");

  $q = new sqlQuery($GLOBALS['conx']);
  $q->query("show tables");
  $qr = new sqlQuery($GLOBALS['conx']);
  echo "<table>";
  while ($row = $q->fetchArray()) {
	  $qr->query("select count(*) as total from ".$row[0]);
	  $qr->fetch();
	  if ($qr->getD("total") > 10000) { 
		echo "\n<tr><td>".$row[0]."</td><td><b>".$qr->getD("total")."</b></td></tr>"	;	  
	  } else {
		echo "\n<tr><td>".$row[0]."</td><td>".$qr->getD("total")."</td></tr>";
	  }
  }
  echo "</table>";

?>


