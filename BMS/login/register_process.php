<?php
// register_process.php - Fixed version for proper BMS registration

header('Content-Type: application/json');

// Collect input
$fullname = trim($_POST['fullname'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone = preg_replace('/[^0-9]/', '', trim($_POST['phone'] ?? ''));
$dob = $_POST['dob'] ?? '';
$pob = trim($_POST['pob'] ?? '');
$gender = $_POST['gender'] ?? '';
$civil_status = $_POST['civil_status'] ?? '';
$employment_status = $_POST['employment_status'] ?? '';
$nationality = trim($_POST['nationality'] ?? '');
$religion = trim($_POST['religion'] ?? '');
$address = trim($_POST['address'] ?? '');
$resident_type = $_POST['resident_type'] ?? '';
$length_of_stay = !empty($_POST['length_of_stay']) ? (int)$_POST['length_of_stay'] : null;
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
if (empty($fullname)) {
    echo json_encode(['success' => false, 'message' => 'Full name required']);
    exit;
}
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Valid email required']);
    exit;
}
if (strlen($phone) < 10 || strlen($phone) > 11) {
    echo json_encode(['success' => false, 'message' => 'Phone must be 10-11 digits']);
    exit;
}
if (!strtotime($dob)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date of birth']);
    exit;
}
if (empty($pob)) {
    echo json_encode(['success' => false, 'message' => 'Place of birth required']);
    exit;
}
if (!in_array($gender, ['Male', 'Female'])) {
    echo json_encode(['success' => false, 'message' => 'Gender must be Male or Female']);
    exit;
}
if (empty($civil_status)) {
    echo json_encode(['success' => false, 'message' => 'Civil status required']);
    exit;
}
if (empty($nationality)) {
    echo json_encode(['success' => false, 'message' => 'Nationality required']);
    exit;
}
if (empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Address required']);
    exit;
}
if (empty($resident_type)) {
    echo json_encode(['success' => false, 'message' => 'Resident type required']);
    exit;
}
if (empty($employment_status)) {
    echo json_encode(['success' => false, 'message' => 'Employment status required']);
    exit;
}
if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

// ✅ Connect to BMS Database
$conn = new mysqli("localhost:3307", "root", "", "bms");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn->autocommit(FALSE); // Start transaction

    // ✅ 1. Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    throw new Exception("This account has already been created. Reference Number is for one-time use only.");
}
$stmt->close();

    // ✅ 2. Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // ✅ 3. Insert into `users` table
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status, created_at) VALUES (?, ?, ?, 'Resident', 'approved', NOW())");
    $stmt->bind_param("sss", $fullname, $email, $hashed);
    $stmt->execute();
    $user_id = $conn->insert_id; // Get the auto-generated user ID
    $stmt->close();

    // ✅ 4. Calculate age
    $age = (new DateTime($dob))->diff(new DateTime())->y;

    // ✅ 5. Insert into `residents` table
    // 🔴 IMPORTANT: Match the exact column order and number of parameters
    $stmt = $conn->prepare("
        INSERT INTO residents (
            user_id, dob, pob, age, gender, civil_status, nationality, 
            religion, address, phone, resident_type, stay_length, 
            date_registered, employment_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");

    // Bind parameters - make sure the types match
    $stmt->bind_param(
        "sssisssssssss", 
        $user_id, $dob, $pob, $age, $gender, $civil_status, 
        $nationality, $religion, $address, $phone, $resident_type, 
        $length_of_stay, $employment_status
    );

    $stmt->execute();
    $stmt->close();

    // ✅ Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful!',
        'redirect_url' => 'login.php?registered=1'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Registration failed: " . $e->getMessage()); // Log error
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed: ' . $e->getMessage()
    ]);
} finally {
    $conn->autocommit(TRUE);
    $conn->close();
}
?>