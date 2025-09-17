<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';


$user_id = $_SESSION['user_id'];

// Check if already has pending report
$stmt = $conn->prepare("SELECT 1 FROM community_reports WHERE user_id = ? AND status = 'Pending' LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    $conn->close();
    header("Location: R.community_report.php?error=pending");
    exit();
}
$stmt->close();

// Generate community_report_id: CMR-2025-0001
$year = date('Y');
$prefix = "CMR-{$year}-";
$stmt = $conn->prepare("SELECT community_report_id FROM community_reports WHERE community_report_id LIKE ? ORDER BY id DESC LIMIT 1");
$searchPrefix = $prefix . '%';
$stmt->bind_param("s", $searchPrefix);
$stmt->execute();
$result = $stmt->get_result();
$latest_id = $result->fetch_assoc();
$stmt->close();

if ($latest_id) {
    $number = (int)substr($latest_id['community_report_id'], -4);
    $new_number = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
} else {
    $new_number = "0001";
}
$community_report_id = $prefix . $new_number;

// Get form data
$first_name = trim($_POST['first_name']);
$middle_name = trim($_POST['middle_name']);
$last_name = trim($_POST['last_name']);
$house_no = trim($_POST['house_no']);
$street = trim($_POST['street']);
$purok = trim($_POST['purok']);
$contact_number = trim($_POST['contact_number']);
$email = !empty($_POST['email']) ? trim($_POST['email']) : null;
$incident_type = $_POST['incident_type'];
$incident_date = $_POST['incident_date'];
$incident_time = !empty($_POST['incident_time']) ? $_POST['incident_time'] : null;
$incident_location = trim($_POST['incident_location']);
$incident_details = trim($_POST['incident_details']);
$accused_names_residences = !empty($_POST['accused_names_residences']) ? trim($_POST['accused_names_residences']) : null;
$requested_action = trim($_POST['requested_action']);

// Optional: Get resident_id
$resident_id = null;
$stmt = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resident_result = $stmt->get_result();
if ($row = $resident_result->fetch_assoc()) {
    $resident_id = $row['id'];
}
$stmt->close();

// Handle file upload with size validation
$evidence_path = null;
if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == 0) {
    $file = $_FILES['evidence'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'mp4', 'mov'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file extension
    if (!in_array($ext, $allowed)) {
        header("Location: R.community_report.php?error=invalid_file");
        exit();
    }

    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
    $isVideo = in_array($ext, ['mp4', 'mov']);

    // Validate file size
    if ($isImage && $file['size'] > 10 * 1024 * 1024) { // 10MB
        header("Location: R.community_report.php?error=file_too_large_image");
        exit();
    }
    if ($isVideo && $file['size'] > 40 * 1024 * 1024) { // 50MB
        header("Location: R.community_report.php?error=file_too_large_video");
        exit();
    }

    // Proceed with upload
    $filename = $community_report_id . '_evidence_' . time() . '.' . $ext;
    $upload_dir = 'uploads/reports/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        $evidence_path = 'uploads/reports/' . $filename;
    } else {
        header("Location: R.community_report.php?error=upload_failed");
        exit();
    }
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO community_reports (
    community_report_id, user_id, resident_id, first_name, middle_name, last_name,
    house_no, street, purok, contact_number, email,
    incident_type, incident_date, incident_time, incident_location,
    incident_details, accused_names_residences, requested_action, evidence_path
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "siissssssssssssssss",
    $community_report_id, $user_id, $resident_id,
    $first_name, $middle_name, $last_name,
    $house_no, $street, $purok, $contact_number, $email,
    $incident_type, $incident_date, $incident_time, $incident_location,
    $incident_details, $accused_names_residences, $requested_action, $evidence_path
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: R.community_report.php?success=community_report");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: R.community_report.php?error=database");
    exit();
}
?>