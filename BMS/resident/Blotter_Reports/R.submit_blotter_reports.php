<?php
// Start session
session_start();

// I-check kung resident at naka-login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    die("<script>alert('Unauthorized access!'); window.location.href = '../../login/login.php';</script>");
}

// Database connection
include '../../login/db_connect.php';

// I-check kung may error sa connection
if ($conn->connect_error) {
    die("<script>alert('Database connection failed: " . addslashes($conn->connect_error) . "'); window.history.back();</script>");
}

// -------------------------------
// Tanggapin at i-sanitize ang form data
// -------------------------------

// Basic Information
$first_name        = trim($_POST['first_name'] ?? '');
$middle_name       = trim($_POST['middle_name'] ?? '');
$last_name         = trim($_POST['last_name'] ?? '');
$age               = (int)($_POST['age'] ?? 0);
$gender            = trim($_POST['gender'] ?? '');
$address           = trim($_POST['address'] ?? '');
$contact           = trim($_POST['contact'] ?? '');

// Incident Details
$incident_type     = trim($_POST['incident_type'] ?? '');
$incident_date     = trim($_POST['incident_date'] ?? '');
$incident_time     = trim($_POST['incident_time'] ?? '');
$incident_location = trim($_POST['incident_location'] ?? '');
$narrative         = trim($_POST['narrative'] ?? '');

// Involved Party (Optional)
$involved_name     = trim($_POST['involved_name'] ?? '');
$involved_address  = trim($_POST['involved_address'] ?? '');
$relation          = trim($_POST['relation'] ?? '');
$description       = trim($_POST['description'] ?? '');

// Witness (Optional)
$witness_name      = trim($_POST['witness_name'] ?? '');
$witness_contact   = trim($_POST['witness_contact'] ?? '');
$witness_address   = trim($_POST['witness_address'] ?? '');

// Purpose of Blotter (checkboxes)
$purpose_police    = (isset($_POST['purpose']) && in_array('Police Report', $_POST['purpose'])) ? 1 : 0;
$purpose_insurance = (isset($_POST['purpose']) && in_array('Insurance Claim', $_POST['purpose'])) ? 1 : 0;
$purpose_complaint = (isset($_POST['purpose']) && in_array('Complaint', $_POST['purpose'])) ? 1 : 0;
$purpose_record    = (isset($_POST['purpose']) && in_array('For Record Only', $_POST['purpose'])) ? 1 : 0;

// Signature & Date
$signature         = trim($_POST['signature'] ?? '');
$report_date       = trim($_POST['report_date'] ?? '');

// Kunin ang user_id mula sa session
$user_id = $_SESSION['user_id'];

// -------------------------------
// I-validate ang required fields
// -------------------------------
$required_fields = [
    $first_name, $last_name, $age, $gender, $address, $contact,
    $incident_type, $incident_date, $incident_time, $incident_location, $narrative,
    $signature, $report_date
];

foreach ($required_fields as $field) {
    if (empty($field) && $field !== 0) {
        die("<script>alert('All required fields must be filled out.'); window.history.back();</script>");
    }
}

// Age validation
if ($age < 1 || $age > 120) {
    die("<script>alert('Please enter a valid age.'); window.history.back();</script>");
}

// -------------------------------
// I-fetch ang resident_id mula sa residents table base sa user_id
// -------------------------------
$stmt = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
if (!$stmt) {
    die("<script>alert('Prepare failed: " . addslashes($conn->error) . "'); window.history.back();</script>");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    die("<script>alert('No resident record found for this account.'); window.history.back();</script>");
}

$resident_row = $result->fetch_assoc();
$resident_id = $resident_row['id'];
$stmt->close();

// -------------------------------
// GENERATE BLOTTER ID: BR-YYYY-XXXX
// -------------------------------
$year = date('Y', strtotime($report_date)); // Kunin ang taon mula sa report_date
$prefix = "BR-{$year}-";

// Hanapin ang pinakamataas na numero sa taong iyon
$stmt = $conn->prepare("SELECT MAX(blotter_id) as max_id FROM blotter_and_reports WHERE blotter_id LIKE ?");
$searchPattern = $prefix . "%";
$stmt->bind_param("s", $searchPattern);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Extract number from existing max ID
$nextNumber = 1;
if ($row['max_id']) {
    $numberPart = (int)substr($row['max_id'], -4); // Last 4 digits
    $nextNumber = $numberPart + 1;
}

// Buo ng bagong blotter_id: BR-2025-0001
$blotter_id = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

// -------------------------------
// I-insert sa blotter_and_reports table (kasama ang blotter_id)
// -------------------------------
$sql = "INSERT INTO blotter_and_reports (
    user_id, resident_id,
    complainant_first_name, complainant_middle_name, complainant_last_name,
    complainant_age, complainant_gender, complainant_address, complainant_contact,
    incident_type, incident_date, incident_time, incident_location, incident_narrative,
    involved_person_name, involved_person_address, relation_to_complainant, involved_description,
    witness_name, witness_contact, witness_address,
    purpose_police_report, purpose_insurance_claim, purpose_complaint, purpose_record_only,
    electronic_signature, report_date,
    blotter_id
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<script>alert('Prepare failed: " . addslashes($conn->error) . "'); window.history.back();</script>");
}

// ✅ 28 parameters: 7 i's, 21 s's (21 strings: lahat ng text + 1 new blotter_id)
$bindTypes = "iisssisssssssssssssiiiiissss"; // 28 characters

$stmt->bind_param(
    $bindTypes,
    $user_id, $resident_id,
    $first_name, $middle_name, $last_name,
    $age, $gender, $address, $contact,
    $incident_type, $incident_date, $incident_time, $incident_location, $narrative,
    $involved_name, $involved_address, $relation, $description,
    $witness_name, $witness_contact, $witness_address,
    $purpose_police, $purpose_insurance, $purpose_complaint, $purpose_record,
    $signature, $report_date,
    $blotter_id
);

// -------------------------------
// I-execute at i-check kung successful
// -------------------------------
if ($stmt->execute()) {
    echo "<script>
        alert('✅ Success! Your Blotter Report has been submitted.\\n\\nBlotter ID: {$blotter_id}\\n\\nStatus: Pending Review.');
        window.location.href = 'R.blotter_reports.php';
    </script>";
} else {
    $error = addslashes($stmt->error);
    echo "<script>
        alert('❌ Submission failed: $error');
        window.history.back();
    </script>";
}

// -------------------------------
// Close statements and connection
// -------------------------------
$stmt->close();
$conn->close();
?>