<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'journalist';
    $status = 1;
    $bio = ''; // Default empty
    $profile_image = ''; // Default empty

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: manage_staff.php");
        exit;
    }

    // Check password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: manage_staff.php");
        exit;
    }

    // Check for existing username/email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Username or email already exists.";
        header("Location: manage_staff.php");
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new staff
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role, first_name, last_name, bio, profile_image, status, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssssi", $username, $hashed_password, $email, $role, $first_name, $last_name, $bio, $profile_image, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff member added successfully.";
    } else {
        $_SESSION['error'] = "Error adding staff. Try again.";
    }

    header("Location: manage_staff.php");
    exit;
}
?>
