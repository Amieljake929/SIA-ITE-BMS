<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    die("Unauthorized access.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid report ID.");
}

$report_id = (int)$_GET['id'];

include '../../login/db_connect.php';


// I-update ang report: status = "Completed"
$stmt = $conn->prepare("UPDATE community_reports SET status = 'Completed', closed_at = NOW(), closed_by = ? WHERE id = ?");
$stmt->bind_param("ii", $_SESSION['user_id'], $report_id);

if ($stmt->execute()) {
    $msg = "Report successfully closed!";
} else {
    $msg = "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();

// Redirect back with message
header("Location: O.community_reports.php?msg=" . urlencode($msg));
exit();
?>