<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include helper
require_once 'generate_reference.php';

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "ris");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// Get action and ID
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if (!in_array($action, ['approve', 'reject']) || !$id) {
    $_SESSION['message'] = "Invalid action or ID.";
    header("Location: ris_resident_registration.php");
    exit();
}

$conn->begin_transaction();

try {
    // Check if registration exists and is still pending
    $stmt = $conn->prepare("SELECT id, full_name, email, status FROM registration WHERE id = ? FOR UPDATE");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Registration not found.");
    }

    $reg = $result->fetch_assoc();
    if ($reg['status'] !== 'pending') {
        throw new Exception("Only pending registrations can be approved or rejected.");
    }

    if ($action === 'approve') {
        // Generate next 8-digit Resident ID starting from 25000001
        $res = $conn->query("SELECT resident_id FROM residents_id ORDER BY resident_id DESC LIMIT 1");
        $next_id = 25000001;

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $next_id = (int)$row['resident_id'] + 1;
        }

        $resident_id = str_pad($next_id, 8, '0', STR_PAD_LEFT);

        // Generate unique reference number
        $reference_number = getUniqueReferenceNumber($conn);

        // Insert into residents_id table (with reference_number)
        $stmt2 = $conn->prepare("INSERT INTO residents_id (resident_id, registration_id, reference_number) VALUES (?, ?, ?)");
        $stmt2->bind_param("sss", $resident_id, $id, $reference_number);
        $stmt2->execute();
        $stmt2->close();

        // Update status to approved
        $stmt3 = $conn->prepare("UPDATE registration SET status = 'approved' WHERE id = ?");
        $stmt3->bind_param("s", $id);
        $stmt3->execute();
        $stmt3->close();

        // Send email with PHPMailer
        require_once 'send_reference_email.php';
        $emailSent = sendReferenceNumber($reg['email'], $reg['full_name'], $reference_number);

        $_SESSION['message'] = "✅ Resident approved! Resident ID: $resident_id";
        if ($emailSent) {
            $_SESSION['message'] .= " | ✉️ Reference Number sent to email.";
        } else {
            $_SESSION['message'] .= " | ❌ Failed to send email.";
        }
    } 
    elseif ($action === 'reject') {
        // Update status to rejected
        $stmt4 = $conn->prepare("UPDATE registration SET status = 'rejected' WHERE id = ?");
        $stmt4->bind_param("s", $id);
        $stmt4->execute();
        $stmt4->close();

        $_SESSION['message'] = "❌ Resident rejected.";
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = "❌ Error: " . $e->getMessage();
}

$conn->close();

// Redirect back
header("Location: ris_resident_registration.php");
exit();
?>