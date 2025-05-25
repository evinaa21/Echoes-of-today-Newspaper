<?php
session_start();
include('../includes/db_connection.php');

// Get staff ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid staff ID.";
    exit;
}

$staff_id = intval($_GET['id']);

// Fetch staff user data
$userQuery = "SELECT * FROM users WHERE id = $staff_id LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

if (!$user) {
    echo "Staff not found.";
    exit;
}

// Count news articles
$countQuery = "
SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS approved,
    SUM(CASE WHEN status = 'pending_review' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected
FROM articles
WHERE author_id = $staff_id
";

$countResult = mysqli_query($conn, $countQuery);
$counts = mysqli_fetch_assoc($countResult);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Detail - <?= htmlspecialchars($user['username']) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">

  <style>
    .main-content {
      margin-left: 250px;
      margin-top: 60px;
      padding: 30px;
      background-color: #f8f9fa;
      min-height: 100vh;
    }
  </style>
</head>
<body>

<?php include('admin_header.php'); ?>
<?php include('admin_sidebar.php'); ?>


<div class="main-content px-4 pt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Staff Detail - <?= htmlspecialchars($user['username']) ?></h4>
    <a href="manage_staff.php" class="btn btn-outline-primary">
  <i class="fas fa-arrow-left me-1"></i> Back to Staff List
</a>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
  <div class="col-md-3">
   <a href="manage_staff.php?id=<?= $staff_id ?>&status=all" class="card-link text-decoration-none">
      <div class="card h-100 border border-primary shadow-sm">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
              <i class="fas fa-newspaper fa-lg"></i>
            </div>
            <div>
              <p class="mb-1 text-dark fw-semibold">Total News</p>
              <h4 class="mb-0"><?= $counts['total'] ?? 0 ?></h4>
            </div>
          </div>
          <i class="fas fa-chevron-right text-muted"></i>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-3">
   <a href="manage_staff.php?id=<?= $staff_id ?>&status=published" class="card-link text-decoration-none">
      <div class="card h-100 border border-success shadow-sm">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <div class="bg-success bg-opacity-10 text-success rounded p-2">
              <i class="fas fa-check-circle fa-lg"></i>
            </div>
            <div>
              <p class="mb-1 text-dark fw-semibold">Approved News</p>
              <h4 class="mb-0"><?= $counts['approved'] ?? 0 ?></h4>
            </div>
          </div>
          <i class="fas fa-chevron-right text-muted"></i>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-3">
    <a href="manage_staff.php?id=<?= $staff_id ?>&status=pending_review" class="card-link text-decoration-none">
      <div class="card h-100 border border-warning shadow-sm">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
              <i class="fas fa-clock fa-lg"></i>
            </div>
            <div>
              <p class="mb-1 text-dark fw-semibold">Pending News</p>
              <h4 class="mb-0"><?= $counts['pending'] ?? 0 ?></h4>
            </div>
          </div>
          <i class="fas fa-chevron-right text-muted"></i>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-3">
    <a href="manage_staff.php?id=<?= $staff_id ?>&status=rejected" class="card-link text-decoration-none">
      <div class="card h-100 border border-danger shadow-sm">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <div class="bg-danger bg-opacity-10 text-danger rounded p-2">
              <i class="fas fa-times-circle fa-lg"></i>
            </div>
            <div>
              <p class="mb-1 text-dark fw-semibold">Rejected News</p>
              <h4 class="mb-0"><?= $counts['rejected'] ?? 0 ?></h4>
            </div>
          </div>
          <i class="fas fa-chevron-right text-muted"></i>
        </div>
      </div>
    </a>
  </div>
</div>

  <!-- User Info -->
  <div class="card shadow-sm p-4 rounded-4">
  <h5 class="mb-4">Information of <?= htmlspecialchars($user['username']) ?></h5>
  <div class="row">
    <!-- LEFT: Profile Picture and Basic Info -->
   <div class="col-md-4 d-flex align-items-start justify-content-center">
  <div class="text-center mt-4">
    <img src="<?= $user['profile_image'] ? htmlspecialchars($user['profile_image']) : '../uploads/placeholder.png' ?>" 
         alt="Profile Image" 
         class="rounded-circle mb-3 img-fluid"
         style="max-width: 150px; height: 150px; object-fit: cover; border: 3px solid #ddd;">
    <p class="mb-1 fw-bold"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
    <p class="mb-0 text-muted">@<?= htmlspecialchars($user['username']) ?> &bullet; <?= htmlspecialchars($user['role']) ?></p>
  </div>
</div>



    <!-- RIGHT: Detailed Info Form -->
    <div class="col-md-8">
      <form>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
          </div>
          <div class="col-md-6">
          <label class="form-label">Mobile</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" disabled>
        </div>
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Role</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['role']) ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <input type="text" class="form-control" value="<?= ucfirst($user['status']) ?>" disabled>

          </div>
          <div class="col-md-6">
            <label class="form-label">Joined At</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['created_at']) ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['address'] ?? '') ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['city'] ?? '') ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">State</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['state'] ?? '') ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Zip Code</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>" disabled>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


</body>
</html>
