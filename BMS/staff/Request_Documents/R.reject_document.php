<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

include '../../login/db_connect.php';
include '../../phpmailer_config.php'; // ✅ Tama ang path!

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

// Fetch current record
$stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found.");
}

$record = $result->fetch_assoc();

// Handle form submission: Mark as Validated
$success = false;
$error = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = 'Rejected'; // ✅ Changed from 'Validated'
    $remarks = trim($_POST['remarks'] ?? '');

    $update_stmt = $conn->prepare("UPDATE `$table` SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
    $update_stmt->bind_param("ssi", $status, $remarks, $id);

    if ($update_stmt->execute()) {
        $success = true;

        // ✅ Send Email Notification to Resident (Updated for Rejection)
        $resident_email = $record['email'];
        if (!empty($resident_email)) {
            $subject = "Document Request Rejected - Barangay Bagbag"; // ✅ Updated Subject

            $body = "
                <html>
                <head>
                    <title>Document Rejection</title>
                    <style>
                        body {
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            background-color: #f9f9f9;
                            margin: 0;
                            padding: 20px 0;
                            color: #333;
                            line-height: 1.6;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            background: white;
                            border-radius: 12px;
                            overflow: hidden;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                        }
                        .header {
                            background: linear-gradient(135deg, #c62828, #d32f2f); /* ✅ Changed to Red Gradient */
                            color: white;
                            padding: 25px 20px;
                            text-align: center;
                            font-size: 24px;
                            font-weight: 600;
                            letter-spacing: 0.5px;
                        }
                        .content {
                            padding: 30px 25px;
                            background: #fff;
                        }
                        .content p {
                            margin: 0 0 16px;
                            font-size: 16px;
                        }
                        .status-box {
                            background-color: #ffebee; /* ✅ Light Red */
                            border-left: 4px solid #d32f2f;
                            padding: 14px 16px;
                            margin: 20px 0;
                            border-radius: 0 4px 4px 0;
                            font-size: 15px;
                        }
                        .footer {
                            text-align: center;
                            font-size: 13px;
                            color: #777;
                            padding: 20px;
                            background-color: #f5f5f5;
                            border-top: 1px solid #eee;
                        }
                        @media (max-width: 600px) {
                            .header { font-size: 20px; padding: 20px 15px; }
                            .content { padding: 25px 20px; }
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>Barangay BagbagCare</div> <!-- You might want to change this too, but it's optional -->
                        <div class='content'>
                            <p>Dear <strong>" . htmlspecialchars($record['first_name']) . "</strong>,</p>
                            <p>We regret to inform you that your request for a <strong>" . ucfirst(str_replace('_', ' ', $tab)) . "</strong> has been <strong>rejected</strong> by our staff.</p>
                            
                            <div class='status-box'>
                                <strong>Status:</strong> <span style='color: #c62828; font-weight: 600;'>Rejected</span><br>
                                <strong>Remarks:</strong> " . htmlspecialchars($remarks ?: 'No specific reason provided.') . "
                            </div>

                            <p>You may contact the Barangay Hall for more information or to clarify the reason for rejection.</p>
                            <p>Thank you for your understanding.</p>
                        </div>
                        <div class='footer'>
                            © " . date('Y') . " Barangay BagbagCare. All rights reserved.<br>
                            Empowering Communities, One Service at a Time.
                        </div>
                    </div>
                </body>
                </html>
            ";

            if (sendNotificationEmail($resident_email, $subject, $body)) {
                echo "<script>alert('Rejection successful and notification sent!');</script>";
            } else {
                echo "<script>alert('Rejection successful but email could not be sent. Please check logs.'); console.log('Email failed');</script>";
            }
        }

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
  <title>Reject Document</title>
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
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        Reject Request
      </h2>

      <p class="text-gray-700 mb-6">
  You are about to <strong class="text-red-600">reject</strong> the request of:
  <strong><?= htmlspecialchars($record['first_name'] . ' ' . $record['middle_name'] . ' ' . $record['last_name']) ?></strong>
  (<em><?= ucfirst(str_replace('_', ' ', $tab)) ?></em>).
</p>

      <p class="text-red-700 bg-red-50 border border-red-200 p-3 rounded mb-6">
  <i class="fas fa-exclamation-triangle mr-2"></i>
  <strong>Warning:</strong> This action will mark the request as <strong>Rejected</strong>. The resident will be notified.
</p>

      <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-6"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" id="validateForm">
        <!-- Optional Remarks -->
        <div class="mb-6">
          <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks (Optional)</label>
          <textarea name="remarks" id="remarks" rows="4"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Enter any observations..."><?= htmlspecialchars($_POST['remarks'] ?? '') ?></textarea>
          <p class="text-xs text-gray-500 mt-1">This is for internal review only.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3">
          <a href="S.request_documents.php?tab=<?= htmlspecialchars($tab) ?>"
             class="bg-gray-500 text-white px-5 py-2 rounded-md hover:bg-gray-600 transition">
            Cancel
          </a>
          <button type="submit"
        class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition flex items-center"
        onclick="return confirm('Are you sure you want to reject this request? This action cannot be undone.');">
  <i class="fas fa-times-circle mr-2"></i> Mark as Rejected
</button>
        </div>
      </form>
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

    // Show success alert and redirect after form submission
    <?php if ($success): ?>
      alert("Successfully Rejected!");
      window.location.href = "S.request_documents.php?tab=<?= htmlspecialchars($tab) ?>&status=all";
    <?php endif; ?>
  </script>
</body>
</html>