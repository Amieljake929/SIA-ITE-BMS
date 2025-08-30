<?php
// RIS/generate_reference.php

// Function to generate a random 5-character string
function generatePart() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $part = '';
    for ($i = 0; $i < 5; $i++) {
        $part .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $part;
}

// Function to generate unique reference number
function getUniqueReferenceNumber($conn) {
    $attempts = 0;
    do {
        $ref = generatePart() . '-' . generatePart() . '-' . generatePart();
        $stmt = $conn->prepare("SELECT resident_id FROM residents_id WHERE reference_number = ?");
        $stmt->bind_param("s", $ref);
        $stmt->execute();
        $result = $stmt->get_result();
        $attempts++;
        if ($attempts > 10) {
            throw new Exception("Failed to generate unique reference number after 10 attempts.");
        }
    } while ($result->num_rows > 0);

    return $ref;
}
?>