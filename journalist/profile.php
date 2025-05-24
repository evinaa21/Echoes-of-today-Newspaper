<?php
session_start();
include_once('../includes/db_connection.php');

// Use logged-in journalist info
$userId = $_SESSION['user_id'] ?? 2;
$userId = intval($userId);

$stmt = $conn->prepare("SELECT username, email, role, first_name, last_name, bio, profile_image, address, state, zip_code, city FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email, $role, $first_name, $last_name, $bio, $profile_image, $address, $state, $zip_code, $city);
$stmt->fetch();
$stmt->close();
$conn->close();

$full_name = $first_name . " " . $last_name;
$image = $profile_image ?: "https://via.placeholder.com/120";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .container-fluid {
      margin-left: 250px;
    }
    .profile-header {
      font-size: 1.5rem;
      font-weight: 600;
    }
    .card {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .left-card {
      background-color: #4527a0;
      color: white;
    }
    .profile-img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #0dcaf0;
    }
    .text-label {
      font-weight: 500;
      color: #333;
    }
    .form-control {
      border-radius: 6px;
      height: 42px;
    }
    .submit-btn {
      background-color: #0d47a1;
      color: white;
      font-weight: 500;
      border-radius: 6px;
    }
    .submit-btn:hover {
      background-color: #093170;
    }
    .image-box {
      border: 2px dashed #adb5bd;
      padding: 10px;
      border-radius: 6px;
      text-align: center;
    }
  </style>
</head>

<body>

  <?php include('journalistNavBar.php'); ?>
  <div class="container-fluid p-0">
    <?php include('header.php'); ?>

    <div class="p-4">
      <h2 class="profile-header mb-4">Profile Settings</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card left-card text-center p-4">
            <img src="<?= htmlspecialchars($image) ?>" class="profile-img mb-3" alt="User Image">
            <h5><?= htmlspecialchars($full_name) ?></h5>
            <p class="mb-3">@<?= htmlspecialchars($username) ?></p>
            <table class="table text-white">
              <tr><td>Email</td><td>[Hidden]</td></tr>
              <tr><td>Mobile</td><td>[Hidden]</td></tr>
              <tr><td>City</td><td><?= htmlspecialchars($city ?: 'N/A') ?></td></tr>
            </table>
          </div>
        </div>

        <div class="col-md-8">
          <div class="card p-4">
            <h5 class="mb-3">Edit Profile Info</h5>
            <form action="#" method="POST" enctype="multipart/form-data">
              <div class="row g-3">
                <div class="col-md-4 text-center">
                  <div class="image-box">
                    <img src="<?= htmlspecialchars($image) ?>" class="img-fluid mb-2" alt="Profile">
                    <input type="file" name="profile_image" class="form-control mt-2">
                    <small class="text-muted">*.jpg, *.png — 350x300px</small>
                  </div>
                </div>

                <div class="col-md-8">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="text-label">First Name</label>
                      <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($first_name) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="text-label">Last Name</label>
                      <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($last_name) ?>">
                    </div>
                    <div class="col-md-12">
                      <label class="text-label">Address</label>
                      <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="text-label">State</label>
                      <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($state) ?>">
                    </div>
                    <div class="col-md-3">
                      <label class="text-label">Zip Code</label>
                      <input type="text" name="zip_code" class="form-control" value="<?= htmlspecialchars($zip_code) ?>">
                    </div>
                    <div class="col-md-3">
                      <label class="text-label">City</label>
                      <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($city) ?>">
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-4 text-end">
                <button type="submit" class="btn submit-btn">Update Profile</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
