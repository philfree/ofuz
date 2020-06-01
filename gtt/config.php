<?php
/*
 * Database connection
 *
 */
$dbhost = 'localhost';
$dbuser = 'ofuzdev';
$dbpass = 'd3v5';
$db = 'ofuz';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $db);

if(! $conn ){
die('Could not connect: ' . mysqli_error());
}
?>
