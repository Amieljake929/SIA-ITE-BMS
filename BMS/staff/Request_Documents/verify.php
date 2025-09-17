<?php
// Database connection
include '../../login/db_connect.php';


// Get parameters
$id = $_GET['id'] ?? 0;
$type = $_GET['type'] ?? '';

$allowed_tables = [
    'barangay_clearance', 'business_permit', 'certificate_of_residency',
    'certificate_of_indigency', 'cedula', 'solo_parent', 'first_time_job_seeker'
];

if (!in_array($type, $allowed_tables) || !$id) {
    $error = "Invalid document link.";
}

$valid = false;
$doc = null;

if (!$error) {
    $table = $conn->real_escape_string($type);
    $id = (int)$id;

    $stmt = $conn->prepare("SELECT *, 'approved' as verified_status FROM `$table` WHERE id = ? AND status = 'Approved'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $valid = true;
        $doc = $result->fetch_assoc();
    }
}
$conn->close();

// Document type labels
$doc_labels = [
    'barangay_clearance' => 'Barangay Clearance',
    'business_permit' => 'Business Permit',
    'certificate_of_residency' => 'Certificate of Residency',
    'certificate_of_indigency' => 'Certificate of Indigency',
    'cedula' => 'Community Tax Certificate (Cedula)',
    'solo_parent' => 'Solo Parent Certificate',
    'first_time_job_seeker' => 'First-Time Job Seeker Certificate'
];

$doc_name = $doc_labels[$type] ?? 'Document';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <style>
    body { background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); }
    .card { max-width: 800px; margin: 2rem auto; }
    .qr-preview { max-width: 180px; margin: 0 auto; }
    .qr-preview img { border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem; background: white; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">

  <div class="card p-6 bg-white rounded-xl shadow-lg text-center">

    <!-- Logo -->
    <div class="flex justify-center mb-4">
      <img src="images/Bagbag.png" alt="Barangay Bagbag Logo" class="h-16 object-contain">
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Barangay Bagbag</h1>
    <p class="text-gray-600 mb-6">Document Verification Portal</p>

    <?php if (isset($error)): ?>
      <!-- Error -->
      <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
        <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
        <p class="font-medium"><?= htmlspecialchars($error) ?></p>
        <p class="text-sm mt-2">Please check the link or contact the barangay office.</p>
      </div>

    <?php elseif ($valid): ?>
      <!-- Valid Document -->
      <div class="bg-green-50 border border-green-200 text-green-800 p-6 rounded-lg mb-6">
        <i class="fas fa-check-circle text-5xl mb-3 text-green-500"></i>
        <h2 class="text-xl font-semibold">Valid Document</h2>
        <p class="text-sm text-green-700">This document is officially issued and verified.</p>
      </div>

      <!-- Document Info -->
      <div class="text-left bg-gray-50 p-6 rounded-lg space-y-3 text-sm">
        <h3 class="font-bold text-lg text-gray-800 border-b pb-2">Document Details</h3>
        <p><strong>Type:</strong> <?= $doc_name ?></p>
        <p><strong>Holder:</strong> <?= htmlspecialchars(strtoupper($doc['first_name'] . ' ' . $doc['middle_name'][0] . '. ' . $doc['last_name'])) ?></p>
        <p><strong>Document ID:</strong> <?= htmlspecialchars($doc[$type . '_id'] ?? $doc['id']) ?></p>
        <p><strong>Status:</strong> 
          <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Approved</span>
        </p>
        <p><strong>Issued On:</strong> <?= date('F j, Y', strtotime($doc['application_date'])) ?></p>
        <p><strong>Valid Until:</strong> <?= date('F j, Y', strtotime($doc['application_date'] . ' + 1 year')) ?></p>
      </div>

      <!-- QR Code Preview -->
      <div class="mt-6 qr-preview">
        <img src="data:image/png;base64,<?= base64_encode(file_get_contents("https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://verify.bagbageservices.gov.ph/verify.php?id={$id}&type={$type}")) ?>" alt="QR Code">
        <p class="text-xs text-gray-500 mt-2">QR Code used for verification</p>
      </div>

      <p class="text-xs text-gray-500 mt-6">
        This document is valid and recognized by Barangay Bagbag, Marikina City.
      </p>

    <?php else: ?>
      <!-- Invalid -->
      <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
        <i class="fas fa-times-circle text-4xl mb-3 text-red-500"></i>
        <h2 class="text-xl font-semibold">Invalid or Expired Document</h2>
        <p class="text-sm mt-2">This document may have been rejected, revoked, or not approved.</p>
        <p>Contact the Barangay Office for more information.</p>
      </div>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="mt-8">
      <a href="javascript:history.back()" class="inline-block bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition">
        <i class="fas fa-arrow-left mr-2"></i> Go Back
      </a>
    </div>

  </div>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 mt-10 pb-6">
    &copy; <?= date('Y') ?> Barangay Bagbag. All rights reserved. | Verification System v1.0
  </footer>

</body>
</html>