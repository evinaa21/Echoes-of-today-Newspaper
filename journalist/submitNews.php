<?php
session_start();
include('../includes/db_connection.php');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// Sanitize and fetch POST data
$author_id     = intval($_POST['author_id']);
$status        = mysqli_real_escape_string($conn, $_POST['status'] ?? 'pending_review');
$category_id   = intval($_POST['category_id']);
$title         = mysqli_real_escape_string($conn, $_POST['title']);
$slug          = mysqli_real_escape_string($conn, $_POST['slug']);
$tags          = mysqli_real_escape_string($conn, $_POST['tags'] ?? '');
$trending      = isset($_POST['trending']) && $_POST['trending'] == '1' ? 1 : 0;
$is_featured   = isset($_POST['is_featured']) && $_POST['is_featured'] == '1' ? 1 : 0;
$has_video     = isset($_POST['has_video']) && $_POST['has_video'] == '1' ? 1 : 0;
$youtube_link  = mysqli_real_escape_string($conn, $_POST['youtube_link'] ?? '');
$excerpt       = mysqli_real_escape_string($conn, $_POST['excerpt']);
$content       = mysqli_real_escape_string($conn, $_POST['content']);

// Image upload handling
$image_path = '';
if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $file_tmp = $_FILES['featured_image']['tmp_name'];
    $file_type = mime_content_type($file_tmp);
    $file_name = basename($_FILES['featured_image']['name']);
    $target_dir = '../uploads/';
    $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $file_name);
    $target_file = $target_dir . $new_name;

    if (in_array($file_type, $allowed_types)) {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        if (move_uploaded_file($file_tmp, $target_file)) {
            $image_path = mysqli_real_escape_string($conn, str_replace('../', '', $target_file));
        }
    }
}

// Build the insert query
$query = "
    INSERT INTO articles 
    (author_id, status, category_id, title, slug, tags, trending, is_featured, has_video, youtube_link, excerpt, content, featured_image, created_at)
    VALUES 
    (
        $author_id,
        '$status',
        $category_id,
        '$title',
        '$slug',
        '$tags',
        $trending,
        $is_featured,
        $has_video,
        '$youtube_link',
        '$excerpt',
        '$content',
        '$image_path',
        NOW()
    )
";

// Execute query
$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['success'] = "News submitted successfully!";
    header("Location: allNews.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
