<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

// Kontrollo nëse është dhënë ID dhe është numër
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Kryej fshirjen nga tabela categories
    $deleteQuery = "DELETE FROM categories WHERE id = $id";
    if (mysqli_query($conn, $deleteQuery)) {
        // Pas suksesit kthehu te faqja e kategorive
        header("Location: category.php?msg=deleted");
        exit;
    } else {
        die("Error deleting category: " . mysqli_error($conn));
    }
} else {
    // Nëse ID nuk është e vlefshme
    header("Location: category.php?error=invalid_id");
    exit;
}
?>
