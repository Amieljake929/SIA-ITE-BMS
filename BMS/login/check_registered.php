<?php
// BMS/login/check_registered.php

header('Content-Type: application/json');

// Get email from query
$email = $_GET['email'] ?? '';
if (!$email) {
    echo json_encode(['error' => 'Email required']);
    exit;
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "bms");

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;

$stmt->close();
$conn->close();

// Return JSON
echo json_encode(['registered' => $exists]);
?>