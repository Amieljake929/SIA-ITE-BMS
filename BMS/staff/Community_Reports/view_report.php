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
    // Windows absolute -> keep from 'uploads/' onward
    $f = str_replace('\\', '/', $file);
    if (preg_match('~uploads/.*~', $f, $m)) {
        $f = $m[0]; // ex: uploads/reports/xxx.png
    }

    // Already URL or absolute site path
    if (preg_match('~^https?://~i', $f) || str_starts_with($f, '/')) {
        return $f;
    }

    // Files live under resident/Community_Reports/uploads relative to this file
    if (str_starts_with($f, 'uploads/')) {
        return '../../resident/Community_Reports/' . $f;
    }

    // Default: return as is
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
    $ext = strtolower($ext);
    return $map[$ext] ?? 'video/mp4';
}
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
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../staff_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>
            <h1 class="text-xl font-bold text-green-800">Community Reports</h1>
        </div>
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

      <!-- Evidence -->
      <?php if (!empty($report['evidence_path'])): ?>
        <div class="border-t pt-4">
          <p class="mb-2"><strong>Evidence:</strong></p>
          <?php $files = normalize_media_paths($report['evidence_path']); ?>
          <?php if (!empty($files)): ?>
            <div class="space-y-4">
              <?php foreach ($files as $rawFile): 
                $ext = strtolower(pathinfo($rawFile, PATHINFO_EXTENSION));
                $web = htmlspecialchars(to_web_path($rawFile));
              ?>
                <?php if (is_image_ext($ext)): ?>
                  <!-- IMAGE with click-to-zoom -->
                  <div class="border rounded-lg p-2">
                    <img src="<?= $web ?>" 
                         alt="Evidence Image" 
                         class="max-w-full h-auto max-h-64 rounded cursor-pointer"
                         onclick="openModal('<?= $web ?>')">
                    <div class="text-xs mt-1 text-gray-500 break-all"><?= htmlspecialchars($rawFile) ?></div>
                  </div>
                <?php elseif (is_video_ext($ext)): ?>
                  <!-- VIDEO -->
                  <div class="border rounded-lg p-2">
                    <video controls preload="metadata" class="max-w-full h-auto max-h-96 rounded">
                      <source src="<?= $web ?>" type="<?= video_mime($ext) ?>">
                      Your browser does not support the video tag.
                    </video>
                    <div class="text-xs mt-1 text-gray-500 break-all"><?= htmlspecialchars($rawFile) ?></div>
                  </div>
                <?php else: ?>
                  <!-- OTHER FILE (fallback) -->
                  <div class="border rounded-lg p-2 bg-gray-50">
                    <i class="fas fa-file mr-2"></i>
                    <a href="<?= $web ?>" target="_blank" class="text-blue-600 hover:underline break-all">
                      <?= basename($rawFile) ?>
                    </a>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-gray-500 italic">No evidence found.</p>
          <?php endif; ?>
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
    &copy; <?= date('Y') ?> BagbagCare. All rights reserved.
  </footer>

  <!-- Image Preview Modal -->
  <div id="imgModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
    <span class="absolute top-5 right-8 text-white text-4xl font-bold cursor-pointer" onclick="closeModal()">&times;</span>
    <img id="modalImg" src="" class="max-h-[90%] max-w-[90%] rounded shadow-lg">
  </div>

  <script>
    // date/time header
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();

    // modal functions for image zoom
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
