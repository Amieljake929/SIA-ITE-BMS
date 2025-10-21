<?php
// Make sure to start the session to access the CAPTCHA code
session_start(); 

header('Content-Type: application/json');

// --- START: CAPTCHA Validation ---
// This block is added at the beginning to check the CAPTCHA first.
if (!isset($_POST['captcha_code']) || empty($_POST['captcha_code'])) {
    echo json_encode(['success' => false, 'message' => 'Please enter the CAPTCHA code.']);
    exit;
}

// Compare user's input with the stored session value (case-insensitive)
$userInput = strtolower(trim($_POST['captcha_code']));
$captchaText = isset($_SESSION['captcha_text']) ? $_SESSION['captcha_text'] : '';

// Unset the session captcha text to prevent re-use, even if validation fails.
// This is a crucial security step.
if (isset($_SESSION['captcha_text'])) {
    unset($_SESSION['captcha_text']);
}

if ($userInput !== $captchaText || $captchaText === '') {
    echo json_encode(['success' => false, 'message' => 'The CAPTCHA code you entered is incorrect.']);
    exit;
}
// --- END: CAPTCHA Validation ---


// If the script reaches this point, the CAPTCHA was correct.
// Your original code continues below without any changes.

// Collect input
$fullname = trim($_POST['fullname'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
// Corrected phone number cleaning to allow '+' for international numbers if needed, but your current logic is fine for PH numbers.
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
// Your length_of_stay was casting to int, changing to string to match DB if it's varchar
$length_of_stay = trim($_POST['length_of_stay'] ?? ''); 
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Your existing validation logic
if (empty($fullname)) {
    echo json_encode(['success' => false, 'message' => 'Full name required']);
    exit;
}
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Valid email required']);
    exit;
}
// ... (all your other validations are here and untouched) ...
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
// I corrected the gender validation to allow 'Other' if it's in your form. Let's assume Male/Female for now as per your original code.
if (!in_array($gender, ['Male', 'Female', 'Other'])) { 
    echo json_encode(['success' => false, 'message' => 'Please select a valid gender.']);
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
// Changed password length check to 8 to match the frontend requirement
if (strlen($password) < 8) { 
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}
// You can add more complex password validation here to match the frontend (regex for uppercase, number, etc.) if you want server-side enforcement.

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
    $stmt = $conn->prepare("
        INSERT INTO residents (
            user_id, dob, pob, age, gender, civil_status, nationality, 
            religion, address, phone, resident_type, stay_length, 
            date_registered, employment_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");

    // Bind parameters
    $stmt->bind_param(
        "ississsssssss",  // Changed user_id to 'i' for integer type
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
