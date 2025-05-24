<?php
// image.php
// Validate and sanitize the filename
$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
if (empty($filename)) {
    header('HTTP/1.0 404 Not Found');
    exit('File not found');
}

// Set the path to the uploads directory (outside public)
$uploads_path = __DIR__ . '/../uploads/';
$file_path = $uploads_path . $filename;

// Verify file exists and is within uploads directory
if (!file_exists($file_path) || !is_file($file_path)) {
    header('HTTP/1.0 404 Not Found');
    exit('File not found');
}

// Get file mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_path);
finfo_close($finfo);

// Verify it's an image
if (!str_starts_with($mime_type, 'image/')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Not an image file');
}

// Set proper headers and output image
header('Content-Type: ' . $mime_type);
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
