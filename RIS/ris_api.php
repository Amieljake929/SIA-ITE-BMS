<?php
// ris_api.php - Updated for validation-only use by BMS

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key");
header("Content-Type: application/json; charset=UTF-8");

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ------ API Key Authentication ------
$secretApiKey = "my-secret-barangay-api-key-123";
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if (empty($apiKey) && function_exists('getallheaders')) {
    $headers = getallheaders();
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'x-api-key') {
            $apiKey = $value;
            break;
        }
    }
}

if ($apiKey !== $secretApiKey) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized access. Invalid API Key."]);
    exit();
}

// Database
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "ris";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}
$conn->set_charset('utf8mb4');

// ------------------------------
// POST: Add new resident (for RIS internal use)
// ------------------------------
function handlePostRequest($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Required fields
    $required_fields = [
        'full_name', 'email', 'dob', 'pob', 'age', 'gender', 'civil_status',
        'nationality', 'address', 'phone', 'resident_type', 'employment_status'
    ];

    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing field: $field"]);
            return;
        }
    }

    // Normalize case
    $data['gender'] = ucfirst(strtolower($data['gender']));
    $data['civil_status'] = ucwords(strtolower(str_replace('_', ' ', $data['civil_status'])));
    $data['resident_type'] = ucwords(strtolower($data['resident_type']));
    $data['employment_status'] = ucwords(strtolower($data['employment_status']));

    // Validate
    if (!in_array($data['gender'], ['Male', 'Female'])) {
        http_response_code(400);
        echo json_encode(["error" => "Gender must be Male or Female"]);
        return;
    }

    $allowed = ['Single', 'Married', 'Widow/Widower', 'Separated', 'Divorced'];
    if (!in_array($data['civil_status'], $allowed)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid civil_status"]);
        return;
    }

    $allowed = ['Permanent', 'Temporary', 'Voter', 'Non-voter'];
    if (!in_array($data['resident_type'], $allowed)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid resident_type"]);
        return;
    }

    $allowed = ['Student', 'Employed', 'Unemployed', 'Self-employed', 'Retired', 'Homemaker', 'Others'];
    if (!in_array($data['employment_status'], $allowed)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid employment_status"]);
        return;
    }

    // Check if email or phone exists
    $check = $conn->prepare("SELECT id FROM registration WHERE email = ? OR phone = ?");
    $check->bind_param("ss", $data['email'], $data['phone']);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Email or phone already registered in RIS."]);
        $check->close();
        return;
    }
    $check->close();

    // Auto-generate ID
    $res = $conn->query("SELECT MAX(id) as max_id FROM registration FOR UPDATE");
    $row = $res->fetch_assoc();
    $lastId = (int)($row['max_id'] ?? '00000000');
    $new_id = str_pad($lastId + 1, 8, '0', STR_PAD_LEFT);

    // Insert
    $sql = "INSERT INTO registration (
        id, full_name, email, dob, pob, age, gender, civil_status,
        nationality, religion, address, phone, resident_type, stay_length,
        employment_status, valid_id_type, valid_id_number, valid_id_image, selfie_with_id,
        is_senior_citizen, is_pwd, is_solo_parent, is_voter, is_student, is_indigenous
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssisssssssisssssiiiiii",
        $new_id,
        $data['full_name'], $data['email'], $data['dob'], $data['pob'], $data['age'],
        $data['gender'], $data['civil_status'], $data['nationality'],
        $data['religion'] ?? null, $data['address'], $data['phone'],
        $data['resident_type'], $data['stay_length'] ?? null, $data['employment_status'],
        $data['valid_id_type'] ?? null, $data['valid_id_number'] ?? null,
        $data['valid_id_image'] ?? null, $data['selfie_with_id'] ?? null,
        $data['is_senior_citizen'] ?? 0, $data['is_pwd'] ?? 0, $data['is_solo_parent'] ?? 0,
        $data['is_voter'] ?? 0, $data['is_student'] ?? 0, $data['is_indigenous'] ?? 0
    );

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Resident registered.", "id" => $new_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Insert failed"]);
    }
    $stmt->close();
}

// ------------------------------
// GET: Validate if resident exists
// ------------------------------
function handleGetRequest($conn) {
    $email = $_GET['email'] ?? '';
    $phone = $_GET['phone'] ?? '';
    $id = $_GET['id'] ?? '';

    if (!$email && !$phone && !$id) {
        http_response_code(400);
        echo json_encode(["error" => "Provide email, phone, or id"]);
        return;
    }

    $sql = "SELECT id, full_name, email, phone FROM registration WHERE ";
    $params = [];
    $types = "";

    if ($id) {
        $sql .= "id = ?";
        $params[] = $id;
        $types .= "s";
    } elseif ($email) {
        $sql .= "email = ?";
        $params[] = $email;
        $types .= "s";
    } else {
        $sql .= "phone = ?";
        $params[] = $phone;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(200);
        echo json_encode($result->fetch_assoc());
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Not found in RIS"]);
    }
    $stmt->close();
}

// Route
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    handlePostRequest($conn);
} elseif ($method === 'GET') {
    handleGetRequest($conn);
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}

$conn->close();
?>