<?php
session_start();
require '../includes/db_connection.php';
include("header.php");
include("journalistNavBar.php");
//$id = $_POST['id'] ?? $_GET['id'] ?? '';

$id=$_SESSION['user_id'] ?? 2;
$id=intval($id);

if (empty($id)) {
    header('Location: users.php');
    exit;
}

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
        header("Location: edit_password.php?id=$id&success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password </title>
    
    <script>
        function togglePasswordVisibility() {
            const pass = document.getElementById("password");
            const confirm = document.getElementById("confirm_password");
            pass.type = pass.type === "password" ? "text" : "password";
            confirm.type = confirm.type === "password" ? "text" : "password";
        }
    </script>
    <style>
    body {
    background-color: #e6f0ff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.card {
    background-color: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.logo {
    width: 60px;
    margin-bottom: 15px;
}

.title {
    font-size: 22px;
    font-weight: bold;
    color: #0055aa;
    margin-bottom: 5px;
}

.subtitle {
    font-size: 14px;
    color: #666;
    margin-bottom: 20px;
}

form label {
    display: block;
    text-align: left;
    margin: 10px 0 4px;
    font-weight: 600;
    color: #0055aa;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #99c2ff;
    border-radius: 6px;
    background-color: #f4faff;
    font-size: 14px;
}

.readonly-input {
    background-color: #eaf4ff;
    color: #555;
    cursor: not-allowed;
}

.error {
    display: block;
    color: red;
    font-size: 13px;
    margin-top: 2px;
}

.success-msg {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}

.toggle {
    font-size: 13px;
    color: #0077cc;
    margin-top: 8px;
    cursor: pointer;
    text-align: right;
}

.buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.btn-primary {
    background-color: #005fa3;
    color: lightblue;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary:hover {
    background-color: #005fa3;
}

.btn-cancel {
    background-color: #ddd;
    color: #333;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
}

.btn-cancel:hover {
    background-color: #bbb;
}

</style>
</head>
<body>
    <div class="container">
        <div class="card">
            
            <h1 class="title">Change Password</h1>
            <p class="subtitle">Enter a new password for your account</p>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-msg"> Password changed successfully!</div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly class="readonly-input">

                <label>New Password</label>
                <input type="password" name="password" id="password">
                <span class="error"><?= $errors['password'] ?></span>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password">
                <span class="error"><?= $errors['confirm_password'] ?></span>

                <label class="toggle">
                 <input type="checkbox" id="showPasswords" onclick="togglePasswordVisibility()">
                Show Passwords
                </label>

                <div class="buttons">
                    <button type="submit" class="btn-primary">Change Password</button>
                    <a href="profile.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
