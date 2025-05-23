<?php
session_start();
include_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die('Invalid request.');
}

$article_id = intval($_POST['id']);
$title = mysqli_real_escape_string($conn, $_POST['title']);
$category_id = intval($_POST['category_id']);
$tags = mysqli_real_escape_string($conn, $_POST['tags']);
$status = mysqli_real_escape_string($conn, $_POST['status']);
$excerpt = mysqli_real_escape_string($conn, $_POST['excerpt']);
$content = mysqli_real_escape_string($conn, $_POST['content']);
$youtube_link = mysqli_real_escape_string($conn, $_POST['youtube_link']);
$is_featured = isset($_POST['is_featured']) ? 1 : 0;
$trending = isset($_POST['trending']) ? 1 : 0;

// Fetch current image
$old_image = '';
$res = mysqli_query($conn, "SELECT featured_image FROM articles WHERE id = $article_id");
if ($row = mysqli_fetch_assoc($res)) {
    $old_image = $row['featured_image'];
}

// Handle image upload
$upload_dir = "../uploads/articles/";
$image_path = $old_image;

if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
    $image_tmp = $_FILES['featured_image']['tmp_name'];
    $image_name = time() . '_' . basename($_FILES['featured_image']['name']);
    $target_path = $upload_dir . $image_name;

    // Move file and update path
    if (move_uploaded_file($image_tmp, $target_path)) {
        $image_path = "uploads/articles/" . $image_name;
    }
}

// Update query
$update_query = "
    UPDATE articles SET
        title = '$title',
        category_id = $category_id,
        tags = '$tags',
        status = '$status',
        excerpt = '$excerpt',
        content = '$content',
        youtube_link = '$youtube_link',
        featured_image = '$image_path',
        is_featured = $is_featured,
        trending = $trending
    WHERE id = $article_id
";

if (mysqli_query($conn, $update_query)) {
    header("Location: viewArticle.php?id=$article_id&updated=1");
    exit;
} else {
    echo "<div class='alert alert-danger text-center mt-5'>❌ Failed to update article. Please try again.</div>";
}
?>
