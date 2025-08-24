<?php
// register_process.php - Validate only via RIS

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
    echo json_encode(['success' => false, 'message' => 'Invalid DOB']);
    exit;
}
if (empty($pob)) {
    echo json_encode(['success' => false, 'message' => 'POB required']);
    exit;
}
if (!in_array($gender, ['Male', 'Female'])) {
    echo json_encode(['success' => false, 'message' => 'Gender must be Male or Female']);
    exit;
}
// Add other validations...

// Check if user exists in RIS
$ris_api_url = "http://localhost/ITE-SIA/RIS/ris_api.php";
$ris_api_key = "my-secret-barangay-api-key-123";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$ris_api_url?email=" . urlencode($email));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: ' . $ris_api_key]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode([
        'success' => false,
        'message' => 'You are not registered in the Barangay RIS. Please contact the office.'
    ]);
    exit;
}

// Register in BMS
$conn = new mysqli("localhost:3307", "root", "", "bms");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'BMS connection failed']);
    exit;
}

$conn->autocommit(FALSE);
try {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt1 = $conn->prepare("INSERT INTO users (full_name, email, password, role, status, created_at) VALUES (?, ?, ?, 'Resident', 'approved', NOW())");
    $stmt1->bind_param("sss", $fullname, $email, $hashed);
    $stmt1->execute();
    $user_id = $conn->insert_id;
    $stmt1->close();

    $age = (new DateTime($dob))->diff(new DateTime())->y;
    $stmt2 = $conn->prepare("INSERT INTO residents (user_id, dob, pob, age, gender, civil_status, nationality, religion, address, phone, resident_type, stay_length, date_registered, employment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    $stmt2->bind_param("sssisssssssss", $user_id, $dob, $pob, $age, $gender, $civil_status, $nationality, $religion, $address, $phone, $resident_type, $length_of_stay, $employment_status);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful!',
        'redirect_url' => 'login.php?registered=1'
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
} finally {
    $conn->autocommit(TRUE);
    $conn->close();
}
?>