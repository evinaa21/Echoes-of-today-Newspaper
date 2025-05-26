<?php
session_start();
require_once '../includes/db_connection.php';

$author_id = $_SESSION['user_id'] ?? 2;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title)));
    $content = $_POST['content'] ?? '';
    $excerpt = $_POST['excerpt'] ?? null;
    $category_id = intval($_POST['category_id'] ?? 0);
    $tags = $_POST['tags'] ?? null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $trending = isset($_POST['trending']) ? 1 : 0;
    $has_video = isset($_POST['has_video']) ? 1 : 0;
    $youtube_link = $_POST['youtube_link'] ?? null;
    $status = 'pending_review';

    $featured_image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $filename = time() . '_' . basename($_FILES["featured_image"]["name"]);
        $targetFilePath = $targetDir . $filename;
        if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFilePath)) {
            $featured_image = $filename;
        } else {
            $errors[] = "Failed to upload image.";
        }
    }

    if (empty($title) || empty($content) || $category_id === 0) {
        $errors[] = "Title, content, and category are required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO articles (
            title, slug, content, excerpt, featured_image,
            author_id, category_id, status, is_featured,
            trending, youtube_link, has_video, tags, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->bind_param(
            "sssssiisiiiss",
            $title, $slug, $content, $excerpt, $featured_image,
            $author_id, $category_id, $status, $is_featured,
            $trending, $youtube_link, $has_video, $tags
        );

        if ($stmt->execute()) {
            header("Location: createNews.php?success=1");
            exit;
        } else {
            echo "<script>alert('❌ Failed to submit news. Try again later.'); window.location.href='createNews.php';</script>";
        }

        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<script>alert('❌ $error'); window.location.href='createNews.php';</script>";
        }
    }
}
?>
