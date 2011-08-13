<?php
// display a user password


include("config.php");

$d = new UserRelations();

echo $d->decrypt("qM1vsedZt7nWydzE");
 

?>