<?php


session_start();
include_once('../includes/db_connection.php');

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'journalist') {
//     header('Location: ../login.php');
//     exit;
// }
$userId = $_SESSION['user_id'] ?? 2;
$userId = intval($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $state = $_POST['state'] ?? '';
    $zip_code = $_POST['zip_code'] ?? '';
    $city = $_POST['city'] ?? '';

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, address = ?, state = ?, zip_code = ?, city = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $first_name, $last_name, $address, $state, $zip_code, $city, $userId);
    $stmt->execute();
    $stmt->close();
    exit;
}

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
  <title>Profile Setting</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6fa;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    .content {
      margin-left: 250px;
      padding: 20px;
    }

    .card {
      background-color: white;
      border: none;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
    }

    .left-card {
      background-color: #4527a0;
      color: white;
    }

    .left-card .card-body {
      padding: 30px;
    }

    .profile-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 10px;
      border: 3px solid #00f0ff;
    }

    .info-table td {
      padding: 6px 0;
    }

    .label {
      color: #6c757d;
    }

    .info-value {
      font-weight: 500;
    }

    .submit-btn {
      background-color: #4a3aff;
      color: white;
      border: none;
      padding: 12px 25px;
      font-weight: 500;
      border-radius: 6px;
    }

    .submit-btn:hover {
      background-color: #362fff;
    }

    .text-label {
      font-weight: 500;
      color: #333;
    }

    .form-section input {
      border-radius: 6px;
      height: 45px;
    }

    .image-box {
      border: 2px dashed #ccc;
      padding: 10px;
      text-align: center;
      border-radius: 6px;
    }

    .image-box img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
    }
  </style>
</head>

<body>

  <?php include('journalistNavBar.php'); ?>
  <?php include('header.php'); ?>

  <div class="content">
    <h5 class="mb-4 fw-semibold">Profile Setting</h5>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card left-card">
          <div class="card-body text-center">
            <img src="<?= htmlspecialchars($image) ?>" class="profile-img" alt="User Image">
            <h5><?= htmlspecialchars($full_name) ?></h5>
            <p>@<?= htmlspecialchars($username) ?></p>
            <table class="table text-white info-table mt-3">
              <tr><td class="label">Email</td><td class="info-value">[Email is protected]</td></tr>
              <tr><td class="label">Mobile</td><td class="info-value">[Mobile is protected]</td></tr>
              <tr><td class="label">Country</td><td class="info-value"><?= htmlspecialchars($country ?? 'N/A') ?></td></tr>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card p-4">
          <h6 class="mb-3">Profile Information</h6>
          <form action="#" method="POST" enctype="multipart/form-data">
            <div class="row g-3">
              <div class="col-md-4 text-center">
                <div class="image-box">
                  <img src="<?= htmlspecialchars($image) ?>" alt="User Image">
                  <input type="file" name="profile_image" class="form-control mt-2">
                  <small class="text-muted">*.jpg, *.png — 350x300px</small>
                </div>
              </div>

              <div class="col-md-8 form-section">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="text-label">First Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($first_name) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="text-label">Last Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($last_name) ?>">
                  </div>
                  <div class="col-md-12">
                    <label class="text-label">Address</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($address) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="text-label">State</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($state) ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="text-label">Zip Code</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($zip_code) ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="text-label">City</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($city) ?>">
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 text-end">
              <button type="submit" class="submit-btn">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
