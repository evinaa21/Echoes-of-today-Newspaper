<?php
session_start();
require_once 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifier = trim($_POST['identifier']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND is_active = 1 AND email_verified = 1");
  $stmt->bind_param("ss", $identifier, $identifier);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];

      // Redirect based on role
      switch ($user['role']) {
        case 'admin': header("Location: admin/dashboard.php"); break;
        case 'journalist': header("Location: journalist/dashboard.php"); break;
      }
      exit;
    } else {
      header("Location: login.php?error=Incorrect password");
      exit;
    }
  } else {
    header("Location: login.php?error=User not found or inactive");
    exit;
  }
}
