<?php
include_once("config.php");
$sender_email = "ravi@sqlfusion.com";
$contact = new sqlQuery($conx);
$sql = "SELECT c.* FROM contact c LEFT JOIN contact_email ce ON c.idcontact = ce.idcontact WHERE ce.email_address = '".$sender_email."'";
$contact->query($sql);
if($contact->getNumRows()) {
  while($contact->fetch()) {
    $results = array("firstname"=>$contact->getData("firstname"),"lastname"=>$contact->getData("lastname"),"position"=>$contact->getData("position"),"company"=>$contact->getData("company"));
  }
  echo json_encode($results);
}
?>