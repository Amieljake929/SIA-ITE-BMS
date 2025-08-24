<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: ../login.php");
    exit();
}

// Database Connection
$conn = new mysqli("localhost:3307", "root", "", "bms");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

$user_id = $_SESSION['user_id'];

// -------------------------------
// Helper: Generate Business Permit ID (BP-YYYY-0001)
// -------------------------------
function generate_permit_id($conn) {
    $year = date('Y');
    $query = "SELECT business_permit_id FROM business_permit WHERE business_permit_id LIKE ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $like_param = "BP-$year-%";
    $stmt->bind_param("s", $like_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_id = $result->num_rows > 0 ? $result->fetch_assoc()['business_permit_id'] : null;
    $stmt->close();

    if ($last_id) {
        $number = (int)substr($last_id, -4);
        $new_number = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $new_number = '0001';
    }

    return "BP-$year-$new_number";
}

// -------------------------------
// Helper: Upload File
// -------------------------------
function uploadFile($file, $target_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $filename = basename($file['name']);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'gif'];

    if (!in_array($extension, $allowed)) {
        return false;
    }

    // Prevent large files (optional: max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    // Generate unique filename
    $new_filename = 'permit_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $target_path = $target_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $new_filename;
    }

    return false;
}

// -------------------------------
// Define Upload Directory (Relative to this script)
// -------------------------------
$upload_dir = 'images/'; // This is: resident/Business_Permit/images/
$full_upload_path = __DIR__ . '/' . $upload_dir;

// Create directory if it doesn't exist
if (!is_dir($full_upload_path)) {
    if (!mkdir($full_upload_path, 0777, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create upload directory. Please check folder permissions.'
        ]);
        exit;
    }
}

// Ensure directory is writable
if (!is_writable($full_upload_path)) {
    echo json_encode([
        'success' => false,
        'message' => 'Upload directory is not writable. Please check permissions.'
    ]);
    exit;
}

// -------------------------------
// Collect and Sanitize Input
// -------------------------------
$first_name = trim($_POST['first_name'] ?? '');
$middle_name = trim($_POST['middle_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$address = trim($_POST['address'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$business_name = trim($_POST['business_name'] ?? '');
$business_address = trim($_POST['business_address'] ?? '');
$business_nature = trim($_POST['business_nature'] ?? '');
$ownership_form = $_POST['ownership_form'] ?? '';
$registration_number = trim($_POST['registration_number'] ?? '');
$tin = trim($_POST['tin'] ?? '');
$employees = max(0, (int)($_POST['employees'] ?? 0));
$capitalization = (float)($_POST['capitalization'] ?? 0);
$operation_date = $_POST['operation_date'] ?? '';
$signature = trim($_POST['signature'] ?? '');
$application_date = $_POST['application_date'] ?? '';

// -------------------------------
// Validation
// -------------------------------
if (empty($first_name) || empty($last_name) || empty($address) || empty($contact_number) ||
    empty($business_name) || empty($business_address) || empty($business_nature) ||
    empty($ownership_form) || empty($tin) || empty($operation_date) || empty($signature) ||
    empty($application_date)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill out all required fields.'
    ]);
    exit;
}

if ($capitalization <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Capitalization must be greater than zero.'
    ]);
    exit;
}

// Signature validation
$full_name_parts = array_filter([$first_name, $middle_name, $last_name], 'strlen');
$full_name = strtolower(implode(' ', $full_name_parts));
$input_signature = strtolower(trim($signature));
$full_name_clean = preg_replace('/\s+/', ' ', $full_name);
$signature_clean = preg_replace('/\s+/', ' ', $input_signature);

if ($full_name_clean !== $signature_clean) {
    echo json_encode([
        'success' => false,
        'message' => 'Signature must match your full name exactly: "' . htmlspecialchars(ucwords($full_name)) . '"'
    ]);
    exit;
}

// -------------------------------
// Upload Attachments
// -------------------------------
$valid_id_front = uploadFile($_FILES['valid_id_front'], $full_upload_path);
$proof_of_address = uploadFile($_FILES['proof_of_address'], $full_upload_path);
$cedula = uploadFile($_FILES['cedula'], $full_upload_path);

// Optional: previous clearance
$previous_clearance = isset($_FILES['previous_clearance']) && $_FILES['previous_clearance']['error'] === UPLOAD_ERR_OK
    ? uploadFile($_FILES['previous_clearance'], $full_upload_path)
    : null;

// Validate required uploads
if (!$valid_id_front) {
    echo json_encode(['success' => false, 'message' => 'Failed to upload Valid ID (Front).']);
    exit;
}
if (!$proof_of_address) {
    echo json_encode(['success' => false, 'message' => 'Failed to upload Proof of Business Address.']);
    exit;
}
if (!$cedula) {
    echo json_encode(['success' => false, 'message' => 'Failed to upload Community Tax Certificate (Cedula).']);
    exit;
}

// -------------------------------
// Get resident_id (if exists)
// -------------------------------
$resident_id = null;
$stmt = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $resident_id = $row['id'];
}
$stmt->close();

// -------------------------------
// Generate Business Permit ID
// -------------------------------
$business_permit_id = generate_permit_id($conn);

// -------------------------------
// Insert into business_permit table
// -------------------------------
$stmt = $conn->prepare("
    INSERT INTO business_permit (
        user_id, 
        resident_id, 
        business_permit_id,
        first_name, 
        middle_name, 
        last_name, 
        address, 
        contact_number,
        business_name, 
        business_address, 
        business_nature, 
        ownership_form,
        registration_number, 
        tin, 
        number_of_employees, 
        capitalization,
        operation_start_date, 
        valid_id_front, 
        proof_of_address,
        previous_clearance, 
        cedula, 
        signature, 
        application_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissssssssssssisidsssss",
    $user_id,
    $resident_id,
    $business_permit_id,
    $first_name,
    $middle_name,
    $last_name,
    $address,
    $contact_number,
    $business_name,
    $business_address,
    $business_nature,
    $ownership_form,
    $registration_number,
    $tin,
    $employees,
    $capitalization,
    $operation_date,
    $valid_id_front,
    $proof_of_address,
    $previous_clearance,
    $cedula,
    $signature,
    $application_date
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Your business permit application has been submitted successfully!',
        'redirect_url' => 'R.business_permit.php?permit_submitted=1'
    ]);
} else {
    error_log("Business Permit Insert Error: " . $stmt->error);
    echo json_encode([
        'success' => false,
        'message' => 'Something went wrong. Please try again later.'
    ]);
}

$stmt->close();
$conn->close();
?>