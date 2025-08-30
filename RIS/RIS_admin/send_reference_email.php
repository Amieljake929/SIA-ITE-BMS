<?php
// RIS/send_reference_email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendReferenceNumber($toEmail, $fullName, $referenceNumber) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'barangaybagbagmanagementsystem@gmail.com'; // Your Gmail
        $mail->Password   = 'flda drgk dptd abwo'; // App Password (not your Gmail password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender (must match Username)
        $mail->setFrom('barangaybagbagmanagementsystem@gmail.com', 'Barangay Bagbag eServices');
        
        // Recipient
        $mail->addAddress($toEmail, $fullName);

        // Optional: Reply-To
        $mail->addReplyTo('barangaybagbagmanagementsystem@gmail.com', 'Barangay Support');

        // Content
        $mail->isHTML(true);
        $mail->Subject = '✅ Congratulations! Your Resident Registration is Approved';
        $mail->Body    = "
        <h2>Hello, {$fullName}!</h2>
        <p>We’re happy to inform you that your resident registration has been <strong>approved</strong>.</p>
        <p><strong>Your Reference Number:</strong> 
           <code style='font-size:1.2em; background:#f0f0f0; padding:10px; border-radius:6px; font-family:monospace;'>
               {$referenceNumber}
           </code>
        </p>
        <p>Use this number to create your online account at the Barangay eServices Portal.</p>
        <br>
        <p>Thank you!</p>
        <strong>Barangay Bagbag eServices Team</strong>
        ";

        // Optional: Plain text fallback
        $mail->AltBody = "Hello, {$fullName}\n\n"
            . "Your resident registration has been approved.\n"
            . "Your Reference Number: {$referenceNumber}\n\n"
            . "Use this number to register your online account.\n\n"
            . "Barangay Bagbag eServices Team";

        // Send email
        $mail->send();
        return true;

    } catch (Exception $e) {
        // Log detailed error
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>