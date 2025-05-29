<?php
$server_name = "localhost";
$user_name = "root";
$password = "";
$database = "prakse";

$connection = new mysqli($server_name, $user_name, $password, $database);

if ($connection->connect_error) {
    die("Unable to connect to database: " . $connection->connect_error);
}
?>
