<?php
session_start();
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
    $role = $_POST['role'] ?? 'journalist';
    $status = 1;

    // Basic validation
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: manage_staff.php");
        exit;
    }

    // Check if username or email already exists
    $checkQuery = "SELECT id FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Username or email already exists.";
        header("Location: manage_staff.php");
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $insertQuery = "INSERT INTO users (first_name, last_name, email, username, password, role, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $username, $hashed_password, $role, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "New staff member added successfully.";
    } else {
        $_SESSION['error'] = "Error adding staff. Please try again.";
    }

    header("Location: manage_staff.php");
    exit;
}
?>
