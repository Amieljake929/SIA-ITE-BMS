<?php
// ris_registration_process.php

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "ris"); // 👈 Change to your DB name

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset('utf8mb4');

// Upload directory
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die("Failed to create upload directory.");
    }
}

$valid_id_image = null;
$selfie_with_id = null;

// Handle valid ID image upload
if (!empty($_FILES['valid_id_image']['name']) && $_FILES['valid_id_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['valid_id_image']['tmp_name'];
    $fileName = basename($_FILES['valid_id_image']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    if (in_array($fileExt, $allowed)) {
        $safeName = "valid_id_" . time() . "_" . uniqid() . "." . $fileExt;
        $targetPath = $uploadDir . $safeName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $valid_id_image = "uploads/" . $safeName;
        } else {
            error_log("Upload failed: valid_id_image - $safeName");
        }
    }
}

// Handle selfie with ID upload
if (!empty($_FILES['selfie_with_id']['name']) && $_FILES['selfie_with_id']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['selfie_with_id']['tmp_name'];
    $fileName = basename($_FILES['selfie_with_id']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png'];
    if (in_array($fileExt, $allowed)) {
        $safeName = "selfie_" . time() . "_" . uniqid() . "." . $fileExt;
        $targetPath = $uploadDir . $safeName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $selfie_with_id = "uploads/" . $safeName;
        } else {
            error_log("Upload failed: selfie_with_id - $safeName");
        }
    }
}

// Checkbox values
$is_senior_citizen = isset($_POST['is_senior_citizen']) ? 1 : 0;
$is_pwd            = isset($_POST['is_pwd']) ? 1 : 0;
$is_solo_parent    = isset($_POST['is_solo_parent']) ? 1 : 0;
$is_voter          = isset($_POST['is_voter']) ? 1 : 0;
$is_student        = isset($_POST['is_student']) ? 1 : 0;
$is_indigenous     = isset($_POST['is_indigenous']) ? 1 : 0;

// Sanitize input
$full_name         = trim($conn->real_escape_string($_POST['full_name'] ?? ''));
$email             = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$dob               = $conn->real_escape_string($_POST['dob'] ?? '');
$pob               = trim($conn->real_escape_string($_POST['pob'] ?? ''));
$age               = (int)($_POST['age'] ?? 0);
$gender            = $conn->real_escape_string($_POST['gender'] ?? '');
$civil_status      = $conn->real_escape_string($_POST['civil_status'] ?? '');
$nationality       = trim($conn->real_escape_string($_POST['nationality'] ?? ''));
$religion          = !empty(trim($_POST['religion'] ?? '')) ? trim($conn->real_escape_string($_POST['religion'])) : null;
$address           = trim($conn->real_escape_string($_POST['address'] ?? ''));
$phone             = trim($conn->real_escape_string($_POST['phone'] ?? ''));
$resident_type     = $conn->real_escape_string($_POST['resident_type'] ?? '');
$stay_length       = isset($_POST['stay_length']) && $_POST['stay_length'] !== '' ? (int)$_POST['stay_length'] : null;
$employment_status = $conn->real_escape_string($_POST['employment_status'] ?? '');
$valid_id_type     = !empty(trim($_POST['valid_id_type'] ?? '')) ? trim($conn->real_escape_string($_POST['valid_id_type'])) : null;
$valid_id_number   = !empty(trim($_POST['valid_id_number'] ?? '')) ? trim($conn->real_escape_string($_POST['valid_id_number'])) : null;

// Validation: Required fields
if (!$email || !$full_name || !$dob || !$pob || !$gender || !$civil_status || !$nationality || !$address || !$phone || !$employment_status || !$resident_type) {
    header("Location: ris_registration_form.php?error=" . urlencode("Please fill in all required fields."));
    exit;
}

// Check if email already exists
$stmt_check = $conn->prepare("SELECT id FROM registration WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    header("Location: ris_registration_form.php?error=" . urlencode("Email already registered."));
    exit;
}
$stmt_check->close();

// ---- Generate next 8-digit ID safely (transaction + row lock) ----
$conn->begin_transaction();

$lastId = 0;
$res = $conn->query("SELECT id FROM registration ORDER BY id DESC LIMIT 1 FOR UPDATE");
if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    $lastId = (int)$row['id'];
}
$new_id = str_pad($lastId + 1, 8, '0', STR_PAD_LEFT);
if ($new_id === '00000000') $new_id = '00000001';

// Prepare INSERT statement
$sql = "INSERT INTO registration (
    id, full_name, email, dob, pob, age, gender, civil_status,
    nationality, religion, address, phone, resident_type, stay_length,
    employment_status, valid_id_type, valid_id_number, valid_id_image, selfie_with_id,
    is_senior_citizen, is_pwd, is_solo_parent, is_voter, is_student, is_indigenous
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $conn->rollback();
    header("Location: ris_registration_form.php?error=" . urlencode("Prepare failed: " . $conn->error));
    exit;
}

$stmt->bind_param(
    "sssssisssssssisssssiiiiii",
    $new_id, $full_name, $email, $dob, $pob, $age, $gender, $civil_status,
    $nationality, $religion, $address, $phone, $resident_type, $stay_length,
    $employment_status, $valid_id_type, $valid_id_number, $valid_id_image, $selfie_with_id,
    $is_senior_citizen, $is_pwd, $is_solo_parent, $is_voter, $is_student, $is_indigenous
);

// Execute and redirect
if ($stmt->execute()) {
    $conn->commit();
    header("Location: ris_registration_form.php?success=" . urlencode("Registration successful! ID: $new_id"));
} else {
    $conn->rollback();
    error_log("Insert failed: " . $stmt->error);
    header("Location: ris_registration_form.php?error=" . urlencode("Database error: " . $stmt->error));
}

$stmt->close();
$conn->close();
exit;
?>