<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/PHPMailer/src/PHPMailer.php';
require_once 'includes/PHPMailer/src/SMTP.php';
require_once 'includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "No account found with that email.";
    } else {
        // Generate 6-digit OTP
        $otp     = rand(100000, 999999);

        // Delete old OTPs for this email
        $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

        // Save new OTP to DB
     $stmt = $pdo->prepare("INSERT INTO password_resets (email, otp, expires_at) 
                        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
$stmt->execute([$email, $otp]);

        // Send OTP via PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io'; // Mailtrap host
            $mail->SMTPAuth   = true;
            $mail->Username = 'd8290f9167fd05';
            $mail->Password = '83dcf50c762890'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 2525;

            $mail->setFrom('noreply@foodbyte.com', 'FoodByte');
            $mail->addAddress($email, $user['username']);
            $mail->Subject = 'Your FoodByte Password Reset OTP';
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family:DM Sans,sans-serif;max-width:400px;margin:auto;padding:2rem;'>
                    <h2 style='color:#F97316'>🍴 FoodByte</h2>
                    <p>Hi <strong>{$user['username']}</strong>,</p>
                    <p>Your OTP for password reset is:</p>
                    <div style='font-size:2.5rem;font-weight:800;color:#F97316;
                                letter-spacing:8px;text-align:center;padding:1rem;
                                background:#FFF7ED;border-radius:12px;margin:1rem 0'>
                        {$otp}
                    </div>
                    <p style='color:#6B7280;font-size:0.85rem'>
                        This OTP expires in <strong>10 minutes</strong>.<br>
                        If you didn't request this, ignore this email.
                    </p>
                </div>
            ";
            $mail->send();

            // Store email in session for next step
            $_SESSION['reset_email'] = $email;
            header('Location: verify-otp.php');
            exit;

        } catch (Exception $e) {
            $error = "Could not send OTP. Please try again.";
        }
    }
}
?>

