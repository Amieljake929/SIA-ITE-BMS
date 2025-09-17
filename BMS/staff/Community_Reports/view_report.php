<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid or missing report ID.");
}

$report_id = (int)$_GET['id'];

include '../../login/db_connect.php';


$sql = "SELECT * FROM community_reports WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Report not found in the database.");
}

$report = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Report #<?= $report['id'] ?> - Bagbag eServices</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center">
    <span id="datetime" class="font-medium">LOADING DATE...</span>
    <img src="../../images/Bagbag.png" alt="Logo" class="h-10" />
  </div>

  <!-- Header -->
  <header class="bg-white shadow px-6 py-4">
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

            <h1 class="text-xl font-bold text-green-800">Community Reports</h1>
        </div>

  </header>

  <!-- Main Content -->
  <main class="container mx-auto px-6 py-8">
    <a href="S.community_reports.php" class="inline-flex items-center text-green-700 hover:text-green-900 mb-6">
      <i class="fas fa-arrow-left mr-2"></i> Back to Reports
    </a>

    <div class="bg-white shadow rounded-lg p-6 space-y-5">
      <h2 class="text-xl font-semibold text-gray-800">Report Details</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Report ID:</strong> <?= $report['id'] ?></p>
        <p><strong>Status:</strong> 
          <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800"><?= htmlspecialchars($report['status']) ?></span>
        </p>
        <p><strong>Resident:</strong> <?= htmlspecialchars($report['first_name'] . ' ' . $report['last_name']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($report['contact_number']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($report['email']) ?></p>
        <p><strong>Address:</strong> 
          <?= htmlspecialchars("House #{$report['house_no']}, {$report['street']}, Purok {$report['purok']}, {$report['barangay']}, {$report['city']}, {$report['province']}") ?>
        </p>
        <p><strong>Incident Type:</strong> <?= htmlspecialchars($report['incident_type']) ?></p>
        <p><strong>Date & Time:</strong> <?= htmlspecialchars($report['incident_date']) ?> at <?= htmlspecialchars($report['incident_time']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($report['incident_location']) ?></p>
      </div>

      <div class="border-t pt-4">
        <p><strong>Details:</strong></p>
        <p class="text-gray-700 mt-1"><?= nl2br(htmlspecialchars($report['incident_details'])) ?></p>
      </div>

      <?php if (!empty($report['accussed_names_residences'])): ?>
        <div class="border-t pt-4">
          <p><strong>Accused:</strong></p>
          <p><?= htmlspecialchars($report['accussed_names_residences']) ?></p>
        </div>
      <?php endif; ?>

      <?php if (!empty($report['requested_action'])): ?>
        <div class="border-t pt-4">
          <p><strong>Requested Action:</strong></p>
          <p><?= htmlspecialchars($report['requested_action']) ?></p>
        </div>
      <?php endif; ?>

      <?php if (!empty($report['evidence_path']) && file_exists($report['evidence_path'])): ?>
        <div class="border-t pt-4">
          <p><strong>Evidence:</strong></p>
          <img src="<?= htmlspecialchars($report['evidence_path']) ?>" alt="Evidence" class="mt-2 max-w-md rounded shadow">
        </div>
      <?php endif; ?>

      <div class="flex justify-end mt-6">
        <a href="assign_report.php?id=<?= $report['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          <i class="fas fa-share mr-1"></i> Assign to BPSO
        </a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-8">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved.
  </footer>

  <script>
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();
  </script>
</body>
</html>