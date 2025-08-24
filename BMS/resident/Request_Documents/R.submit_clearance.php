<?php
// Start session
session_start();

// I-check kung resident at naka-login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    die("<script>alert('Unauthorized access!'); window.location.href = '../../login/login.php';</script>");
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "bms");

// I-check kung may error sa connection
if ($conn->connect_error) {
    die("<script>alert('Database connection failed: " . addslashes($conn->connect_error) . "'); window.history.back();</script>");
}

// Tanggapin ang form data
$first_name = $conn->real_escape_string($_POST['first_name']);
$middle_name = $conn->real_escape_string($_POST['middle_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$dob = $_POST['dob'];
$age = (int)$_POST['age'];
$gender = $conn->real_escape_string($_POST['gender']);
$civil_status = $conn->real_escape_string($_POST['civil_status']);
$nationality = $conn->real_escape_string($_POST['nationality']);

$house_no = $conn->real_escape_string($_POST['house_no']);
$street = $conn->real_escape_string($_POST['street']);
$purok = $conn->real_escape_string($_POST['purok']);
$residency_years = (int)$_POST['residency_years'];

$id_type = $conn->real_escape_string($_POST['id_type']);
$id_number = $conn->real_escape_string($_POST['id_number']);
$contact_number = $conn->real_escape_string($_POST['contact_number']);

$purpose = $conn->real_escape_string($_POST['purpose']);
$signature = $conn->real_escape_string($_POST['signature']);
$application_date = $conn->real_escape_string($_POST['application_date']);

// Kunin ang user_id mula sa session
$user_id = $_SESSION['user_id'];

// I-fetch ang resident_id mula sa residents table base sa user_id
$stmt = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<script>alert('No resident record found for this account.'); window.history.back();</script>");
}

$resident_row = $result->fetch_assoc();
$resident_id = $resident_row['id'];
$stmt->close();

// I-insert sa barangay_clearance table
$stmt = $conn->prepare("
    INSERT INTO barangay_clearance (
        resident_id, user_id, first_name, middle_name, last_name, dob, age, gender,
        civil_status, nationality, house_no, street, purok, residency_years,
        id_type, id_number, contact_number, purpose, signature, application_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisssisssssssissssss",
    $resident_id, $user_id, $first_name, $middle_name, $last_name, $dob, $age, $gender,
    $civil_status, $nationality, $house_no, $street, $purok, $residency_years,
    $id_type, $id_number, $contact_number, $purpose, $signature, $application_date
);

// I-execute at i-check kung successful
if ($stmt->execute()) {
    // ✅ Success alert
    echo "<script>
        alert('✅ Success! Your Barangay Clearance request has been submitted.');
        window.location.href = 'R.barangay_clearance.php';
    </script>";
} else {
    // ❌ Error alert
    echo "<script>
        alert('❌ Error: " . addslashes($stmt->error) . "');
        window.history.back();
    </script>";
}

// Close statements and connection
$stmt->close();
$conn->close();
?>