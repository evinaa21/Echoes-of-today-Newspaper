<?php
require_once '../includes/db_connection.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$article_id = intval($data['article_id']);

if ($article_id > 0) {
    // Update view count
    $stmt = $conn->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();

    // Return new view count
    $result = $conn->query("SELECT view_count FROM articles WHERE id = " . $article_id);
    $views = $result->fetch_assoc()['view_count'];

    echo json_encode(['success' => true, 'views' => $views]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid article ID']);
}

$conn->close();