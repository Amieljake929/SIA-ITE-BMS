<?php
session_start();
include 'db_connect.php';
require_once '../phpmailer_config.php'; // Include PHPMailer config for sending emails

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If user found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $full_name, $email_db, $hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {

            // ✅ Check if the account is approved
            $status_query = $conn->prepare("SELECT status FROM users WHERE id = ?");
            $status_query->bind_param("i", $id);
            $status_query->execute();
            $status_result = $status_query->get_result();

            if ($status_result->num_rows === 1) {
                $user_status = $status_result->fetch_assoc()['status'];

                if ($user_status !== 'approved') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Your account is still pending approval.'
                    ]);
                    $stmt->close();
                    $conn->close();
                    exit();
                }
            }

            // ✅ Proceed with login if approved
            // Check if role is barangay (official, staff, bpso) for MFA
            $barangay_roles = ['official', 'staff', 'bpso'];
            if (in_array(strtolower(trim($role)), $barangay_roles)) {
                // Check for existing unexpired MFA code for this barangay user
                $check_stmt = $conn->prepare("SELECT code, expires_at FROM barangay_mfa WHERE barangay_id = ? AND expires_at > NOW() LIMIT 1");
                $check_stmt->bind_param("i", $id);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    // Reuse existing code
                    $check_stmt->bind_result($mfa_code, $expires_at);
                    $check_stmt->fetch();
                    $check_stmt->close();
                } else {
                    // Generate new 6-digit random code
                    $check_stmt->close();
                    $mfa_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                    // Insert MFA code into database with 30-minute expiration
                    $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                    $mfa_stmt = $conn->prepare("INSERT INTO barangay_mfa (barangay_id, code, verified, expires_at) VALUES (?, ?, 0, ?)");
                    $mfa_stmt->bind_param("iss", $id, $mfa_code, $expires_at);
                    if (!$mfa_stmt->execute()) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to generate MFA code. Please try again.'
                        ]);
                        $mfa_stmt->close();
                        $stmt->close();
                        $conn->close();
                        exit();
                    }
                    $mfa_stmt->close();
                }

                // Send MFA code via email
                $subject = "Your MFA Code for Barangay Bagbag Login";
                $body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                        .header { text-align: center; color: #3a9d6a; }
                        .code { font-size: 24px; font-weight: bold; color: #3a9d6a; text-align: center; margin: 20px 0; }
                        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2 class='header'>Barangay Bagbag MFA Verification</h2>
                        <p>Hi <strong>$full_name</strong>,</p>
                        <p>Your 6-digit verification code is:</p>
                        <div class='code'>$mfa_code</div>
                        <p>This code will expire in 30 minutes. Please enter it to complete your login.</p>
                        <p>If you did not request this, please ignore this email.</p>
                        <div class='footer'>
                            <p>Best regards,<br>Barangay Bagbag Management System</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                if (!sendNotificationEmail($email_db, $subject, $body)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to send MFA code. Please try again.'
                    ]);
                    $stmt->close();
                    $conn->close();
                    exit();
                }

                // Set temporary session variables for MFA
                $_SESSION['mfa_user_id'] = $id;
                $_SESSION['mfa_barangay_id'] = $id; // Assuming barangay_id is same as user_id, adjust if different
                $_SESSION['mfa_full_name'] = $full_name;
                $_SESSION['mfa_email'] = $email_db;
                $_SESSION['mfa_role'] = $role;
                $_SESSION['mfa_expires'] = $expires_at;

                echo json_encode([
                    'success' => true,
                    'redirect_url' => 'mfa_verify.php'
                ]);
            } else {
                // For non-barangay roles, proceed as before (though this shouldn't happen in this file)
                $_SESSION['user_id'] = $id;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email_db;
                $_SESSION['role'] = $role;

                // Normalize and route
                $redirect_url = '';
                switch (strtolower(trim($role))) {
                    case 'official':
                        $redirect_url = '../official/official_dashboard.php';
                        break;
                    case 'staff':
                        $redirect_url = '../staff/staff_dashboard.php';
                        break;
                    case 'bpso':
                        $redirect_url = '../BPSO/BPSO_dashboard.php';
                        break;
                    default:
                        echo json_encode([
                            'success' => false,
                            'message' => 'Account Not Found'
                        ]);
                        $stmt->close();
                        $conn->close();
                        exit();
                }

                echo json_encode([
                    'success' => true,
                    'redirect_url' => $redirect_url
                ]);
            }

        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Incorrect password.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No account found with that email.'
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
