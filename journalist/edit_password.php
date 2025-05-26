<?php
require_once '../includes/auth_journalist.php';
require '../includes/db_connection.php';

$id = $_SESSION['user_id'] ?? 2;
$id = intval($id);

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: dashboard.php');
    exit;
}

$errors = ['password' => '', 'confirm_password' => ''];
$showSuccessModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (empty($errors['password']) && empty($errors['confirm_password'])) {
        $hashpassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashpassword, $id);
        $stmt->execute();
        $stmt->close();
        $showSuccessModal = true;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .form-label {
            font-weight: 500;
            color: #0d47a1;
        }

        .error {
            color: red;
            font-size: 0.85rem;
        }

        .dashboard-card {
            padding: 30px;
            border-radius: 20px;
            background: linear-gradient(135deg, #ffffff, #f5f8ff);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
            border: 1px solid #d0e2ff;
        }

        .dashboard-card:hover {
            transform: scale(1.015);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .dashboard-card .form-control {
            border-radius: 12px;
            border: 1px solid #b0c4de;
        }

        .dashboard-card .btn-primary {
            background-color: #0d47a1;
            border-color: #0d47a1;
            border-radius: 10px;
            font-weight: 500;
        }

        .dashboard-card .btn-secondary {
            border-radius: 10px;
        }
    </style>

</head>

<body>
    <div class="d-flex">
        <?php include 'journalistNavBar.php'; ?>
        <div class="container-fluid p-0" style="margin-left: 250px;">
            <?php include 'header.php'; ?>
            <div class="p-4">
                <h2 class="mb-4">Change Password</h2>

                <div class="dashboard-card mx-auto" style="max-width: 500px;">
                    <p class="text-muted">Update your account password below.</p>

                    <form method="POST" class="mt-3">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>"
                                readonly>
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
                                    onclick="togglePasswordVisibility()">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                            <a href="profile.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-success">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Password Changed</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ✅ Your password has been updated successfully.
                </div>
                <div class="modal-footer">
                    <a href="profile.php" class="btn btn-success">OK</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pass = document.getElementById("password");
            const confirm = document.getElementById("confirm_password");
            pass.type = pass.type === "password" ? "text" : "password";
            confirm.type = confirm.type === "password" ? "text" : "password";
        }

        <?php if ($showSuccessModal): ?>
            window.addEventListener('DOMContentLoaded', function () {
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            });
        <?php endif; ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>