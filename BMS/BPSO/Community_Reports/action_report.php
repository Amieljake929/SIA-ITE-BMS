<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BPSO') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid report ID.");
}

$report_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

include '../../login/db_connect.php';

// Fetch report
$stmt = $conn->prepare("SELECT * FROM community_reports WHERE id = ?");
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

if (!$report) {
    $error = "Report not found.";
} else {
    // Authorization: Check if assigned to this BPSO
    if ((int)$report['assigned_to'] !== (int)$user_id) {
        $error = "Unauthorized: This report is not assigned to you.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($error)) {
    $status = $_POST['status'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');

    if (!in_array($status, ['Assigned', 'For Closing'])) {
        $error = "Invalid status selected.";
    } else {
        $stmt = $conn->prepare("UPDATE community_reports SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $status, $remarks, $report_id);
        if ($stmt->execute()) {
            $success = "Report updated successfully.";
            // Refresh data
            $report['status'] = $status;
            $report['bpso_remarks'] = $remarks;
        } else {
            $error = "Failed to update report.";
        }
        $stmt->close();
    }
}

/* -------------------- helpers for evidence rendering -------------------- */
function normalize_media_paths($raw) {
    // Try JSON array first
    $arr = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
        return $arr;
    }
    // Fallback: comma-separated or single string
    $raw = trim((string)$raw);
    if ($raw === '') return [];
    if (strpos($raw, ',') !== false) {
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
    return [$raw];
}

function to_web_path($file) {
    // Normalize slashes
    $f = str_replace('\\', '/', $file);

    // If absolute Windows path, keep portion from 'uploads/...'
    if (preg_match('~uploads/.*~', $f, $m)) {
        $f = $m[0];
    }

    // Already http(s) or site-root path
    if (preg_match('~^https?://~i', $f) || str_starts_with($f, '/')) {
        return $f;
    }

    // If begins with uploads/, files are under resident/Community_Reports
    if (str_starts_with($f, 'uploads/')) {
        // from BPSO/Community_Reports/* to resident/Community_Reports/uploads/*
        return '../../resident/Community_Reports/' . $f;
    }

    // Otherwise treat as-is (relative)
    return $f;
}

function is_image_ext($ext) {
    return in_array(strtolower($ext), ['jpg','jpeg','png','gif','bmp','webp'], true);
}
function is_video_ext($ext) {
    return in_array(strtolower($ext), ['mp4','webm','ogg','ogv','mov','avi','mkv'], true);
}
function video_mime($ext) {
    $map = [
        'mp4'  => 'video/mp4',
        'webm' => 'video/webm',
        'ogg'  => 'video/ogg',
        'ogv'  => 'video/ogg',
        'mov'  => 'video/quicktime',
        'avi'  => 'video/x-msvideo',
        'mkv'  => 'video/x-matroska',
    ];
    return $map[strtolower($ext)] ?? 'video/mp4';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Review Community Report #<?= $report_id ?> - Bagbag eServices</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    body { font-family: 'Inter', sans-serif; }
    .status-assigned { @apply bg-blue-100 text-blue-800; }
    .status-for-closing { @apply bg-purple-100 text-purple-800; }
    .status-completed { @apply bg-green-100 text-green-800; }
    .status-rejected { @apply bg-red-100 text-red-800; }
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
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../BPSO_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Community Report - BPSO #<?= $report_id ?></h1>
        </div>

      <!-- User Info -->
      <div class="text-sm text-gray-600">
        Logged in as: <span class="font-medium text-blue-700"><?= htmlspecialchars($_SESSION['full_name'] ?? 'BPSO Officer') ?></span>
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
      <?php if (!isset($error) && $report): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <!-- Incident Details -->
          <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-2xl font-bold text-green-800 mb-6 border-b pb-2 border-green-200">Community Report Details</h2>

            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Community Report ID</label>
                <p class="text-lg font-medium"><?= htmlspecialchars($report['community_report_id']) ?></p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Complainant</label>
                <p class="text-lg font-medium"><?= htmlspecialchars("{$report['first_name']} {$report['middle_name']} {$report['last_name']}") ?></p>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Incident Details</label>
                <p class="whitespace-pre-wrap text-gray-800"><?= htmlspecialchars($report['incident_details']) ?></p>
              </div>

              <?php if (!empty($report['evidence_path'])): ?>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Evidence</label>
                  <?php 
                    $evidence_files = normalize_media_paths($report['evidence_path']);
                  ?>
                  <?php if (!empty($evidence_files)): ?>
                    <div class="mt-2 space-y-4">
                      <?php foreach($evidence_files as $file): 
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $src = htmlspecialchars(to_web_path($file));
                      ?>
                        <?php if (is_image_ext($ext)): ?>
                          <!-- IMAGE with zoom -->
                          <div class="border rounded-lg p-2">
                            <img src="<?= $src ?>" alt="Evidence Image" class="max-w-full h-auto max-h-64 rounded cursor-pointer" onclick="openModal('<?= $src ?>')">
                          </div>
                        <?php elseif (is_video_ext($ext)): ?>
                          <!-- VIDEO -->
                          <div class="border rounded-lg p-2">
                            <video controls class="max-w-full h-auto max-h-96 rounded">
                              <source src="<?= $src ?>" type="<?= video_mime($ext) ?>">
                              Your browser does not support the video tag.
                            </video>
                          </div>
                        <?php else: ?>
                          <!-- OTHER FILE (fallback link) -->
                          <div class="border rounded-lg p-2 bg-gray-50">
                            <i class="fas fa-file mr-2"></i>
                            <a href="<?= $src ?>" target="_blank" class="text-blue-600 hover:underline">
                              <?= htmlspecialchars(basename($file)) ?>
                            </a>
                          </div>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <p class="text-gray-500 italic"><?= htmlspecialchars($report['evidence_path']) ?></p>
                  <?php endif; ?>
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
                <?= $report['status'] === 'Assigned' ? 'status-assigned' : '' ?>
                <?= $report['status'] === 'For Closing' ? 'status-for-closing' : '' ?>
                <?= $report['status'] === 'Completed' ? 'status-completed' : '' ?>
                <?= $report['status'] === 'Rejected' ? 'status-rejected' : '' ?>
              ">
                <?= htmlspecialchars($report['status']) ?>
              </span>
            </div>

            <!-- Update Form -->
            <form method="POST" class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status & Add Remarks</h3>

              <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="Assigned" <?= $report['status'] === 'Assigned' ? 'selected' : '' ?>>Assigned / Still Working</option>
                  <option value="For Closing" <?= $report['status'] === 'For Closing' ? 'selected' : '' ?>>For Closing</option>
                </select>
              </div>

              <div class="mb-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Your Remarks / Actions Taken</label>
                <textarea name="remarks" id="remarks" rows="4"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Describe the action you took, investigation result, or conversation with the parties involved..."><?= htmlspecialchars($report['remarks'] ?? '') ?></textarea>
              </div>

              <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-200 flex items-center justify-center">
                <i class="fas fa-save mr-2"></i> Save Update
              </button>
            </form>

            <!-- Back Button -->
            <div>
              <a href="B.community_reports.php" class="w-full inline-block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
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
    &copy; <?= date('Y') ?> BagbagCare. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

  <!-- Image Preview Modal -->
  <div id="imgModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
    <span class="absolute top-5 right-8 text-white text-4xl font-bold cursor-pointer" onclick="closeModal()">&times;</span>
    <img id="modalImg" src="" class="max-h-[90%] max-w-[90%] rounded shadow-lg">
  </div>

  <!-- JavaScript -->
  <script>
    // Update time
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

    // Image modal
    function openModal(src) {
      document.getElementById("imgModal").classList.remove("hidden");
      document.getElementById("modalImg").src = src;
    }
    function closeModal() {
      document.getElementById("imgModal").classList.add("hidden");
    }
  </script>
</body>
</html>
