<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['reset_email'])) {
    header('Location: forgot-password.php');
    exit;
}

$error = '';
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);

//     // Add this before your SELECT query temporarily
// $debug = $pdo->prepare("SELECT otp, expires_at, NOW() as mysql_now 
//                          FROM password_resets WHERE email = ?");
// $debug->execute([$email]);
// $row = $debug->fetch();
// echo "Stored OTP: " . $row['otp'];
// echo " | Expires: " . $row['expires_at'];
// echo " | MySQL NOW: " . $row['mysql_now'];
// die();

   $otp = trim($_POST['otp']); 

$stmt = $pdo->prepare("SELECT * FROM password_resets 
                        WHERE email = ? AND CAST(otp AS CHAR) = ? 
                        AND expires_at > NOW() 
                        ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$email, (string)$otp]);
    $reset = $stmt->fetch();

    if ($reset) {
        // OTP valid! Let them reset password
        $_SESSION['otp_verified'] = true;
        header('Location: reset-password.php');
        exit;
    } else {
        $error = "Invalid or expired OTP. Please try again.";
    }
}
?>
