<?php
require_once '../includes/db_connection.php';

// Set content type for JSON response
header('Content-Type: application/json');

if (isset($_POST['ad_id']) && is_numeric($_POST['ad_id'])) {
    $ad_id = (int) $_POST['ad_id'];

    // Update impression count
    $update_query = "UPDATE advertisements SET impressions = impressions + 1 WHERE id = ? AND is_active = 1";
    $stmt = $conn->prepare($update_query);

    if ($stmt) {
        $stmt->bind_param("i", $ad_id);
        $success = $stmt->execute();

        if ($success && $stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Impression tracked']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No rows updated - ad may not exist or be inactive']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ad_id']);
}

$conn->close();
?>