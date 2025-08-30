<?php
// ris_api.php - Final version with is_registered check

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key");
header("Content-Type: application/json; charset=UTF-8");

// Handle OPTIONS (preflight)
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

    // Auto-generate internal ID (00000001, etc.)
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
        echo json_encode(["error" => "Insert failed: " . $stmt->error]);
    }
    $stmt->close();
}

// ------------------------------
// GET: Validate if resident exists by reference_number, resident_id, email, or phone
// ------------------------------
function handleGetRequest($conn) {
    $email = $_GET['email'] ?? '';
    $phone = $_GET['phone'] ?? '';
    $id = $_GET['id'] ?? '';        // 8-digit resident_id (e.g., 25000001)
    $ref = $_GET['ref'] ?? '';      // Reference Number (e.g., ABC12-XYZ34-5MN6)

    if (!$email && !$phone && !$id && !$ref) {
        http_response_code(400);
        echo json_encode(["error" => "Provide email, phone, id, or ref"]);
        return;
    }

    // Case 1: Lookup by reference_number
    if ($ref) {
        $stmt = $conn->prepare("
            SELECT r.full_name, r.email, r.dob, r.phone, r.pob, r.gender, 
                   r.civil_status, r.nationality, r.religion, r.address, 
                   r.resident_type, r.stay_length, r.employment_status
            FROM registration r
            JOIN residents_id rid ON r.id = rid.registration_id
            WHERE rid.reference_number = ? AND r.status = 'approved'
        ");
        $stmt->bind_param("s", $ref);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Invalid or unapproved reference number."]);
            $stmt->close();
            return;
        }

        $data = $result->fetch_assoc();
        $stmt->close();

        // Check if already registered in BMS
        $bms_conn = new mysqli("localhost:3307", "root", "", "bms");
        if ($bms_conn->connect_error) {
            http_response_code(500);
            echo json_encode(["error" => "BMS connection failed"]);
            return;
        }

        $check = $bms_conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $data['email']);
        $check->execute();
        $is_registered = $check->get_result()->num_rows > 0;
        $check->close();
        $bms_conn->close();

        // Add flag
        $data['is_registered'] = $is_registered;

        http_response_code(200);
        echo json_encode($data);
        return;
    }

    // Case 2: Lookup by resident_id (25000001)
    if ($id) {
        $stmt = $conn->prepare("SELECT registration_id FROM residents_id WHERE resident_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Resident ID not found in RIS (not approved yet)."]);
            $stmt->close();
            return;
        }

        $row = $result->fetch_assoc();
        $registration_id = $row['registration_id'];

        $stmt2 = $conn->prepare("
            SELECT full_name, email, dob, phone, pob, gender, civil_status, 
                   nationality, religion, address, resident_type, stay_length, employment_status
            FROM registration 
            WHERE id = ? AND status = 'approved'
        ");
        $stmt2->bind_param("s", $registration_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "No approved registration found."]);
            $stmt2->close();
            return;
        }

        $data = $result2->fetch_assoc();

        // Check if already registered in BMS
        $bms_conn = new mysqli("localhost:3307", "root", "", "bms");
        if ($bms_conn->connect_error) {
            http_response_code(500);
            echo json_encode(["error" => "BMS connection failed"]);
            return;
        }

        $check = $bms_conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $data['email']);
        $check->execute();
        $is_registered = $check->get_result()->num_rows > 0;
        $check->close();
        $bms_conn->close();

        $data['is_registered'] = $is_registered;

        http_response_code(200);
        echo json_encode($data);
        $stmt2->close();
        $stmt->close();
        return;
    }

    // Case 3: Fallback to email or phone
    $sql = "SELECT id, full_name, email, phone, dob FROM registration WHERE ";
    $params = [];
    $types = "";

    if ($email) {
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
        $data = $result->fetch_assoc();

        // Check if already registered in BMS
        $bms_conn = new mysqli("localhost:3307", "root", "", "bms");
        if (!$bms_conn->connect_error) {
            $check = $bms_conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $data['email']);
            $check->execute();
            $is_registered = $check->get_result()->num_rows > 0;
            $check->close();
            $bms_conn->close();
            $data['is_registered'] = $is_registered;
        }

        http_response_code(200);
        echo json_encode($data);
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