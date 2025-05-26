<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

// Check for valid ID and status
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['status'])) {
    header("Location: category.php");
    exit;
}

$id = (int)$_GET['id'];
$newStatus = ($_GET['status'] == 1) ? 1 : 0; // sanitize

// Update the status
$query = "UPDATE categories SET is_active = $newStatus WHERE id = $id LIMIT 1";
if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = $newStatus ? "Category enabled." : "Category disabled.";
} else {
    $_SESSION['error'] = "Failed to update status.";
}

header("Location: category.php");
exit;
