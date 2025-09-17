<?php
// files/download.php
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . basename($_GET['file']));

$allowed_paths = [
    'resident/Community_Reports/uploads/reports/'
];

$file = $_GET['file'];

// Security: Validate file path
if (!isset($file) || empty($file)) {
    http_response_code(400);
    die("No file specified.");
}

// Check if file is within allowed paths
$isValid = false;
foreach ($allowed_paths as $path) {
    if (strpos($file, $path) === 0) {
        $isValid = true;
        break;
    }
}

if (!$isValid) {
    http_response_code(403);
    die("Access denied.");
}

if (!file_exists($file)) {
    http_response_code(404);
    die("File not found.");
}

readfile($file);