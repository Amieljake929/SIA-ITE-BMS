<?php
// Start session
session_start();

// Check if resident and logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    die("<script>alert('Unauthorized access!'); window.location.href = '../../login/login.php';</script>");
}

// Database connection
include '../../login/db_connect.php';

// Check connection
if ($conn->connect_error) {
    die("<script>alert('Database connection failed: " . addslashes($conn->connect_error) . "'); window.history.back();</script>");
}

// Get form data
$owner_first_name = $conn->real_escape_string($_POST['owner_first_name']);
$owner_middle_name = $conn->real_escape_string($_POST['owner_middle_name']);
$owner_last_name = $conn->real_escape_string($_POST['owner_last_name']);
$owner_address = $conn->real_escape_string($_POST['owner_address']);
$owner_contact = $conn->real_escape_string($_POST['owner_contact']);
$owner_email = $conn->real_escape_string($_POST['owner_email']);

$pet_name = $conn->real_escape_string($_POST['pet_name']);
$pet_type = $conn->real_escape_string($_POST['pet_type']);
$pet_breed = $conn->real_escape_string($_POST['pet_breed']);
$pet_color_markings = $conn->real_escape_string($_POST['pet_color_markings']);
$pet_sex = $conn->real_escape_string($_POST['pet_sex']);
$pet_female_status = isset($_POST['pet_female_status']) ? $conn->real_escape_string($_POST['pet_female_status']) : NULL;
$pet_male_status = isset($_POST['pet_male_status']) ? $conn->real_escape_string($_POST['pet_male_status']) : NULL;
$pet_age_birthdate = $_POST['pet_age_birthdate'];
$num_dogs = (int)$_POST['num_dogs'];
$num_cats = (int)$_POST['num_cats'];

$last_vaccination_date = $_POST['last_vaccination_date'];
$vaccination_brand = $conn->real_escape_string($_POST['vaccination_brand']);
$has_pet_booklet = $conn->real_escape_string($_POST['has_pet_booklet']);
$tag_tattoo_microchip = $conn->real_escape_string($_POST['tag_tattoo_microchip']);

// Admin fields (empty for now)
$current_vaccination_date = NULL;
$current_brand = '';
$serial_lot_no = '';
$expiration_date = NULL;
$next_vaccination_date = NULL;
$veterinarian_name = '';
$prc_license = '';
$veterinarian_signature = '';

// Consent
$consent_accuracy = isset($_POST['consent_accuracy']) ? 'Yes' : 'No';
$consent_side_effects = isset($_POST['consent_side_effects']) ? 'Yes' : 'No';
$consent_data_privacy = isset($_POST['consent_data_privacy']) ? 'Yes' : 'No';

// Status
$status = 'Pending';

// Get user_id and resident_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM residents WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<script>alert('No resident record found for this account.'); window.history.back();</script>");
}

$resident_row = $result->fetch_assoc();
$resident_id = $resident_row['id'];
$stmt->close();

// Insert into registration_anti_rabies table
// Insert into registration_anti_rabies table
$stmt = $conn->prepare("
    INSERT INTO registration_anti_rabies (
        resident_id, user_id, owner_first_name, owner_middle_name, owner_last_name, owner_address, owner_contact, owner_email,
        pet_name, pet_type, pet_breed, pet_color_markings, pet_sex, pet_female_status, pet_male_status, pet_age_birthdate,
        num_dogs, num_cats, last_vaccination_date, vaccination_brand, has_pet_booklet, tag_tattoo_microchip,
        current_vaccination_date, current_brand, serial_lot_no, expiration_date, next_vaccination_date,
        veterinarian_name, prc_license, veterinarian_signature,
        consent_accuracy, consent_side_effects, consent_data_privacy, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
"); // <-- DITO ANG FIX: Binawasan ko ng isang '?'

$stmt->bind_param(
    "iissssssssssssssiissssssssssssssss", // Ito ay tama na (34 types)
    $resident_id, $user_id, $owner_first_name, $owner_middle_name, $owner_last_name, $owner_address, $owner_contact, $owner_email,
    $pet_name, $pet_type, $pet_breed, $pet_color_markings, $pet_sex, $pet_female_status, $pet_male_status, $pet_age_birthdate,
    $num_dogs, $num_cats, $last_vaccination_date, $vaccination_brand, $has_pet_booklet, $tag_tattoo_microchip,
    $current_vaccination_date, $current_brand, $serial_lot_no, $expiration_date, $next_vaccination_date,
    $veterinarian_name, $prc_license, $veterinarian_signature,
    $consent_accuracy, $consent_side_effects, $consent_data_privacy, $status
);

// Execute and check
if ($stmt->execute()) {
    echo "<script>
        alert('✅ Success! Your Anti Rabies Registration has been submitted.');
        window.location.href = 'R.anti_rabies_registration.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Error: " . addslashes($stmt->error) . "');
        window.history.back();
    </script>";
}

// Close
$stmt->close();
$conn->close();
?>
