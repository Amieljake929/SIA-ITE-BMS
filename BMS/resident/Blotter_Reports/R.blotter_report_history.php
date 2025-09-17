<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch all blotter reports of the resident
$sql = "SELECT 
            br.id,
            br.incident_type,
            br.created_at,
            br.status,
            br.assigned_to_bpso_id,
            u.full_name as bpso_name
        FROM blotter_and_reports br
        LEFT JOIN users u ON br.assigned_to_bpso_id = u.id
        WHERE br.user_id = ?
        ORDER BY br.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Blotter Report History - Bagbag eServices</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    .status-pending {
      @apply px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800;
    }
    .status-assigned {
      @apply px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800;
    }
    .status-closing {
      @apply px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800;
    }
    .status-completed {
      @apply px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800;
    }
    .status-other {
      @apply px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800;
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
      <h1 class="text-xl font-bold text-green-800">Blotter Report History</h1>

      <!-- User Info with Dropdown -->
      <div class="relative inline-block text-right">
        <button id="userMenuButton" class="flex items-center font-medium cursor-pointer text-sm focus:outline-none whitespace-nowrap">
          <span class="text-gray-800">Logged in:</span>
          <span class="text-blue-700 ml-1"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
          <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
        </button>

        <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl hidden z-10">
          <ul class="py-2 text-sm">
            <li>
              <a href="R.blotter_reports.php" class="block px-5 py-2 text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-exclamation-triangle text-green-600 mr-3"></i> New Report
              </a>
            </li>
            <li>
              <a href="../../login/logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow px-6 py-8">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">

      <h2 class="text-2xl font-bold text-green-800 mb-2">Your Blotter Reports</h2>
      <p class="text-gray-600 mb-8">Below is the list of all your submitted blotter reports and their current status.</p>

      <?php if ($result->num_rows > 0): ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Report #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Incident</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Date Submitted</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Assigned To</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php while ($report = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 text-sm font-medium"><?= htmlspecialchars($report['id']) ?></td>
                  <td class="px-6 py-4 text-sm"><?= htmlspecialchars($report['incident_type']) ?></td>
                  <td class="px-6 py-4 text-sm"><?= date('M d, Y', strtotime($report['created_at'])) ?></td>
                  <td class="px-6 py-4 text-sm">
                    <?php
                    $status = $report['status'];
                    $class = match($status) {
                        'Pending' => 'status-pending',
                        'Assigned' => 'status-assigned',
                        'For Closing' => 'status-closing',
                        'Completed' => 'status-completed',
                        default => 'status-other'
                    };
                    ?>
                    <span class="<?= $class ?>"><?= htmlspecialchars($status) ?></span>
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <?= $report['bpso_name'] ? htmlspecialchars($report['bpso_name']) : '<span class="text-gray-400">Not assigned yet</span>' ?>
                  </td>
                  <td class="px-6 py-4 text-sm font-medium">
                    <!-- You can create a new view page for blotter reports if needed -->
                    <a href="R.view_blotter_report.php?id=<?= $report['id'] ?>" class="text-blue-600 hover:text-blue-800">
                      <i class="fas fa-eye"></i> View Details
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="text-center py-10">
          <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
          <p class="text-gray-500 text-lg">You haven't submitted any blotter report yet.</p>
          <a href="R.blotter_reports.php" class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i> Submit a Blotter Report
          </a>
        </div>
      <?php endif; ?>

      <div class="mt-8 text-center">
        <a href="../resident_dashboard.php" class="text-gray-600 hover:text-gray-800 inline-flex items-center">
          <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
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

    // Dropdown Toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');
    userMenuButton.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', (e) => {
      if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
        userDropdown.classList.add('hidden');
      }
    });
  </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>