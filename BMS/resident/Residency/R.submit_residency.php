<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';

// Check connection
if ($conn->connect_error) {
    // Mas magandang error handling ito
    header("Location: ../R.residency.php?error=db_connect");
    exit();
}

// --- (SIMULA NG PAGBABAGO) ---

// Kunin ang user_id mula sa session
$user_id = $_SESSION['user_id'];

// Laging i-fetch ang resident_id mula sa residents table base sa user_id
// (Gaya ng ginawa mo sa clearance script)
$stmt_check = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
if (!$stmt_check) {
    // Nag-fail ang pag-prepare ng query
    header("Location: ../R.residency.php?error=db_prepare");
    exit();
}

$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    // Walang nahanap na resident record para sa user na 'to
    header("Location: ../R.residency.php?error=no_resident_record");
    exit();
}

$resident_row = $result_check->fetch_assoc();
$resident_id = $resident_row['id']; // <-- Ito na ang TAMA at VALID na ID
$stmt_check->close();

// (Optional) I-update ang session para tama na rin
$_SESSION['resident_id'] = $resident_id;

// --- (TAPOS NG PAGBABAGO) ---


// --- GENERATE residency_id in PHP ---
$year = date('Y');
$prefix = "CR-{$year}-";

// Hanapin ang pinakabagong numero sa taong ito
// Note: Mas maganda kung ang $stmt ay iba sa $stmt_check sa taas, 
// kaya papalitan ko ang pangalan nito to $stmt_idgen
$stmt_idgen = $conn->prepare("SELECT MAX(residency_id) FROM certificate_of_residency WHERE residency_id LIKE ?");
$search = $prefix . "%";
$stmt_idgen->bind_param("s", $search);
$stmt_idgen->execute();
$result = $stmt_idgen->get_result();
$row = $result->fetch_row();
$stmt_idgen->close(); // Isara agad pagkatapos gamitin

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
// (Gumamit ng real_escape_string para sa extra security sa text inputs)
$first_name = $conn->real_escape_string($_POST['first_name'] ?? '');
$middle_name = $conn->real_escape_string($_POST['middle_name'] ?? '');
$last_name = $conn->real_escape_string($_POST['last_name'] ?? '');
$dob = $_POST['dob'] ?? '';
$birth_place = $conn->real_escape_string($_POST['birth_place'] ?? '');
$gender = $_POST['gender'] ?? '';
$email = $conn->real_escape_string($_POST['email'] ?? '');
$civil_status = $_POST['civil_status'] ?? '';
$street = $conn->real_escape_string($_POST['street'] ?? '');
$barangay = "Bagbag";
$city = "Quezon City";
$province = "Metro Manila";
$purpose = $conn->real_escape_string($_POST['purpose'] ?? '');
$contact_number = $conn->real_escape_string($_POST['contact_number'] ?? '');
$signature = $_POST['signature'] ?? ''; // Siguraduhin na safe ito (e.g., base64 data)
$application_date = $_POST['application_date'] ?? date('Y-m-d');

// --- Insert to Database ---
$stmt_insert = $conn->prepare("
    INSERT INTO certificate_of_residency 
    (residency_id, user_id, resident_id, first_name, middle_name, last_name, 
     dob, birth_place, gender, civil_status, street, 
     barangay, city, province, purpose, contact_number, signature, application_date, email) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt_insert->bind_param(
    "siissssssssssssssss",
    $residency_id,
    $user_id,
    $resident_id, // <-- Siguradong valid na ito
    $first_name,
    $middle_name,
    $last_name,
    $dob,
    $birth_place,
    $gender,
    $civil_status,
    $street,
    $barangay,
    $city,
    $province,
    $purpose,
    $contact_number,
    $signature,
    $application_date,
    $email
);

if ($stmt_insert->execute()) {
    // Success
    header("Location: R.residency.php?success=residency");
    exit();
} else {
    // Nagka-error sa pag-insert
    // Pwede mo i-log ang error: error_log($stmt_insert->error);
    header("Location: ../R.residency.php?error=database_insert_failed");
    exit();
}

$stmt_insert->close();
$conn->close();
?>