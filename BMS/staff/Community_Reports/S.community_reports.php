<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

include '../../login/db_connect.php';


$sql = "SELECT * FROM community_reports 
        WHERE status IN ('Pending', 'For Assignment') 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Community Reports - Bagbag eServices</title>
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

  <!-- Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">
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
  <main class="container mx-auto px-6 py-8 flex-grow">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Pending Reports</h2>

    <?php if ($result->num_rows > 0): ?>
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-green-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Resident</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Incident</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium"><?= htmlspecialchars($row['id']) ?></td>
                <td class="px-6 py-4 text-sm"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td class="px-6 py-4 text-sm"><?= htmlspecialchars($row['incident_type']) ?></td>
                <td class="px-6 py-4 text-sm"><?= htmlspecialchars($row['incident_date']) ?></td>
                <td class="px-6 py-4 text-sm">
                  <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800"><?= htmlspecialchars($row['status']) ?></span>
                </td>
                <td class="px-6 py-4 text-sm space-x-2">
                  <a href="view_report.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-eye"></i> View
                  </a>
                  <a href="assign_report.php?id=<?= $row['id'] ?>" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-share"></i> Assign
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-gray-500 text-center py-8">No pending reports at the moment.</p>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved.
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

<?php $conn->close(); ?>