<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: ../login/login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';


// Fetch reports for approval
$query = "
    SELECT 
        b.id,
        b.complainant_first_name,
        b.complainant_last_name,
        b.incident_type,
        b.incident_date,
        b.status,
        b.report_date,
        b.blotter_id,
        u.full_name AS bpso_name
    FROM blotter_and_reports b
    LEFT JOIN users u ON b.assigned_to_bpso_id = u.id
    WHERE b.status = 'For Approval'
    ORDER BY b.report_date DESC
";

$result = $conn->query($query);
$reports = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Approve Blotter Reports - Bagbag eServices</title>

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
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">

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

            <h1 class="text-xl font-bold text-green-800">Blotter Reports</h1>
        </div>

      <!-- User Info with Dropdown -->
      <div class="relative inline-block text-right">
        <button id="userMenuButton" class="flex items-center font-medium cursor-pointer text-sm focus:outline-none whitespace-nowrap">
          <span class="text-gray-800">Logged in:</span>
          <span class="text-blue-700 ml-1">
            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
          </span>
          <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
        </button>

        <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl hidden z-10">
          <ul class="py-2 text-sm">
            <li>
              <a href="#" class="block px-5 py-2 text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-user text-green-600 mr-3"></i> Profile
              </a>
            </li>
            <li>
              <a href="../../login/logout_official.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
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
    <div class="max-w-6xl mx-auto">

      <h2 class="text-2xl font-bold text-green-800 mb-6">Reports Awaiting Approval</h2>

      <?php if (empty($reports)): ?>
        <div class="bg-white p-8 rounded-xl shadow text-center text-gray-600">
          <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
          <p>No reports are currently pending approval.</p>
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead class="bg-green-100 text-gray-800">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold">Blotter ID</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Complainant</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Incident</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">BPSO</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php foreach ($reports as $r): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars($r['blotter_id']) ?></td>
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars("{$r['complainant_first_name']} {$r['complainant_last_name']}") ?></td>
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars($r['incident_type']) ?></td>
                  <td class="px-4 py-3 text-sm"><?= $r['incident_date'] ?></td>
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars($r['bpso_name'] ?? 'Not Assigned') ?></td>
                  <td class="px-4 py-3 text-sm">
                    <span class="px-2 py-1 text-xs rounded-full status-approval font-medium">
                      <?= $r['status'] ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-sm">
                    <a href="O.view_report.php?id=<?= $r['id'] ?>" class="text-green-600 hover:text-green-800 font-medium text-sm">
                      <i class="fas fa-check-circle mr-1"></i> Review & Close
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
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
      const formattedDate = now.toLocaleString('en-US', options);
      document.getElementById('datetime').textContent = formattedDate.toUpperCase();
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