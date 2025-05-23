<?php
session_start();
include('../includes/db_connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: advertisement.php');
    exit;
}

$id = (int)$_GET['id'];

// Optional: get the image path to delete the file
$result = mysqli_query($conn, "SELECT image_path FROM advertisements WHERE id = $id");
$ad = mysqli_fetch_assoc($result);

if ($ad) {
    $imagePath = $ad['image_path'];

    // Delete from database
    if (mysqli_query($conn, "DELETE FROM advertisements WHERE id = $id LIMIT 1")) {
        // Delete file if exists
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $_SESSION['success'] = "Advertisement deleted.";
    } else {
        $_SESSION['error'] = "Failed to delete advertisement.";
    }
}

header('Location: advertisement.php');
exit;
