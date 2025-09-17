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
$stmt = $conn->prepare("SELECT 1 FROM certificate_of_indigency WHERE user_id = ? AND status = 'Pending' LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $stmt->close();
    $conn->close();
    header("Location: R.indigency.php?error=pending");
    exit();
}
$stmt->close();

// Generate indigency_id: CI-2025-0001
$year = date('Y');
$prefix = "CI-{$year}-";

// Get the latest ID with the same prefix
$stmt = $conn->prepare("SELECT indigency_id FROM certificate_of_indigency WHERE indigency_id LIKE ? ORDER BY id DESC LIMIT 1");
$searchPrefix = $prefix . '%';
$stmt->bind_param("s", $searchPrefix);
$stmt->execute();
$result = $stmt->get_result();
$latest_id = $result->fetch_assoc();
$stmt->close();

if ($latest_id) {
    $number = (int)substr($latest_id['indigency_id'], -4);
    $new_number = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
} else {
    $new_number = "0001";
}

$indigency_id = $prefix . $new_number;

// Sanitize & get form data
$first_name = trim($_POST['first_name']);
$middle_name = trim($_POST['middle_name']);
$last_name = trim($_POST['last_name']);
$dob = $_POST['dob'];
$birth_place = trim($_POST['birth_place']);
$gender = $_POST['gender'];
$email = $_POST['email'];
$civil_status = $_POST['civil_status'];
$house_no = trim($_POST['house_no']);
$street = trim($_POST['street']);
$purok = trim($_POST['purok']);
$financial_status = $_POST['financial_status'];
$contact_number = trim($_POST['contact_number']);
$signature = trim($_POST['signature']);
$application_date = $_POST['application_date'];

// Validate signature matches full name
$full_name = trim("{$first_name} {$middle_name} {$last_name}");
if (strtolower($signature) !== strtolower($full_name)) {
    $conn->close();
    header("Location: R.indigency.php?error=signature");
    exit();
}

// Optional: Set resident_id if exists (example: if resident is linked via user_id)
$resident_id = null;
$stmt = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resident_result = $stmt->get_result();
if ($resident_row = $resident_result->fetch_assoc()) {
    $resident_id = $resident_row['id'];
}
$stmt->close();

// Insert into database
$stmt = $conn->prepare("INSERT INTO certificate_of_indigency (
    indigency_id, user_id, resident_id, first_name, middle_name, last_name,
    dob, birth_place, gender, civil_status,
    house_no, street, purok, barangay, city, province,
    financial_status, contact_number, signature, application_date, email
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Bagbag', 'Quezon City', 'Metro Manila', ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "siisssssssssssssss",
    $indigency_id,
    $user_id,
    $resident_id,
    $first_name,
    $middle_name,
    $last_name,
    $dob,
    $birth_place,
    $gender,
    $civil_status,
    $house_no,
    $street,
    $purok,
    $financial_status,
    $contact_number,
    $signature,
    $application_date,
    $email
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: R.indigency.php?success=indigency");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: R.indigency.php?error=database");
    exit();
}
?>