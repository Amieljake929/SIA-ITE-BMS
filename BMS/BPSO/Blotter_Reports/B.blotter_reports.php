<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BPSO') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';


// FETCH ALL PENDING REPORTS (with complainant and status)
$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        b.id,
        b.complainant_first_name,
        b.complainant_last_name,
        b.incident_type,
        b.incident_date,
        b.status,
        b.created_at
    FROM blotter_and_reports b
    WHERE b.assigned_to_bpso_id = ?
      AND b.status != 'Completed'
    ORDER BY b.updated_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bagbag eServices - Official Portal</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />


    

  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">THURSDAY, AUGUST 7, 2025, 11:16:33 AM</span>
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
                onclick="window.location.href='../BPSO_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Blotter Reports - BPSO</h1>
        </div>

      <div class="flex items-center space-x-4"></div>

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

  
  <main class="px-6 py-8">
  <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-green-800 mb-4">Your Assigned Reports</h2>
    <table class="min-w-full border border-gray-300">
      <thead class="bg-green-100">
        <tr>
          <th>Complainant</th>
          <th>Incident</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reports as $r): ?>
        <tr>
          <td><?= $r['complainant_first_name'] . ' ' . $r['complainant_last_name'] ?></td>
          <td><?= $r['incident_type'] ?></td>
         <td class="px-4 py-2">
            <span class="px-2 py-1 text-xs rounded <?= $r['status'] === 'Pending' ? 'bg-yellow-200' : 'bg-blue-200' ?>">
              <?= $r['status'] ?>
            </span>
          </td>
          <td>
            <a href="B.view_investigation.php?id=<?= $r['id'] ?>" class="text-blue-600">View</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

  
  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-4/5 max-w-xs rounded-lg shadow-xl p-6">
      <h3 class="text-lg font-bold text-gray-800 mb-4">Navigation</h3>
      <ul class="space-y-3">
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Home</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Services</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">About</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Contact</a></li>
        <li><a href="logout.php" class="block text-green-700 hover:text-green-900 font-medium">Logout</a></li>
      </ul>
      <button id="closeMenu" class="mt-4 text-red-500 text-sm">Close</button>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Update time
    function updateTime() {
      const now = new Date();
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      };
      const formattedDate = now.toLocaleString('en-US', options);
      document.getElementById('datetime').textContent = formattedDate.toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();




    // Close with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeLightbox();
    });

    // User Dropdown Toggle
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