<?php

// Params to connect to a database
$dbHost = "localhost";
$dbUser = "AdminLab12";
$dbPass = "4VPnroTOC6wOU3mn";
$dbName = "gomoku";

//connection to database
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
     die("Database connection failed.");
}
?>