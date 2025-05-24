<?php
session_start();
include_once('../includes/db_connection.php');

$userId = $_SESSION['user_id'] ?? 2;
$userId = intval($userId);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = $_POST['first_name'] ?? '';
  $last_name = $_POST['last_name'] ?? '';
  $address = $_POST['address'] ?? '';
  $zip_code = $_POST['zip_code'] ?? '';
  $city = $_POST['city'] ?? '';
  $country = $_POST['country'] ?? '';

  $update_stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, address = ?, zip_code = ?, city = ?, country = ? WHERE id = ?");
  $update_stmt->bind_param("ssssssi", $first_name, $last_name, $address, $zip_code, $city, $country, $userId);
  $update_stmt->execute();
  $update_stmt->close();
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}

$stmt = $conn->prepare("SELECT username, email, role, first_name, last_name, bio, profile_image, address, zip_code, city, mobile, country FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email, $role, $first_name, $last_name, $bio, $profile_image, $address, $zip_code, $city, $mobile, $country);
$stmt->fetch();
$stmt->close();
$conn->close();

$full_name = $first_name . " " . $last_name;
$image = $profile_image ? "uploads/2.png" : "uploads/2.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Profile Setting</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/journalist_style.css" />
  <style>
    .profile-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #00f0ff;
    }

    .left-box {
      background-color: #4527a0;
      color: white;
      padding: 30px;
      border-radius: 10px;
    }

    .info-table td {
      padding: 6px 0;
    }

    .image-box {
      border: 2px dashed #ccc;
      padding: 15px;
      text-align: center;
      border-radius: 10px;
    }

    .image-box img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
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

    .left-box i {
      color: #90caf9;
    }

    .text-light-emphasis {
      color: #cfd8dc !important;
    }
  </style>
</head>

<body>
  <div class="d-flex">
    <?php include('journalistNavBar.php'); ?>

    <div class="container-fluid p-0" style="margin-left: 250px;">
      <?php include('header.php'); ?>

      <div class="p-4">
        <h2 class="mb-4">Profile Setting</h2>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="left-box text-center">
              <img src="<?= htmlspecialchars($image) ?>" class="profile-img mb-3" alt="User Image">
              <h5><?= htmlspecialchars($full_name) ?></h5>
              <p class="text-light">@<?= htmlspecialchars($username) ?></p>
              <div class="text-start mt-4">
                <div class="mb-3">
                  <i class="bi bi-envelope-fill me-2 text-white"></i>
                  <strong class="text-white">Email:</strong>
                  <span class="text-white ms-2"><?= htmlspecialchars($email) ?></span>
                </div>
                <div class="mb-3">
                  <i class="bi bi-phone-fill me-2 text-white"></i>
                  <strong class="text-white">Mobile:</strong>
                  <span class="text-white ms-2"><?= htmlspecialchars($mobile ?? 'N/A') ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT FORM PANEL -->
          <div class="col-md-8">
            <div class="card p-4">
              <h5 class="mb-3">Update Information</h5>
              <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                  <div class="col-md-4 text-center">
                    <div class="image-box">
                      <img src="<?= htmlspecialchars($image) ?>" alt="User Image">
                      <input type="file" name="profile_image" class="form-control mt-2">
                      <small class="text-muted">*.jpg, *.png — 350x300px</small>
                    </div>
                  </div>

                  <div class="col-md-8">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($first_name) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($last_name) ?>">
                      </div>
                      <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address) ?>">
                      </div>
                      <div class="col-md-3">
                        <label class="form-label">Zip Code</label>
                        <input type="text" name="zip_code" class="form-control" value="<?= htmlspecialchars($zip_code) ?>">
                      </div>
                      <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($city) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($country ?? '') ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-4 text-end">
                  <button type="submit" class="submit-btn">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>