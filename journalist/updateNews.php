<?php
require_once '../includes/auth_journalist.php';
include_once '../includes/db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<div class='alert alert-danger'>❌ Invalid request.</div>";
    exit;
}

// Capture and sanitize inputs
$article_id = intval($_POST['id']);
$title = trim($_POST['title']);
$category_id = intval($_POST['category_id']);
$tags = trim($_POST['tags']);
$excerpt = trim($_POST['excerpt']);
$content = trim($_POST['content']);
$youtube_link = trim($_POST['youtube_link']);
$is_featured = isset($_POST['is_featured']) ? 1 : 0;
$trending = isset($_POST['trending']) ? 1 : 0;
$current_image = trim($_POST['current_image'] ?? '');
$delete_image = isset($_POST['delete_image']) && !empty($current_image);

// Get current status from DB
$check = mysqli_query($conn, "SELECT status FROM articles WHERE id = $article_id");
$existing = mysqli_fetch_assoc($check);
$old_status = $existing['status'] ?? 'draft';

// Determine new status
$status = $old_status === 'rejected' ? 'pending_review' : $old_status;

// Validate required fields
if (!$title || !$category_id || !$content) {
    $message = "<div class='alert alert-danger text-center mt-4'>❌ Please fill in all required fields.</div>";
    include 'edit_article.php';
    exit;
}

// Handle image deletion
$featured_image = $current_image;
if ($delete_image) {
    $image_path = "../$current_image";
    if (file_exists($image_path)) {
        unlink($image_path);
    }
    $featured_image = null;
}

// Handle new image upload
if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['featured_image']['tmp_name'];
    $originalName = basename($_FILES['featured_image']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed)) {
        $uniqueName = uniqid('article_', true) . '.' . $ext;
        $uploadDir = '../uploads/';
        $finalPath = $uploadDir . $uniqueName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($tmpPath, $finalPath)) {
            if ($current_image && !$delete_image) {
                $oldImage = "../$current_image";
                if (file_exists($oldImage))
                    unlink($oldImage);
            }
            $featured_image = "uploads/$uniqueName";
        } else {
            $message = "<div class='alert alert-danger text-center mt-4'>❌ Failed to upload image.</div>";
            include 'edit_article.php';
            exit;
        }
    } else {
        $message = "<div class='alert alert-danger text-center mt-4'>❌ Invalid image format.</div>";
        include 'edit_article.php';
        exit;
    }
}

// Update article
$stmt = $conn->prepare("UPDATE articles 
    SET title = ?, category_id = ?, tags = ?, status = ?, excerpt = ?, content = ?, youtube_link = ?, 
        featured_image = ?, is_featured = ?, trending = ?
    WHERE id = ?");

$stmt->bind_param(
    "sissssssiis",
    $title,
    $category_id,
    $tags,
    $status,
    $excerpt,
    $content,
    $youtube_link,
    $featured_image,
    $is_featured,
    $trending,
    $article_id
);

if ($stmt->execute()) {
    $message = "<div class='alert alert-success text-center mt-4'>✅ Article updated successfully. Status: <strong>$status</strong></div>";
} else {
    $message = "<div class='alert alert-danger text-center mt-4'>❌ Failed to update the article.</div>";
}

// Re-show edit form with success or error message
if ($stmt->execute()) {
    header("Location: editNews.php?id=$article_id&success=1");
    exit();
} else {
    header("Location: editNews.php?id=$article_id&success=0");
    exit();
}

