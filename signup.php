<?php
session_start();
require_once 'includes/db.php';

// if (isset($_SESSION['user_id'])) {
//     header('Location: index.php');
//     exit;
// }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // Validate
    if (!$username)
         $errors[] = "Username is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))    
        $errors[] = "Valid email is required.";
    if (strlen($password) < 6)   
         $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm)  
         $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $errors[] = "Username or email already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->execute([$username, $email, $hash]);

            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>

