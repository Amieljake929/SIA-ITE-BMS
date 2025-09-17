<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BPSO') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';
include '../../phpmailer_config.php'; // Include PHPMailer config

// Get report ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid report ID.");
}

// Fetch report
$stmt = $conn->prepare("SELECT * FROM blotter_and_reports WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

if (!$report) {
    $error = "Report not found.";
} else {
    // Authorization: Check if assigned to this BPSO
    if ((int)$report['assigned_to_bpso_id'] !== (int)$_SESSION['user_id']) {
        $error = "Unauthorized: This report is not assigned to you.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($error)) {
    $status = $_POST['status'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');

    if (!in_array($status, ['Under Investigation', 'For Approval'])) {
        $error = "Invalid status selected.";
    } else {
        $stmt = $conn->prepare("UPDATE blotter_and_reports SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $status, $remarks, $id);
        if ($stmt->execute()) {
            $success = "Report updated successfully.";
            
            // Send email notification if complainant has contact info
            if (!empty($report['complainant_contact'])) {
                $toEmail = $report['complainant_contact'];
                $subject = "Update on Your Blotter Report #{$id}";
                
                if ($status === 'Under Investigation') {
                    $body = "
                    <h2 style='color: #4F46E5;'>Your Blotter Report is Now Under Investigation</h2>
                    <p>Hello " . htmlspecialchars($report['complainant_first_name']) . ",</p>
                    <p>We would like to inform you that your blotter report (ID: #{$id}) is now under investigation by our Barangay Peace and Order Officer.</p>
                    <p><strong>Current Status:</strong> Under Investigation</p>
                    <p><strong>Remarks:</strong> " . (empty($remarks) ? 'No additional remarks provided.' : htmlspecialchars($remarks)) . "</p>
                    <p>You will be notified of any further updates regarding your case.</p>
                    <p>Thank you for your patience and cooperation.</p>
                    ";
                } else { // For Approval
                    $body = "
                    <h2 style='color: #7C3AED;'>Your Blotter Report is Now For Approval</h2>
                    <p>Hello " . htmlspecialchars($report['complainant_first_name']) . ",</p>
                    <p>We would like to inform you that the investigation of your blotter report (ID: #{$id}) has been completed and is now pending approval by the Barangay Officials.</p>
                    <p><strong>Current Status:</strong> For Approval</p>
                    <p><strong>Remarks:</strong> " . (empty($remarks) ? 'No additional remarks provided.' : htmlspecialchars($remarks)) . "</p>
                    <p>You will be notified once a final decision has been made regarding your case.</p>
                    <p>Thank you for your patience and cooperation.</p>
                    ";
                }

                // Send email using PHPMailer
                sendNotificationEmail($toEmail, $subject, $body);
            }
            
            // Refresh data
            $report['status'] = $status;
            $report['remarks'] = $remarks;
        } else {
            $error = "Failed to update report.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Investigate Report #<?= $id ?> - Bagbag eServices</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .status-pending { @apply bg-yellow-100 text-yellow-800; }
    .status-assigned { @apply bg-blue-100 text-blue-800; }
    .status-investigation { @apply bg-indigo-100 text-indigo-800; }
    .status-approval { @apply bg-purple-100 text-purple-800; }
    .status-completed { @apply bg-green-100 text-green-800; }
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
                onclick="window.location.href='../BPSO_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Investigate Blotter Report #</h1>
        </div>


      <!-- User Info -->
      <div class="text-sm text-gray-600">
        Logged in as: <span class="font-medium text-blue-700"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow px-6 py-8">
    <div class="max-w-5xl mx-auto">

      <!-- Success/Error Messages -->
      <?php if (isset($success)): ?>
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded text-green-800">
          <p><?= htmlspecialchars($success) ?></p>
        </div>
      <?php endif; ?>
      <?php if (isset($error)): ?>
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded text-red-800">
          <p><?= htmlspecialchars($error) ?></p>
        </div>
      <?php endif; ?>

      <!-- Show Report Details Only If Authorized -->
      <?php if (!isset($error)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <!-- Incident Details -->
          <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-2xl font-bold text-green-800 mb-6 border-b pb-2 border-green-200">Incident Details</h2>

            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Complainant</label>
                <p class="text-lg font-medium"><?= htmlspecialchars("{$report['complainant_first_name']} {$report['complainant_middle_name']} {$report['complainant_last_name']}") ?></p>
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
                <p class="whitespace-pre-wrap text-gray-800"><?= htmlspecialchars($report['incident_narrative']) ?></p>
              </div>

              <!-- Involved Person -->
              <?php if (!empty($report['involved_person_name'])): ?>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Involved Person</label>
                  <p><?= htmlspecialchars($report['involved_person_name']) ?> (<?= htmlspecialchars($report['relation_to_complainant']) ?>)</p>
                  <p class="text-sm text-gray-600"><?= htmlspecialchars($report['involved_description']) ?></p>
                </div>
              <?php endif; ?>

              <!-- Witness -->
              <?php if (!empty($report['witness_name'])): ?>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Witness</label>
                  <p><?= htmlspecialchars($report['witness_name']) ?></p>
                  <p class="text-sm text-gray-600"><?= htmlspecialchars($report['witness_address']) ?> | <?= htmlspecialchars($report['witness_contact']) ?></p>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Sidebar: Status & Actions -->
          <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>
              <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                <?= $report['status'] === 'Pending' ? 'status-pending' : '' ?>
                <?= $report['status'] === 'Assigned' ? 'status-assigned' : '' ?>
                <?= $report['status'] === 'Under Investigation' ? 'status-investigation' : '' ?>
                <?= $report['status'] === 'For Approval' ? 'status-approval' : '' ?>
                <?= $report['status'] === 'Completed' ? 'status-completed' : '' ?>
              ">
<?= htmlspecialchars($report['status']) ?></span>
            </div>

            <!-- Update Form -->
            <form method="POST" class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h3>

              <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="Under Investigation" <?= $report['status'] === 'Under Investigation' ? 'selected' : '' ?>>Under Investigation</option>
                  <option value="For Approval" <?= $report['status'] === 'For Approval' ? 'selected' : '' ?>>For Approval</option>
                </select>
              </div>

              <div class="mb-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="4"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Add your investigation notes here..."><?= htmlspecialchars($report['remarks'] ?? '') ?></textarea>
              </div>

              <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-200 flex items-center justify-center">
                <i class="fas fa-save mr-2"></i> Save Update
              </button>
            </form>

            <!-- Back Button -->
            <div>
              <a href="B.blotter_reports.php" class="w full inline block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
              </a>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

  <!-- JavaScript -->
  <script>
    // Update time
    function updateTime() {
      const now = new Date();
      const options = {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      };
      const formatted edDate = now.toLocaleString('en-US', options);
      document.getElementById('datetime').textContent = format edDate.toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();

    // User Dropdown Toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenuButton && userDropdown) {
      userMenuButton.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('hidden');
      });

      document.addEventListener('click', (e) => {
        if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
          userDropdown.classList.add('hidden');
        }
      });
    }
  </script>
</body>
</html>