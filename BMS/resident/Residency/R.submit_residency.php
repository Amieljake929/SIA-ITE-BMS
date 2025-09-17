<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';


$user_id = $_SESSION['user_id'];

// Get resident_id (halimbawa, naka-save sa session o kukunin sa DB)
$resident_id = $_SESSION['resident_id'] ?? null;

// Optional: Kung walang session, kunin mula sa DB
if (!$resident_id) {
    $stmt = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $resident = $result->fetch_assoc();
    if (!$resident) {
        die("Error: Resident record not found.");
    }
    $resident_id = $resident['id'];
    $_SESSION['resident_id'] = $resident_id;
}

// --- GENERATE residency_id in PHP ---
$year = date('Y');
$prefix = "CR-{$year}-";

// Hanapin ang pinakabagong numero sa taong ito
$stmt = $conn->prepare("SELECT MAX(residency_id) FROM certificate_of_residency WHERE residency_id LIKE ?");
$search = $prefix . "%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();

$last_id = $row[0]; // e.g., CR-2025-0003

if ($last_id && preg_match("/CR-(\d{4})-(\d{4})/", $last_id, $matches)) {
    $last_year = $matches[1];
    $last_num = (int)$matches[2];

    if ($last_year == $year) {
        $next_num = $last_num + 1;
    } else {
        $next_num = 1; // Reset kapag bagong taon
    }
} else {
    $next_num = 1; // Simula kung wala pa
}

$residency_id = $prefix . str_pad($next_num, 4, '0', STR_PAD_LEFT);
// Result: CR-2025-0001, CR-2025-0002, etc.

// --- Collect Form Data ---
$first_name = $_POST['first_name'] ?? '';
$middle_name = $_POST['middle_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$dob = $_POST['dob'] ?? '';
$birth_place = $_POST['birth_place'] ?? '';
$gender = $_POST['gender'] ?? '';
$email = $_POST['email'] ?? '';
$civil_status = $_POST['civil_status'] ?? '';
$house_no = $_POST['house_no'] ?? '';
$street = $_POST['street'] ?? '';
$purok = $_POST['purok'] ?? '';
$barangay = "Bagbag";
$city = "Quezon City";
$province = "Metro Manila";
$purpose = $_POST['purpose'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$signature = $_POST['signature'] ?? '';
$application_date = $_POST['application_date'] ?? date('Y-m-d');

// --- Insert to Database ---
$stmt = $conn->prepare("
    INSERT INTO certificate_of_residency 
    (residency_id, user_id, resident_id, first_name, middle_name, last_name, 
     dob, birth_place, gender, civil_status, house_no, street, purok, 
     barangay, city, province, purpose, contact_number, signature, application_date, email) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "siissssssssssssssssss",
    $residency_id,
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
    $barangay,
    $city,
    $province,
    $purpose,
    $contact_number,
    $signature,
    $application_date,
    $email
);

if ($stmt->execute()) {
    // Success
    header("Location: R.residency.php?success=residency");
    exit();
} else {
    header("Location: ../R.residency.php?error=database");
    exit();
}

$stmt->close();
$conn->close();
?>