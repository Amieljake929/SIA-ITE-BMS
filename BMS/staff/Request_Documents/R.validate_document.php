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
    $status = 'Validated';
    $remarks = trim($_POST['remarks'] ?? '');

    $update_stmt = $conn->prepare("UPDATE `$table` SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
    $update_stmt->bind_param("ssi", $status, $remarks, $id);

    if ($update_stmt->execute()) {
        $success = true;

        // ✅ Send Email Notification to Resident
        $resident_email = $record['email'];
if (!empty($resident_email)) {
    $subject = "Document Request Validated - Barangay Bagbag";

    $body = "
        <html>
        <head>
            <title>Document Validation</title>
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
                    background: linear-gradient(135deg, #2e7d32, #4caf50);
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
                    background-color: #fff8e1; /* Light Yellow */
                    border-left: 4px solid #4caf50;
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
                <div class='header'>Barangay BagbagCare</div>
                <div class='content'>
                    <p>Dear <strong>" . htmlspecialchars($record['first_name']) . "</strong>,</p>
                    <p>We are pleased to inform you that your request for a <strong>" . ucfirst(str_replace('_', ' ', $tab)) . "</strong> has been successfully validated by our staff.</p>
                    
                    <div class='status-box'>
                        <strong>Status:</strong> <span style='color: #2e7d32; font-weight: 600;'>Validated</span><br>
                        <strong>Remarks:</strong> " . htmlspecialchars($remarks ?: 'No remarks provided.') . "
                    </div>

                    <p>Please wait for final approval from the official. You will receive another notification once approved.</p>
                    <p>Thank you for choosing Barangay BagbagCare!</p>
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
                echo "<script>alert('Validation successful and notification sent!');</script>";
            } else {
                echo "<script>alert('Validation successful but email could not be sent. Please check logs.'); console.log('Email failed');</script>";
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
  <title>Validate Document</title>
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
        Validate Request
      </h2>

      <p class="text-gray-700 mb-6">
        You are about to validate the request of:
        <strong><?= htmlspecialchars($record['first_name'] . ' ' . $record['middle_name'] . ' ' . $record['last_name']) ?></strong>
        (<em><?= ucfirst(str_replace('_', ' ', $tab)) ?></em>).
      </p>

      <p class="text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded mb-6">
        <i class="fas fa-info-circle mr-2"></i>
        After validation, this request will be sent to the <strong>Official</strong> for final approval.
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
                  class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition flex items-center">
            <i class="fas fa-check-circle mr-2"></i> Mark as Validated
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
      alert("Successfully validated!");
      window.location.href = "S.request_documents.php?tab=<?= htmlspecialchars($tab) ?>&status=Validated";
    <?php endif; ?>
  </script>
</body>
</html>