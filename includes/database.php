<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "jakup_ceraja";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Lidhja me databazën dështoi: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>