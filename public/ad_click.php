<?php
echo "Testing ad click tracking...<br>";

// Include database connection
require_once '../includes/db_connection.php';

// Get first active ad
$test_query = "SELECT id, name, clicks, redirect_url FROM advertisements WHERE is_active = 1 LIMIT 1";
$result = $conn->query($test_query);

if ($result->num_rows > 0) {
    $ad = $result->fetch_assoc();
    echo "Found ad: " . htmlspecialchars($ad['name']) . "<br>";
    echo "Current clicks: " . $ad['clicks'] . "<br>";
    echo "Test URL: <a href='ad_click.php?ad_id=" . $ad['id'] . "' target='_blank'>Click here to test</a><br>";
} else {
    echo "No active advertisements found in database.<br>";
}

// Check if ad_id is provided
if (!isset($_GET['ad_id']) || !is_numeric($_GET['ad_id'])) {
    // Redirect to homepage if invalid
    header('Location: index.php');
    exit();
}

$ad_id = (int) $_GET['ad_id'];

// Update click count for the advertisement
$update_query = "UPDATE advertisements SET clicks = clicks + 1 WHERE id = ? AND is_active = 1";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("i", $ad_id);
$stmt->execute();

// Get the redirect URL
$redirect_query = "SELECT redirect_url FROM advertisements WHERE id = ? AND is_active = 1";
$stmt = $conn->prepare($redirect_query);
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Redirect to the advertisement URL
    header('Location: ' . $row['redirect_url']);
    exit();
} else {
    // If ad not found, redirect to homepage
    header('Location: index.php');
    exit();
}

$conn->close();
?>