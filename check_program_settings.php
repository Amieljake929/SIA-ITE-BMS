<?php
include 'BMS/login/db_connect.php';

// Simulate disabling the program
echo "Disabling anti_rabies program...\n";
$updateQuery = $conn->prepare("UPDATE program_settings SET is_enabled = 0 WHERE program_name = 'anti_rabies'");
$updateQuery->execute();
$updateQuery->close();

$query = $conn->query("SELECT * FROM program_settings");
if ($query) {
    while ($row = $query->fetch_assoc()) {
        echo "Program: " . $row['program_name'] . " - Enabled: " . ($row['is_enabled'] ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}

// Now simulate the R_programs.php logic
echo "\n--- Simulating R_programs.php logic ---\n";
$antiRabiesEnabled = false;
$settingsQuery = $conn->query("SELECT is_enabled FROM program_settings WHERE program_name = 'anti_rabies'");
if ($settingsQuery && $row = $settingsQuery->fetch_assoc()) {
    $antiRabiesEnabled = $row['is_enabled'];
    echo "Anti Rabies Enabled from DB: " . ($antiRabiesEnabled ? 'Yes' : 'No') . "\n";
} else {
    echo "Failed to fetch settings.\n";
}

if ($antiRabiesEnabled) {
    echo "Button should be: <a href='R.anti_rabies_registration.php'>Anti Rabies Registration</a>\n";
} else {
    echo "Button should be: <div>Anti Rabies Registration (Disabled)</div>\n";
}

$conn->close();
?>
