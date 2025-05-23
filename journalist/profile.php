<?php
session_start();
include_once('../includes/db_connection.php');


// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'journalist') {
//     header('Location: ../login.php');
//     exit;
// }
$userId = $_SESSION['user_id'] ?? 2; 
$userId = intval($userId);    



$stmt = $conn->prepare("SELECT username, email, role, first_name, last_name, bio, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email, $role, $first_name, $last_name, $bio, $profile_image);
$stmt->fetch();
$stmt->close();
$conn->close();

$full_name = $first_name . " " . $last_name;
$image = $profile_image ?: "https://via.placeholder.com/120";
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #0d6efd;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card p-4">
      <div class="text-center">
        <img src="<?php echo htmlspecialchars($image); ?>" class="profile-img mb-3" alt="Profile Image">
        <h3><?php echo htmlspecialchars($full_name); ?></h3>
        <p class="text-muted">@<?php echo htmlspecialchars($username); ?> | <?php echo htmlspecialchars($role); ?></p>
      </div>
      <hr>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
      <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($bio)); ?></p>
      <a href="edit_password.php" class="btn btn-primary mt-3">Change Password</a>
    </div>
  </div>
</body>
</html>
