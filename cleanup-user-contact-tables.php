<?php
/*
 * The script is to optimize the unused user's contact tables.
 * optimize: deletes all the User's Contacts tables with the format  userid*_contact 
 *
 * Logic: Lists all the tables with the format userid*_contact (userid20_contact) 
   Extracts the iduser from it, checks if iduser exists in "user" table, if doesn't 
   exist, drops that specific table.
 * 
 * As an OUTPUT, displays all the dropped tables.
 */

include_once('config.php');
include_once('includes/ofuz_check_access.script.inc.php');

$dropped_table = "";
$msg = "";

$user = new User();
$user_contact = new User();
$user_contact->getUserContactTables();

if($user_contact->getNumRows()) {
	while($row = $user_contact->fetchArray()) {
		$table = $row[0];
		$iduser = preg_match('/userid(.*?)_contact/', $table, $matches) ? $matches[1] : "";

		if($iduser) {
			if(!$user->doesUserExist($iduser)) {
				$user->dropTable($table);
				$dropped_table .= $table."<br />";
			}
		}
	}
} 

if($dropped_table) {
	$msg = "Unused tables found and dropped: <br />".$dropped_table;
} else {
	$msg = "There is no unused userid*_contact table found.";
}

echo $msg;
?>
