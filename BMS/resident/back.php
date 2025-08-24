<!-- back.php -->
<?php
session_start();

// Ensure the user is logged in and is a Resident
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Redirect back to the resident dashboard
header("Location: resident_dashboard.php");
exit();
?>