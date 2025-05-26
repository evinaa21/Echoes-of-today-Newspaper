<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: ../login.php?error=Please log in');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?error=Admins only');
    exit;
}