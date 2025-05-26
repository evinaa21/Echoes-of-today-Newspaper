<?php
require_once '../config/config.php';

// Establish connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME,3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");
?>