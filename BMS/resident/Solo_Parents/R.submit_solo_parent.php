<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';


$user_id = $_SESSION['user_id'];

// Check pending request
$stmt = $conn->prepare("SELECT 1 FROM solo_parents WHERE user_id = ? AND status = 'Pending' LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    $conn->close();
    header("Location: R.solo_parent.php?error=pending");
    exit();
}
$stmt->close();

// Generate solo_parent_id: SPC-2025-0001
$year = date('Y');
$prefix = "SPC-{$year}-";
$stmt = $conn->prepare("SELECT solo_parent_id FROM solo_parents WHERE solo_parent_id LIKE ? ORDER BY id DESC LIMIT 1");
$searchPrefix = $prefix . '%';
$stmt->bind_param("s", $searchPrefix);
$stmt->execute();
$result = $stmt->get_result();
$latest_id = $result->fetch_assoc();
$stmt->close();

if ($latest_id) {
    $number = (int)substr($latest_id['solo_parent_id'], -4);
    $new_number = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
} else {
    $new_number = "0001";
}
$solo_parent_id = $prefix . $new_number;

// Get parent data
$first_name = trim($_POST['first_name']);
$middle_name = trim($_POST['middle_name']);
$last_name = trim($_POST['last_name']);
$house_no = trim($_POST['house_no']);
$street = trim($_POST['street']);
$purok = trim($_POST['purok']);
$email = trim($_POST['email']);
$occupation = trim($_POST['occupation']);
$monthly_income = !empty($_POST['monthly_income']) ? floatval($_POST['monthly_income']) : null;
$category = $_POST['category'];
$contact_number = trim($_POST['contact_number']);
$application_date = $_POST['application_date'];
$signature = trim($_POST['signature']);

// Validate signature
$full_name = trim("{$first_name} {$middle_name} {$last_name}");
if (strtolower($signature) !== strtolower($full_name)) {
    $conn->close();
    header("Location: R.solo_parent.php?error=signature");
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

// Insert into solo_parents
$stmt = $conn->prepare("INSERT INTO solo_parents (
    solo_parent_id, user_id, resident_id, first_name, middle_name, last_name,
    house_no, street, purok, occupation, monthly_income, category,
    contact_number, application_date, email
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "siissssssssssss",
    $solo_parent_id, $user_id, $resident_id,
    $first_name, $middle_name, $last_name,
    $house_no, $street, $purok, $occupation, $monthly_income, $category,
    $contact_number, $application_date, $email
);

if (!$stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: R.solo_parent.php?error=database");
    exit();
}

$parent_id = $stmt->insert_id;
$stmt->close();

// Insert children
$child_first_names = $_POST['child_first_name'];
$child_middle_names = $_POST['child_middle_name'];
$child_last_names = $_POST['child_last_name'];
$child_dobs = $_POST['child_dob'];
$child_civil_statuses = $_POST['child_civil_status'];

$stmt = $conn->prepare("INSERT INTO solo_parent_children (
    parent_id, child_first_name, child_middle_name, child_last_name, child_dob, child_civil_status
) VALUES (?, ?, ?, ?, ?, ?)");

for ($i = 0; $i < count($child_first_names); $i++) {
    $fname = trim($child_first_names[$i]);
    $mname = trim($child_middle_names[$i]);
    $lname = trim($child_last_names[$i]);
    $dob = $child_dobs[$i];
    $cstatus = $child_civil_statuses[$i];

    if (!empty($fname) && !empty($lname) && !empty($dob) && !empty($cstatus)) {
        $stmt->bind_param("isssss", $parent_id, $fname, $mname, $lname, $dob, $cstatus);
        $stmt->execute();
    }
}
$stmt->close();
$conn->close();

header("Location: R.solo_parent_certificate.php?success=solo_parent");
exit();
?>