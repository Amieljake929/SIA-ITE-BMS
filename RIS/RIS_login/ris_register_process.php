<?php
// Database connection
include 'db_connect.php';


// Get latest ID
$result = $conn->query("SELECT MAX(id) AS max_id FROM users");
$row = $result->fetch_assoc();

if ($row['max_id']) {
    $last_id = intval($row['max_id']); // convert to int
    $new_id = str_pad($last_id + 1, 6, '0', STR_PAD_LEFT); // e.g. 000001, 000002
} else {
    $new_id = "000001"; // First user
}

// Get form data
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password
$role = $_POST['role'];

// Insert user
$stmt = $conn->prepare("INSERT INTO users (id, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $new_id, $full_name, $email, $password, $role);

if ($stmt->execute()) {
    echo "✅ Registration successful!<br>";
    echo "Your User ID is: <b>" . $new_id . "</b>";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
