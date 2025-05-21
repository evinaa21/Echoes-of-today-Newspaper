<?php
session_start();
require_once '../includes/db_connection.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 2;

// Fetch user data
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

$errors = [
    'password' => '',
    'confirm_password' => ''
];

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
        header('Location: edit_password.php?success=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select a New Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center">
    <form class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md" method="POST" action="">
        <h1 class="text-2xl font-bold text-blue-700 mb-6 text-center">🔐 Change Password</h1>

        <!-- Read-Only Username -->
        <div class="mb-5">
            <label for="username" class="block text-sm font-semibold text-blue-600 mb-1">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly
                   class="w-full px-3 py-2 border border-blue-300 rounded-md bg-blue-100 text-gray-800 shadow-sm focus:outline-none">
        </div>

        <div class="mb-5">
            <label for="password" class="block text-sm font-semibold text-blue-600 mb-1">New Password</label>
            <input type="password" id="password" name="password"
                   class="w-full px-3 py-2 border border-blue-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-sm text-red-600 mt-1"><?php echo $errors['password']; ?></p>
        </div>

        <div class="mb-6">
            <label for="confirm_password" class="block text-sm font-semibold text-blue-600 mb-1">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                   class="w-full px-3 py-2 border border-blue-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-sm text-red-600 mt-1"><?php echo $errors['confirm_password']; ?></p>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow focus:outline-none focus:ring-2 focus:ring-blue-400">
                Change Password
            </button>
            <a href="profile.php"
                class="text-red-600 hover:text-red-800 font-medium underline transition duration-150">Cancel</a>
        </div>
    </form>
</body>
</html>
