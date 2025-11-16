<?php
$conn = new mysqli("localhost", "root", "", "foodhub");

if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
