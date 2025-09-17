<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BPSO') {
    die("Unauthorized.");
}

$report_id = $_POST['report_id'] ?? null;
$remarks = trim($_POST['remarks'] ?? '');
$status = $_POST['status'] ?? '';

if (!$report_id || !$remarks || !in_array($status, ['Assigned', 'For Closing'])) {
    die("Invalid input.");
}

include '../../login/db_connect.php';


// Optional: Update assigned_by if first time
$stmt = $conn->prepare("UPDATE community_reports 
                        SET remarks = ?, status = ?, updated_by = 'BPSO', updated_at = NOW() 
                        WHERE id = ?");
$stmt->bind_param("ssi", $remarks, $status, $report_id);

if ($stmt->execute()) {
    $msg = "Action successfully updated!";
} else {
    $msg = "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();

header("Location: action_report.php?id=$report_id&msg=" . urlencode($msg));
exit();
?>