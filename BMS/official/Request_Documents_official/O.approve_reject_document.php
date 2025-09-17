<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';
include '../../phpmailer_config.php'; // ✅ Tama ang path!

// Get parameters
$tab = $_GET['tab'] ?? '';
$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? ''; // approve or reject

$allowed_tables = [
    'barangay_clearance', 'business_permit', 'certificate_of_residency',
    'certificate_of_indigency', 'cedula', 'solo_parents', 'first_time_job_seekers'
];

if (!in_array($tab, $allowed_tables) || !$id || !in_array($action, ['approve', 'reject'])) {
    die("Invalid request.");
}

$table = $conn->real_escape_string($tab);
$id = (int)$id;
$status = $action === 'approve' ? 'Approved' : 'Rejected';

// Fetch current record
$stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found.");
}

$record = $result->fetch_assoc();

// Ensure status is 'Validated' (Staff must validate first)
if ($record['status'] !== 'Validated') {
    die("This document is not pending validation.");
}

// Handle form submission
$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $remarks = trim($_POST['remarks'] ?? '');

    $update_stmt = $conn->prepare("UPDATE `$table` SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
    $update_stmt->bind_param("ssi", $status, $remarks, $id);

    if ($update_stmt->execute()) {
        $success = true;

        // ✅ SEND EMAIL NOTIFICATION
        $toEmail = $record['email']; // Dapat merong 'email' column sa table mo!
        $subject = "Document Request Status - " . ucfirst($action);
        
        if ($action === 'approve') {
            $body = "
            <h2 style='color: green;'>Your Document Has Been Approved!</h2>
            <p>Hello " . htmlspecialchars($record['first_name']) . ",</p>
            <p>We are pleased to inform you that your request for <strong>" . ucfirst(str_replace('_', ' ', $tab)) . "</strong> has been successfully approved.</p>
            <p>You may now proceed with printing and using your document.</p>
            <p><strong>Remarks:</strong> " . (empty($remarks) ? 'None' : htmlspecialchars($remarks)) . "</p>
            <p>Thank you for choosing BagbagCare.</p>
            ";
        } else {
            $body = "
            <h2 style='color: red;'>Your Document Request Has Been Rejected</h2>
            <p>Hello " . htmlspecialchars($record['first_name']) . ",</p>
            <p>We regret to inform you that your request for <strong>" . ucfirst(str_replace('_', ' ', $tab)) . "</strong> has been rejected.</p>
            <p><strong>Reason:</strong> " . (empty($remarks) ? 'No specific reason provided.' : htmlspecialchars($remarks)) . "</p>
            <p>Please contact the Barangay Office for further assistance.</p>
            <p>Thank you for your understanding.</p>
            ";
        }

        // Magpadala ng email
        sendNotificationEmail($toEmail, $subject, $body);

    } else {
        $error = "Database error: " . $conn->error;
    }
    $update_stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Approve/Reject Document</title>
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
                onclick="window.location.href='../official_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Document Approval Request</h1>
        </div>

  
      <a href="O.official_request_documents.php?tab=<?= htmlspecialchars($tab) ?>"
         class="text-blue-600 hover:underline text-sm flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Requests
      </a>
    </div>
  </header>

  <!-- Content -->
  <div class="container mx-auto mt-6 px-6 pb-10">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        Confirm <?= ucfirst($action) ?>
      </h2>

      <p class="text-gray-700 mb-6">
        You are about to <strong><?= $action === 'approve' ? 'approve' : 'reject' ?></strong> the request of:
        <strong><?= htmlspecialchars($record['first_name'] . ' ' . $record['middle_name'] . ' ' . $record['last_name']) ?></strong>
        (<em><?= ucfirst(str_replace('_', ' ', $tab)) ?></em>).
      </p>

      <?php if ($action === 'reject'): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded mb-6">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          <strong>Rejecting</strong> this document will mark it as "Rejected". The applicant will be notified.
        </div>
      <?php else: ?>
        <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded mb-6">
          <i class="fas fa-check-circle mr-2"></i>
          <strong>Approving</strong> this document will allow printing and official use.
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-6"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" id="actionForm">
        <!-- Remarks -->
        <div class="mb-6">
          <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks (Optional)</label>
          <textarea name="remarks" id="remarks" rows="4"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Enter any remarks..."></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3">
          <a href="O.official_request_documents.php?tab=<?= htmlspecialchars($tab) ?>"
             class="bg-gray-500 text-white px-5 py-2 rounded-md hover:bg-gray-600 transition">
            Cancel
          </a>
          <button type="submit"
                  class="bg-<?= $action === 'approve' ? 'green' : 'red' ?>-600 text-white px-6 py-2 rounded-md hover:bg-<?= $action === 'approve' ? 'green' : 'red' ?>-700 transition flex items-center">
            <i class="fas fa-<?= $action === 'approve' ? 'check' : 'times' ?> mr-2"></i>
            Confirm <?= ucfirst($action) ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> BagbagCare. All rights reserved. | Empowering Communities Digitally.
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

    // On success, show alert and redirect
    <?php if ($success): ?>
      alert("Document successfully <?= $action === 'approve' ? 'approved' : 'rejected' ?>!");
      // Redirect back to official requests with current tab
      const tab = "<?= htmlspecialchars($tab) ?>";
      const status = "<?= $action === 'approve' ? 'Approved' : 'Rejected' ?>";
      window.location.href = "O.official_request_documents.php?tab=" + encodeURIComponent(tab) + "&flash=" + encodeURIComponent("<?= ucfirst($action) ?>");

    <?php endif; ?>
  </script>
</body>
</html>