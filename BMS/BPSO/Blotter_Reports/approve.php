<?php
session_start();
if (!in_array($_SESSION['role'], ['Captain', 'Secretary'])) {
    die("Unauthorized");
}

include '../config/db.php';
$id = $_POST['id'];
$action = $_POST['action'];

if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE blotter_and_reports SET status = 'Completed', updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: B.view_investigation.php?id=$id");
?>