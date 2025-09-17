<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';


// Get parameters
$tab = $_GET['tab'] ?? '';
$id = $_GET['id'] ?? 0;

if (!$tab || !$id) {
    die("Invalid request.");
}

$allowed_tables = [
    'barangay_clearance', 'business_permit', 'certificate_of_residency',
    'certificate_of_indigency', 'cedula', 'solo_parents', 'first_time_job_seekers'
];

if (!in_array($tab, $allowed_tables)) {
    die("Invalid document type.");
}

$table = $conn->real_escape_string($tab);
$id = (int)$id;

// Fetch record
$stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found or not approved yet.");
}

$record = $result->fetch_assoc();

// Check if approved
if ($record['status'] !== 'Approved') {
    die("This document is not yet approved. Cannot print.");
}

$conn->close();

// Helper: Format name
function fullName($first, $middle, $last) {
    $middle = $middle ? strtoupper(substr($middle, 0, 1)) . '.' : '';
    return htmlspecialchars(strtoupper($first) . ' ' . $middle . ' ' . strtoupper($last));
}

// Document title mapping
$doc_titles = [
    'barangay_clearance' => 'BARANGAY CLEARANCE',
    'business_permit' => 'BUSINESS PERMIT',
    'certificate_of_residency' => 'CERTIFICATE OF RESIDENCY',
    'certificate_of_indigency' => 'CERTIFICATE OF INDIGENCY',
    'cedula' => 'COMMUNITY TAX CERTIFICATE (CEDULA)',
    'solo_parent' => 'SOLO PARENT ID & CERTIFICATE',
    'first_time_job_seeker' => 'CERTIFICATE OF FIRST-TIME JOB SEEKER'
];

$doc_title = $doc_titles[$tab];
$doc_subtitle = [
    'barangay_clearance' => 'This certifies that the bearer is a bonafide resident...',
    'business_permit' => 'Issued to operate a business within the jurisdiction...',
    'certificate_of_residency' => 'Certifies the residency of the individual...',
    'certificate_of_indigency' => 'Certifies that the bearer belongs to an indigent family...',
    'cedula' => 'Official community tax certificate issued by the barangay...',
    'solo_parent' => 'Recognition of the bearer as a Solo Parent under RA 8972...',
    'first_time_job_seeker' => 'Certifies that the bearer is a first-time job seeker under RA 11261...'
][$tab];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/qrcode.js/lib/qrcode.min.js"></script>
  <style>
    @media print {
      .no-print { display: none !important; }
      body { margin: 0; padding: 0; }
      @page { margin: 0.5in; }
      .print-container { width: 100%; max-width: none; }
    }
    .header-logo { height: 100px; }
    .signature-line { min-height: 60px; }
  </style>
</head>
<body class="bg-gray-100 p-6">

  <!-- Print Container -->
  <div class="print-container bg-white p-10 max-w-5xl mx-auto shadow-lg">
    
    <!-- Letterhead -->
    <div class="text-center mb-6 border-b-4 border-green-800 pb-4">
      <div class="flex justify-center items-center space-x-4">
        <img src="../../images/Bagbag.png" alt="Barangay Logo" class="header-logo">
        <div class="text-left">
          <h2 class="text-2xl font-bold text-green-900">Republic of the Philippines</h2>
          <h3 class="text-xl font-semibold text-green-800">City of Marikina</h3>
          <h3 class="text-xl font-semibold text-green-800">Barangay Bagbag</h3>
          <p class="text-gray-600">Office of the Punong Barangay</p>
        </div>
      </div>
    </div>

    <!-- Document Title -->
    <div class="text-center my-8">
      <h1 class="text-3xl font-bold uppercase text-green-900"><?= $doc_title ?></h1>
      <p class="text-sm text-gray-600 mt-2 italic"><?= $doc_subtitle ?></p>
    </div>

    <!-- QR Code (Top Right Corner) -->
    <div class="absolute top-10 right-10 w-20 h-20 no-print" id="qrcode"></div>

    <!-- Main Content -->
    <div class="mx-16 my-10">
      <p class="text-lg leading-relaxed">
        This is to certify that <strong><?= fullName($record['first_name'], $record['middle_name'], $record['last_name']) ?></strong>
        is a resident of Barangay Bagbag, Marikina City.
      </p>

      <?php if ($tab === 'business_permit'): ?>
        <p class="mt-4 text-lg">
          This permit is issued to <strong><?= htmlspecialchars(strtoupper($record['business_name'])) ?></strong>,
          located at <?= htmlspecialchars($record['business_address']) ?>, engaged in <?= htmlspecialchars($record['business_nature']) ?>.
        </p>
      <?php endif; ?>

      <?php if ($tab === 'solo_parent'): ?>
        <p class="mt-4 text-lg">
          Recognized as a Solo Parent under RA 8972, with children:
        </p>
        <ul class="list-disc ml-8 mt-2">
          <?php
          // Fetch children (if any)
          $child_conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
          $child_stmt = $child_conn->prepare("SELECT child_first_name, child_middle_name, child_last_name FROM solo_parent_children WHERE parent_id = ?");
          $child_stmt->bind_param("i", $id);
          $child_stmt->execute();
          $children = $child_stmt->get_result();
          while ($child = $children->fetch_assoc()):
          ?>
            <li><?= fullName($child['child_first_name'], $child['child_middle_name'], $child['child_last_name']) ?></li>
          <?php endwhile; $child_conn->close(); ?>
        </ul>
      <?php endif; ?>

      <p class="mt-6 text-lg">
        This <?= strtolower(str_replace('_', ' ', $tab)) ?> is issued upon the request of the above-named individual for <?= htmlspecialchars($record['purpose'] ?? 'personal use') ?>.
      </p>

      <p class="mt-4 text-lg">Issued this <?= date('d') ?> day of <?= date('F Y') ?>.</p>
    </div>

    <!-- Signature Section -->
    <div class="mt-16 mx-16 grid grid-cols-1 md:grid-cols-2 gap-12">
      <!-- Resident's Signature -->
      <div class="text-center">
        <div class="signature-line"></div>
        <p class="font-semibold border-t pt-2">Applicant's Signature</p>
      </div>

      <!-- Official's Signature -->
      <div class="text-center">
        <div class="signature-line"></div>
        <p class="font-semibold">Punong Barangay</p>
        <p class="text-sm text-gray-600">Barangay Bagbag, Marikina City</p>
      </div>
    </div>

    <!-- Control Number & QR Info -->
    <div class="mt-10 text-sm text-gray-500 text-center">
      <p>Document ID: <?= htmlspecialchars($record[$tab . '_id'] ?? $record['id']) ?></p>
      <p>Issued on: <?= date('F j, Y \a\t g:i A', strtotime($record['application_date'])) ?></p>
      <p class="mt-1">Verify this document at: <strong>verify.bagbageservices.gov.ph</strong></p>
    </div>

  </div>

  <!-- Print Button -->
  <div class="text-center mt-8 no-print">
    <button onclick="window.print()" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition flex items-center mx-auto">
      <i class="fas fa-print mr-2"></i> Print Document
    </button>
    <a href="R.view_document_details.php?tab=<?= $tab ?>&id=<?= $id ?>" class="block mt-4 text-blue-600 hover:underline">Back to Details</a>
  </div>

  <!-- QR Code Script -->
  <script>
    window.onload = function () {
      const qrText = "https://verify.bagbageservices.gov.ph/verify?id=<?= $id ?>&type=<?= $tab ?>";
      new QRCode(document.getElementById("qrcode"), {
        text: qrText,
        width: 100,
        height: 100
      });
    };
  </script>

</body>
</html>