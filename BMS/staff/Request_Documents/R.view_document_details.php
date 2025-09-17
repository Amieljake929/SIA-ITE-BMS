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

// Allowed tables
$allowed_tables = [
    'barangay_clearance', 'business_permit', 'certificate_of_residency',
    'certificate_of_indigency', 'cedula', 'solo_parents', 'first_time_job_seekers'
];

if (!in_array($tab, $allowed_tables)) {
    die("Invalid document type.");
}

// Fetch record
$table = $conn->real_escape_string($tab);
$id = (int)$id;

$stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found.");
}

$record = $result->fetch_assoc();
$conn->close();

// Helper: Display value or "N/A"
function display($value) {
    return $value ? htmlspecialchars($value) : '<span class="text-gray-500 italic">N/A</span>';
}

// Helper: Display image if exists
function showImage($src, $label) {
    if (!$src) return '<span class="text-gray-500 italic">No image uploaded</span>';
    return '<img src="../uploads/' . htmlspecialchars($src) . '" alt="' . $label . '" class="w-64 h-auto border rounded shadow-sm mt-2">';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Document Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">Loading...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow"/>
    </div>
  </div>

  <!-- Main Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
        <!-- Home Icon Button and Title -->
        <div class="flex items-center space-x-4">
            <!-- Home Icon Button -->
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../staff_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Document Requests</h1>
        </div>



      <a href="S.request_documents.php?tab=<?= htmlspecialchars($tab) ?>"
         class="text-blue-600 hover:underline text-sm flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Requests
      </a>
    </div>
  </header>

  <!-- Content -->
  <div class="container mx-auto mt-6 px-6 pb-10">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">
        <?= ucfirst(str_replace('_', ' ', $tab)) ?> Details
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($record as $key => $value): ?>
          <?php if ($key === 'id' || $key === 'user_id' || $key === 'resident_id') continue; ?>
          <?php if (in_array($key, ['valid_id_front', 'proof_of_address', 'previous_clearance', 'cedula'])): ?>
            <!-- Image fields -->
            <div class="col-span-1 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1"><?= ucfirst(str_replace('_', ' ', $key)) ?>:</label>
              <?= showImage($value, $key) ?>
            </div>
          <?php else: ?>
            <!-- Text fields -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"><?= ucfirst(str_replace('_', ' ', $key)) ?>:</label>
              <p class="text-gray-800"><?= display($value) ?></p>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <!-- Action Buttons -->
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="R.validate_document.php?tab=<?= $tab ?>&id=<?= $id ?>"
           class="bg-yellow-500 text-white px-5 py-2 rounded-md hover:bg-yellow-600 transition flex items-center">
          <i class="fas fa-check-circle mr-2"></i> Validate Request
        </a>
        <?php if ($record['status'] === 'Approved'): ?>
          <a href="print_document.php?tab=<?= $tab ?>&id=<?= $id ?>" target="_blank"
             class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700 transition flex items-center">
            <i class="fas fa-print mr-2"></i> Print Document
          </a>
        <?php else: ?>
          <span class="bg-gray-300 text-gray-500 px-5 py-2 rounded-md cursor-not-allowed flex items-center">
            <i class="fas fa-print mr-2"></i> Print (Pending)
          </span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <!-- JavaScript -->
  <script>
    function updateTime() {
      const now = new Date();
      const options = {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      };
      document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();
  </script>
</body>
</html>