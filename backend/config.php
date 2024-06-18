<?php
$databaseHost = 'localhost';
$databaseUsername = 'user';
$databasePassword = '';
$databaseName = 'arq';

// Connect to the database
$conn = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName); 

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>
