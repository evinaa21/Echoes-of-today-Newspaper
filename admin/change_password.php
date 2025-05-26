<?php
require_once '../includes/auth_admin.php'; 
require '../includes/db_connection.php';

$id = $_SESSION['user_id'];  
$id = intval($id);

/* ---------- fetch user ---------- */
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$errors = ['password' => '', 'confirm_password' => ''];
$success = isset($_GET['success']);          // <-- (1) read flag

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password          = $_POST['password']          ?? '';
    $confirm_password  = $_POST['confirm_password']  ?? '';

    if (strlen($password) < 6)            $errors['password']         = 'Password must be at least 6 characters.';
    if ($password !== $confirm_password)  $errors['confirm_password'] = 'Passwords do not match.';

    if (empty($errors['password']) && empty($errors['confirm_password'])) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $id);
        $stmt->execute();
        $stmt->close();

        /* ---------- (2) redirect back with success flag ---------- */
        header('Location: change_password.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Change Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin_style.css" />
  <style>
      /* styles unchanged … */
      .form-label        { font-weight:500;color:#0d47a1 }
      .error             { color:red;font-size:.85rem }
      .dashboard-card    { padding:30px;border-radius:20px;background:linear-gradient(135deg,#fff,#f5f8ff);
                           box-shadow:0 8px 24px rgba(0,0,0,.1);border:1px solid #d0e2ff }
      .dashboard-card:hover { transform:scale(1.015);box-shadow:0 12px 30px rgba(0,0,0,.15) }
      .dashboard-card .form-control { border-radius:12px;border:1px solid #b0c4de }
      .dashboard-card .btn-primary  { background:#0d47a1;border-color:#0d47a1;border-radius:10px;font-weight:500 }
      .dashboard-card .btn-secondary{ border-radius:10px }
      .main-content { margin-left:250px;margin-top:60px;padding:30px;background:#f8f9fa;min-height:100vh }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include 'admin_sidebar.php'; ?>
    <div class="container-fluid p-0">
      <?php include 'admin_header.php'; ?>

      <div class="main-content">
        <h2 class="fw-bold mb-4" style="font-size:2rem;">Change Password</h2>

        <div class="dashboard-card mx-auto" style="max-width:500px;">
          <?php if ($success): ?>
            <div class="alert alert-success mb-4">✅ Password changed successfully.</div>
          <?php endif; ?>

          <?php if ($success): ?>
  <script>
    /* once DOM is ready, drop ?success=1 from the address bar */
    window.addEventListener('DOMContentLoaded', () => {
      const cleanURL = location.pathname;            // e.g.  /admin/change_password.php
      history.replaceState(null, '', cleanURL);      // updates bar without reloading
    });
  </script>
<?php endif; ?>


          <p class="text-muted">Update your account password below.</p>

          <form method="POST" class="mt-3">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label">New Password</label>
              <input type="password" class="form-control" name="password" id="password">
              <?php if ($errors['password']): ?>
                <div class="error"><?= $errors['password'] ?></div>
              <?php endif; ?>
            </div>

            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password" class="form-control" name="confirm_password" id="confirm_password">
              <?php if ($errors['confirm_password']): ?>
                <div class="error"><?= $errors['confirm_password'] ?></div>
              <?php endif; ?>
            </div>

            <div class="mb-3 d-flex align-items-center justify-content-end gap-2">
              <label class="form-check-label small mb-0" for="showPasswords">Show Passwords</label>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="showPasswords"
                       onclick="passwordToggle()">
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary">Change Password</button>
              <a href="admin_profile.php" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function passwordToggle() {
      ['password','confirm_password'].forEach(id => {
        const f = document.getElementById(id);
        f.type = (f.type === 'password') ? 'text' : 'password';
      });
    }
  </script>
</body>
</html>
