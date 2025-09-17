<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';


$user_id = $_SESSION['user_id'];

// Check if already has pending request
$stmt = $conn->prepare("SELECT 1 FROM first_time_job_seekers WHERE user_id = ? AND status = 'Pending' LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    $conn->close();
    header("Location: R.first_time_job.php?error=pending");
    exit();
}
$stmt->close();

// Generate first_time_job_id: FJS-2025-0001
$year = date('Y');
$prefix = "FJS-{$year}-";
$stmt = $conn->prepare("SELECT first_time_job_id FROM first_time_job_seekers WHERE first_time_job_id LIKE ? ORDER BY id DESC LIMIT 1");
$searchPrefix = $prefix . '%';
$stmt->bind_param("s", $searchPrefix);
$stmt->execute();
$result = $stmt->get_result();
$latest_id = $result->fetch_assoc();
$stmt->close();

if ($latest_id) {
    $number = (int)substr($latest_id['first_time_job_id'], -4);
    $new_number = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
} else {
    $new_number = "0001";
}
$first_time_job_id = $prefix . $new_number;

// Get form data
$first_name = trim($_POST['first_name']);
$middle_name = trim($_POST['middle_name']);
$last_name = trim($_POST['last_name']);
$dob = $_POST['dob'];
$birth_place = trim($_POST['birth_place']);
$house_no = trim($_POST['house_no']);
$email = trim($_POST['email']);

$street = trim($_POST['street']);
$purok = trim($_POST['purok']);
$statement = trim($_POST['statement']);
$signature = trim($_POST['signature']);
$application_date = $_POST['application_date'];

// Validate signature
$full_name = trim("{$first_name} {$middle_name} {$last_name}");
if (strtolower($signature) !== strtolower($full_name)) {
    $conn->close();
    header("Location: R.first_time_job.php?error=signature");
    exit();
}

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

// Insert into database
$stmt = $conn->prepare("INSERT INTO first_time_job_seekers (
    first_time_job_id, user_id, resident_id, first_name, middle_name, last_name,
    dob, birth_place, house_no, street, purok,
    statement, signature, application_date, email
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "siissssssssssss",
    $first_time_job_id, $user_id, $resident_id,
    $first_name, $middle_name, $last_name,
    $dob, $birth_place, $house_no, $street, $purok,
    $statement, $signature, $application_date, $email
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: R.first_time_job.php?success=first_time_job");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: R.first_time_job.php?error=database");
    exit();
}
?>