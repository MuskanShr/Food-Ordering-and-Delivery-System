<?php
session_start();
require_once 'includes/db.php';

// Must come through OTP verification
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header('Location: forgot-password.php');
    exit;
}

$errors = [];
$email  = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (strlen($password) < 6)
        $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm)
        $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")
            ->execute([$hash, $email]);

        // Clean up
        $pdo->prepare("DELETE FROM password_resets WHERE email = ?")
            ->execute([$email]);
        unset($_SESSION['reset_email'], $_SESSION['otp_verified']);

        header('Location: password-changed.php');
        exit;
    }
}
?>
