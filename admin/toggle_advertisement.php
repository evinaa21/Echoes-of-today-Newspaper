<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['status'])) {
    header('Location: advertisement.php');
    exit;
}

$id = (int)$_GET['id'];
$new_status = (int)$_GET['status']; // 1 = enable, 0 = disable

$update = "UPDATE advertisements SET is_active = $new_status WHERE id = $id";
if (mysqli_query($conn, $update)) {
    $_SESSION['success'] = $new_status ? "Advertisement enabled." : "Advertisement disabled.";
} else {
    $_SESSION['error'] = "Failed to update status: " . mysqli_error($conn);
}

header('Location: advertisement.php');
exit;
