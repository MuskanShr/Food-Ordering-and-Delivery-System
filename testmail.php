<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require 'includes/PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Username = 'd8290f9167fd05';
    $mail->Password = '83dcf50c762890'; 
    $mail->Port = 465;
    

    // Email settings
    $mail->setFrom('from@example.com', 'FoodByte');
    $mail->addAddress('test@example.com');

    $mail->Subject = 'Test Email';
    $mail->Body = 'Hello! Your PHPMailer is working.';

    $mail->send();
    echo "✅ Email sent successfully!";
} catch (Exception $e) {
    echo "❌ Error: {$mail->ErrorInfo}";
}
?>