<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    die("Unauthorized access.");
}

$report_id = $_POST['report_id'] ?? null;
$bpso_id = $_POST['bpso_id'] ?? null;

if (!$report_id || !$bpso_id || !is_numeric($report_id) || !is_numeric($bpso_id)) {
    die("Invalid input data.");
}

include '../../login/db_connect.php';


$stmt = $conn->prepare("UPDATE community_reports SET assigned_to = ?, status = 'Assigned', assigned_at = NOW() WHERE id = ?");
$stmt->bind_param("ii", $bpso_id, $report_id);

if ($stmt->execute()) {
    $msg = "Report successfully assigned!";
} else {
    $msg = "Database error: " . $stmt->error;
}
$stmt->close();
$conn->close();

header("Location: view_report.php?id=$report_id&msg=" . urlencode($msg));
exit();
?>