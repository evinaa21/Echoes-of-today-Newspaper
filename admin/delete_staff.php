<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    // Hard delete
    $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Staff member deleted permanently.";
    } else {
        $_SESSION['error'] = "❌ Failed to delete staff member.";
    }

    header("Location: manage_staff.php");
    exit();
} else {
    $_SESSION['error'] = "❌ Invalid request.";
    header("Location: manage_staff.php");
    exit();
}
?>
