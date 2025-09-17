<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: ../login/login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid report ID.");
}

// Fetch report
$stmt = $conn->prepare("
    SELECT 
        b.*,
        u.full_name AS bpso_name
    FROM blotter_and_reports b
    LEFT JOIN users u ON b.assigned_to_bpso_id = u.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Report not found.");
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
  <title>View Report #<?= $id ?> - Bagbag eServices</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <style>
    .status-pending {
      @apply px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800;
    }
    .status-assigned {
      @apply px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800;
    }
    .status-for-approval {
      @apply px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800;
    }
    .status-completed {
      @apply px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800;
    }
    .status-other {
      @apply px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">LOADING DATE...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
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
                onclick="window.location.href='../resident_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">View Blotter Report #<?= $id ?></h1>
        </div>

      <div class="text-sm text-gray-600">
        Logged in as: <span class="font-medium text-blue-700"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow px-6 py-8">
    <div class="max-w-5xl mx-auto">

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Incident Details -->
        <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-lg border border-gray-100">
          <h2 class="text-2xl font-bold text-green-800 mb-6 border-b pb-2 border-green-200">Incident Details</h2>

          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Complainant</label>
              <p class="text-lg"><?= htmlspecialchars("{$report['complainant_first_name']} {$report['complainant_last_name']}") ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Incident Type</label>
                <p><?= htmlspecialchars($report['incident_type']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                <p><?= htmlspecialchars($report['incident_date']) ?> at <?= htmlspecialchars($report['incident_time']) ?></p>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
              <p><?= htmlspecialchars($report['incident_location']) ?></p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Narrative</label>
              <p class="whitespace-pre-wrap"><?= htmlspecialchars($report['incident_narrative']) ?></p>
            </div>
            <?php if (!empty($report['investigation_remarks'])): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Investigation Remarks</label>
                <p class="bg-gray-50 p-3 rounded"><?= htmlspecialchars($report['investigation_remarks']) ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Status -->
          <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>
            <?php
            $status = htmlspecialchars($report['status']);
            $class = match($status) {
                'Pending' => 'status-pending',
                'Assigned' => 'status-assigned',
                'For Approval' => 'status-for-approval',
                'Completed' => 'status-completed',
                default => 'status-other'
            };
            ?>
            <span class="<?= $class ?>"><?= $status ?></span>
          </div>

          <!-- BPSO Info -->
          <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Assigned BPSO</h3>
            <p><?= htmlspecialchars($report['bpso_name'] ?? 'Not Assigned Yet') ?></p>
          </div>

          <!-- Back -->
          <div>
            <a href="R.blotter_report_history.php" class="w-full inline-block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
              <i class="fas fa-arrow-left mr-2"></i> Back to History
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>

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
      const formattedDate = now.toLocaleString('en-US', options);
      document.getElementById('datetime').textContent = formattedDate.toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();
  </script>
</body>
</html>