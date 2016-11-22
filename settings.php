<?php
// change info to connect to specific database with authentication
$host = "HOST";
$user = "USER";
$pass = "PASS";
$team_db = "TEAM_DB";

// Make a connection to the database
$db = new mysqli($host, $user, $pass, $team_db);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
