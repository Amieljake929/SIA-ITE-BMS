<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

/* Use shared DB config that points to DB: bms */
require_once '../../login/db_connect.php'; // ensure this connects to database 'bms'

/* Fetch images from programs table */
$images = [];
$sql = "SELECT id, file_name, file_path, created_at FROM programs ORDER BY created_at DESC, id DESC";
if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        // stored: uploads/programs/xxx.jpg  (relative to staff/Programs)
        $row['public_src'] = '../../staff/Programs/' . ltrim($row['file_path'], '/');
        $images[] = $row;
    }
    $res->free();
}

// $conn->close(); // <-- REMOVED FROM HERE
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bagbag eServices - Official Portal</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">LOADING...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../resident_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Programs | Resident</h1>
        </div>

        <div class="relative inline-block text-right">
          <button id="userMenuButton" class="flex items-center font-medium cursor-pointer text-sm focus:outline-none whitespace-nowrap">
            <span class="text-gray-800">Logged in:</span>
            <span class="text-blue-700 ml-1"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
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
                <a href="../../login/logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                  <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
                </a>
              </li>
            </ul>
          </div>
        </div>
    </div>
  </header>

  <main class="container mx-auto px-6 py-8 w-full max-w-7xl">

    <section class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
          <i class="fa-solid fa-bullhorn mr-3 text-green-700"></i> Announcements
        </h2>
        <p class="text-sm text-gray-500"><?php echo count($images); ?> announcement(s)</p>
      </div>

      <?php if (empty($images)): ?>
        <div class="text-gray-500 text-sm">No announcements available yet.</div>
      <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <?php foreach ($images as $img): ?>
            <div class="group bg-gray-50 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200">
              <button type="button" class="w-full text-left" onclick="openViewer('<?php echo htmlspecialchars($img['public_src']); ?>','<?php echo htmlspecialchars($img['file_name']); ?>')">
                <div class="relative pt-[60%] bg-gray-100">
                  <img
                    src="<?php echo htmlspecialchars($img['public_src']); ?>"
                    alt="announcement image"
                    class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
                </div>
                <div class="p-4">
                  <h3 class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2" title="<?php echo htmlspecialchars($img['file_name']); ?>">
                    <?php echo htmlspecialchars($img['file_name']); ?>
                  </h3>
                  <div class="text-xs text-gray-500">
                    <i class="fa-solid fa-calendar-days mr-1"></i><?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($img['created_at']))); ?>
                  </div>
                </div>
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <div class="mt-8 text-center">
      <?php
      // Check if anti rabies program is enabled
      $antiRabiesEnabled = false;
      $settingsQuery = $conn->query("SELECT is_enabled FROM program_settings WHERE program_name = 'anti_rabies'");
      if ($settingsQuery && $row = $settingsQuery->fetch_assoc()) {
          $antiRabiesEnabled = $row['is_enabled'];
      }
      if ($antiRabiesEnabled) {
      ?>
        <a href="R.anti_rabies_registration.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
          <i class="fas fa-syringe mr-2"></i> Anti Rabies Registration
        </a>
      <?php } else { ?>
        <div class="inline-block bg-gray-400 text-white font-semibold px-6 py-3 rounded-lg shadow cursor-not-allowed">
          <i class="fas fa-syringe mr-2"></i> Anti Rabies Registration (Disabled)
        </div>
      <?php 
      } 
      $conn->close(); // <-- MOVED TO HERE
      ?>
    </div>
  </main>

  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

  <div id="viewer" class="fixed inset-0 bg-black bg-opacity-80 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-5xl w-full">
      <button class="absolute -top-10 right-0 text-white text-2xl" onclick="closeViewer()" aria-label="Close">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <img id="viewerImg" src="" alt="" class="w-full max-h-[80vh] object-contain rounded-lg shadow-lg" />
      <div id="viewerCap" class="mt-3 text-gray-200 text-sm"></div>
    </div>
  </div>

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

  <script>
    // Clock
    function updateTime() {
      const now = new Date();
      const options = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' };
      document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();
    }
    setInterval(updateTime, 1000); updateTime();

    // User Dropdown Toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown   = document.getElementById('userDropdown');
    if (userMenuButton) {
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

    // Lightbox viewer
    const viewer    = document.getElementById('viewer');
    const viewerImg = document.getElementById('viewerImg');
    const viewerCap = document.getElementById('viewerCap');

    function openViewer(src, caption) {
      viewerImg.src = src;
      viewerCap.textContent = caption || '';
      viewer.classList.remove('hidden');
      viewer.classList.add('flex');
    }
    function closeViewer() {
      viewer.classList.add('hidden');
      viewer.classList.remove('flex');
      viewerImg.src = '';
      viewerCap.textContent = '';
    }
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeViewer();
    });
    viewer && viewer.addEventListener('click', (e) => {
      if (e.target === viewer) closeViewer();
    });
  </script>
</body>
</html>