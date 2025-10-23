<?php
// RIS/send_rejection_email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendRejectionEmail($toEmail, $fullName, $remarks) {
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
        $mail->setFrom('barangaybagbagmanagementsystem@gmail.com', 'BagbagCare');
        
        // Recipient
        $mail->addAddress($toEmail, $fullName);

        // Optional: Reply-To
        $mail->addReplyTo('barangaybagbagmanagementsystem@gmail.com', 'Bagbag Support');

        // Content
        $mail->isHTML(true);
        $mail->Subject = '❌ Your Resident Registration Application has been Rejected';
        $mail->Body    = "
        <h2>Hello, {$fullName}!</h2>
        <p>We regret to inform you that your resident registration application has been <strong>rejected</strong>.</p>
        <p><strong>Reason for Rejection:</strong></p>
        <div style='background:#f8f9fa; padding:15px; border-left:4px solid #dc3545; margin:10px 0;'>
            <p style='margin:0; font-style:italic;'>{$remarks}</p>
        </div>
        <p>Please review the remarks above and make the necessary corrections to your application.</p>
        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>Address the issues mentioned in the remarks</li>
            <li>Visit the BagbagCare Portal to resubmit your registration</li>
            <li>Ensure all required documents are complete and accurate</li>
        </ul>
        <p>If you have any questions, please contact our support team.</p>
        <br>
        <p>Thank you for your understanding.</p>
        <strong>Barangay BagbagCare Team</strong>
        ";

        // Optional: Plain text fallback
        $mail->AltBody = "Hello, {$fullName}\n\n"
            . "We regret to inform you that your resident registration application has been rejected.\n\n"
            . "Reason for Rejection:\n{$remarks}\n\n"
            . "Please review the remarks and make the necessary corrections to your application.\n\n"
            . "Next Steps:\n"
            . "- Address the issues mentioned in the remarks\n"
            . "- Visit the BagbagCare Portal to resubmit your registration\n"
            . "- Ensure all required documents are complete and accurate\n\n"
            . "If you have any questions, please contact our support team.\n\n"
            . "Thank you for your understanding.\n\n"
            . "Barangay BagbagCare Team";

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
