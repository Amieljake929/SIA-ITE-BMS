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


// Get report
$sql = "SELECT * FROM community_reports WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Report not found.");
}
$report = $result->fetch_assoc();

// Get BPSOs
$bpso_query = "SELECT id, full_name FROM users WHERE role = 'BPSO'";
$bpso_result = $conn->query($bpso_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Assign Report - Bagbag eServices</title>
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
    <a href="S.community_reports.php?id=<?= $report['id'] ?>" class="inline-flex items-center text-green-700 hover:text-green-900 mb-6">
      <i class="fas fa-arrow-left mr-2"></i> Back to Report
    </a>

    <div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto">
      <h2 class="text-xl font-semibold mb-6">Assign Report #<?= $report['id'] ?></h2>

      <form method="POST" action="process_assign.php">
        <input type="hidden" name="report_id" value="<?= $report['id'] ?>">

        <div class="mb-5">
          <label class="block text-sm font-medium text-gray-700 mb-2">Select BPSO</label>
          <select name="bpso_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <option value="">-- Choose BPSO --</option>
            <?php while ($bpso = $bpso_result->fetch_assoc()): ?>
              <option value="<?= $bpso['id'] ?>">
                <?= htmlspecialchars($bpso['full_name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="flex justify-end space-x-3">
          <a href="view_report.php?id=<?= $report['id'] ?>" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
            Cancel
          </a>
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Assign Report
          </button>
        </div>
      </form>
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

<?php $conn->close(); ?>