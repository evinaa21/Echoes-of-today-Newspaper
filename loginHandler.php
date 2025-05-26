<?php
session_start();
require_once 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);   // e-mail or username
    $password   = $_POST['password'];

    /* ───────────────────────── 1. look-up active user ───────────────────────── */
    $sql  = "SELECT * FROM users
             WHERE (email = ? OR username = ?)
               AND status = 'active'        -- ← matches your table
             LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    /* ───────────────────────── 2. verify password ──────────────────────────── */
    if ($result && ($user = $result->fetch_assoc())) {

        if (password_verify($password, $user['password'])) {

            /* ─────────── 3. store session info & redirect by role ──────────── */
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'journalist':
                    header('Location: journalist/dashboard.php');
                    break;
                default:
                    header('Location: public/index.php');
            }
            exit;

        } else {
            header('Location: login.php?error=Incorrect password'); 
            exit;
        }

    } else {
        header('Location: login.php?error=User not found or inactive');
        exit;
    }
}
?>
