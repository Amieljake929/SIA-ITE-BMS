<?php
session_start();

// Only allow admin or system to delete
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Database Connection
$conn = new mysqli("localhost:3307", "root", "", "bms");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

$permit_id = $_POST['permit_id'] ?? null;

if (!$permit_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid permit ID.']);
    exit;
}

// -------------------------------
// Step 1: Get the file paths from database
// -------------------------------
$stmt = $conn->prepare("SELECT valid_id_front, proof_of_address, previous_clearance, cedula FROM business_permit WHERE business_permit_id = ?");
$stmt->bind_param("s", $permit_id);
$stmt->execute();
$result = $stmt->get_result();
$files = $result->fetch_assoc();
$stmt->close();

if (!$files) {
    echo json_encode(['success' => false, 'message' => 'Permit not found.']);
    exit;
}

// Define upload directory
$upload_dir = __DIR__ . '/images/'; // resident/Business_Permit/images/

$deleted_files = [];
$failed_deletes = [];

// -------------------------------
// Step 2: Delete each file if exists
// -------------------------------
$file_fields = ['valid_id_front', 'proof_of_address', 'previous_clearance', 'cedula'];

foreach ($file_fields as $field) {
    $filename = $files[$field];
    if ($filename) {
        $file_path = $upload_dir . $filename;
        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                $deleted_files[] = $filename;
            } else {
                $failed_deletes[] = $filename;
            }
        }
    }
}

// -------------------------------
// Step 3: Delete record from database
// -------------------------------
$stmt = $conn->prepare("DELETE FROM business_permit WHERE business_permit_id = ?");
$stmt->bind_param("s", $permit_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    $message = "Permit deleted successfully.";
    if (!empty($failed_deletes)) {
        $message .= " But failed to delete files: " . implode(', ', $failed_deletes);
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'deleted_files' => $deleted_files,
        'failed' => $failed_deletes
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete record from database.'
    ]);
}
?>