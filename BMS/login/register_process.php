<?php
// register_process.php

// Set JSON header para sa AJAX response
header('Content-Type: application/json');

// Database Connection (gaya ng ginagamit mo)
$conn = new mysqli("localhost:3307", "root", "", "bms");

// I-check kung may connection error
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}

// -------------------------------
// Collect and Sanitize Input
// -------------------------------
$fullname = trim($_POST['fullname'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: null; // optional
$phone = trim($_POST['phone'] ?? '');
$dob = $_POST['dob'] ?? '';
$pob = trim($_POST['pob'] ?? '');
$gender = $_POST['gender'] ?? '';
$civil_status = $_POST['civil_status'] ?? '';
$employment_status = $_POST['employment_status'] ?? '';
$nationality = trim($_POST['nationality'] ?? '');
$religion = trim($_POST['religion'] ?? '');
$address = trim($_POST['address'] ?? '');
$resident_type = $_POST['resident_type'] ?? '';
$length_of_stay = $_POST['length_of_stay'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// -------------------------------
// Validation
// -------------------------------
if (empty($fullname) || empty($phone) || empty($dob) || empty($pob) || empty($gender) ||
    empty($civil_status) || empty($employment_status) || empty($nationality) || empty($address) ||
    empty($resident_type) || empty($length_of_stay) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required.'
    ]);
    exit;
}

if ($password !== $confirm_password) {
    echo json_encode([
        'success' => false,
        'message' => 'Passwords do not match.'
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 6 characters.'
    ]);
    exit;
}

// Check if email is already taken (if provided)
if ($email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists.'
        ]);
        $stmt->close();
        exit;
    }
    $stmt->close();
}

// Check if phone number is already registered
$stmt = $conn->prepare("SELECT user_id FROM residents WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Phone number is already registered.'
    ]);
    $stmt->close();
    exit;
}
$stmt->close();

// Calculate age from DOB
$dob_obj = new DateTime($dob);
$today = new DateTime();
$age = $dob_obj->diff($today)->y;

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Start transaction (simulate)
$conn->autocommit(FALSE);

try {
    // Insert into `users` table
    // ✅ Updated: status = 'approved' (instead of 'active')
    $stmt1 = $conn->prepare("
        INSERT INTO users (full_name, email, password, role, status, created_at) 
        VALUES (?, ?, ?, 'Resident', 'approved', NOW())
    ");
    $stmt1->bind_param("sss", $fullname, $email, $hashed_password);

    if (!$stmt1->execute()) {
        throw new Exception("Failed to create user.");
    }

    $user_id = $conn->insert_id;
    $stmt1->close();

    // Insert into `residents` table
    $stmt2 = $conn->prepare("
        INSERT INTO residents (
            user_id, dob, pob, age, gender, civil_status, nationality, religion, 
            address, phone, resident_type, stay_length, date_registered, employment_status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?
        )
    ");

    $stmt2->bind_param(
        "sssisssssssss",
        $user_id, $dob, $pob, $age, $gender, $civil_status, $nationality,
        $religion, $address, $phone, $resident_type, $length_of_stay, $employment_status
    );

    if (!$stmt2->execute()) {
        throw new Exception("Failed to save resident details.");
    }

    $stmt2->close();

    // Commit (kung walang error)
    $conn->commit();

    // Success Response
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful!',
        'redirect_url' => 'login.php?registered=1'
    ]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("Registration Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Something went wrong. Please try again.'
    ]);
}

// Restore autocommit
$conn->autocommit(TRUE);
$conn->close();
?>