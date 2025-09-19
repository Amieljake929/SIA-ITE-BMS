<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: ../login/login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid report ID.");
}

// Fetch report with assigned BPSO name
$stmt = $conn->prepare("
    SELECT 
        cr.*,
        u.full_name AS bpso_name
    FROM community_reports cr
    LEFT JOIN users u ON cr.assigned_to = u.id
    WHERE cr.id = ?
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
    // Absolute Windows path -> keep part starting at 'uploads/'
    $f = str_replace('\\', '/', $file);
    if (preg_match('~uploads/.*~', $f, $m)) {
        $f = $m[0]; // e.g. uploads/reports/xxx.jpg
    }

    // If already absolute URL or site-root path, return as-is
    if (preg_match('~^https?://~i', $f) || str_starts_with($f, '/')) {
        return $f;
    }

    // If it begins with "uploads/", files are under resident/Community_Reports
    if (str_starts_with($f, 'uploads/')) {
        return '../../resident/Community_Reports/' . $f;
    }

    // Otherwise treat as relative to current dir
    return $f;
}

function is_image_ext($ext) {
    return in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'], true);
}
function is_video_ext($ext) {
    return in_array($ext, ['mp4','webm','ogg','ogv','mov','avi','mkv'], true);
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
  <title>View Community Report #<?= $id ?> - Bagbag eServices</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
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
        <div class="flex items-center space-x-4">
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../official_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>
            <h1 class="text-xl font-bold text-green-800">View Community Report # <?= $id ?></h1>
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
          <h2 class="text-2xl font-bold text-green-800 mb-6 border-b pb-2 border-green-200">Community Report Details</h2>

          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Community Report ID</label>
              <p class="text-lg font-medium">#<?= htmlspecialchars($report['community_report_id']) ?></p>
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
              <p class="whitespace-pre-wrap"><?= htmlspecialchars($report['incident_details']) ?></p>
            </div>
            
            <?php if (!empty($report['evidence_path'])): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Evidence</label>
                <?php 
                  $files = normalize_media_paths($report['evidence_path']);
                ?>
                <?php if (!empty($files)): ?>
                  <div class="mt-2 space-y-4">
                    <?php foreach ($files as $rawFile): 
                      $ext = strtolower(pathinfo($rawFile, PATHINFO_EXTENSION));
                      $web = htmlspecialchars(to_web_path($rawFile));
                    ?>
                      <?php if (is_image_ext($ext)): ?>
  <!-- IMAGE -->
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
                        <!-- OTHER FILE -->
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
            
            <?php if (!empty($report['bpso_remarks'])): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">BPSO Remarks / Actions Taken</label>
                <p class="bg-gray-50 p-3 rounded"><?= htmlspecialchars($report['bpso_remarks']) ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>
            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full 
              <?= $report['status'] === 'Assigned' ? 'bg-blue-100 text-blue-800' : '' ?>
              <?= $report['status'] === 'For Closing' ? 'bg-purple-100 text-purple-800' : '' ?>
              <?= $report['status'] === 'Completed' ? 'bg-green-100 text-green-800' : '' ?>
              <?= $report['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : '' ?>
            ">
              <?= htmlspecialchars($report['status']) ?>
            </span>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Assigned BPSO</h3>
            <p><?= htmlspecialchars($report['bpso_name'] ?? 'Not Assigned') ?></p>
          </div>

          <?php if ($report['status'] === 'For Closing'): ?>
            <form method="GET" action="close_report.php" onsubmit="return confirm('Are you sure you want to close this report? This action cannot be undone.');">
              <input type="hidden" name="id" value="<?= $id ?>">
              <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition flex items-center justify-center">
                <i class="fas fa-check-circle mr-2"></i> Close Report
              </button>
            </form>
          <?php elseif ($report['status'] === 'Completed'): ?>
            <div class="bg-green-50 p-4 rounded text-center">
              <i class="fas fa-check-circle text-green-600 text-xl"></i>
              <p class="mt-2 font-medium">Report Closed</p>
            </div>
          <?php else: ?>
            <div class="bg-yellow-50 p-4 rounded text-center">
              <i class="fas fa-info-circle text-yellow-600 text-xl"></i>
              <p class="mt-2 font-medium">Report is still being processed</p>
              <p class="text-sm">Status must be "For Closing" to close this report.</p>
            </div>
          <?php endif; ?>

          <div>
            <a href="O.community_reports.php" class="w-full inline-block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
              <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> BagbagCare. All rights reserved. | Empowering Communities Digitally.
  </footer>

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

  <!-- Image Preview Modal -->
<div id="imgModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
  <span class="absolute top-5 right-8 text-white text-4xl font-bold cursor-pointer" onclick="closeModal()">&times;</span>
  <img id="modalImg" src="" class="max-h-[90%] max-w-[90%] rounded shadow-lg">
</div>

<script>
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
