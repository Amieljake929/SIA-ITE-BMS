<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BagbagCare - Resident Portal</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />


    

  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">THURSDAY, AUGUST 7, 2025, 11:16:33 AM</span>
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

            <h1 class="text-xl font-bold text-green-800">Official Committee</h1>
        </div>

    

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
              <a href="../login/logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  
  <main class="flex-grow">
    <div class="container mx-auto px-6 py-16">
      
      <h2 class="text-4xl font-extrabold text-center text-green-900 mb-16 tracking-wide">BARANGAY COUNCIL</h2>

      <div class="flex flex-col items-center mb-20">
        <img src="../../images/Kap.png" alt="RICHARD V. AMBITA, MPA" class="w-64 h-auto mb-5">
        <h3 class="text-2xl font-bold text-gray-900 tracking-wide">RICHARD V. AMBITA, MPA</h3>
        <p class="text-lg text-green-800 font-semibold mt-1">PUNONG BARANGAY</p>
        <p class="text-md text-red-600 font-medium mt-2 text-center max-w-md">CHAIRPERSON OF ALL BARANGAY-BASED INSTITUTIONS AND COMMITTEE</p>
      </div>

      <div class="flex flex-wrap justify-center gap-x-12 gap-y-16 mb-16">
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad1.png" alt="BETTY L. VITANGCOL" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">BETTY L. VITANGCOL</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY KAGAWAD</p>
        </div>
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad2.png" alt="JAYSON SJ. PALIZA" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">JAYSON SJ. PALIZA</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY KAGAWAD</p>
        </div>
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad3.png" alt="LORD MICHAEL ANTHONY A." class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">LORD MICHAEL ANTHONY A.</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY KAGAWAD</p>
        </div>
      </div>

      <div class="flex flex-wrap justify-center gap-x-12 gap-y-16 mb-16">
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad4.png" alt="JESUS DP. VILLAMOR" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">JESUS DP. VILLAMOR</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY KAGAWAD</p>
            <p class="text-sm text-red-600 font-medium mt-2">VICE CHAIRMAN FOR ANY COMMITTEE</p>
        </div>
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad5.png" alt="RICO S. CALESTERIO" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">RICO S. CALESTERIO</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY KAGAWAD</p>
            <p class="text-sm text-red-600 font-medium mt-2">EMPLOYMENT / HEALTH AND SANITATION / SOLID WASTE</p>
        </div>
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad6.png" alt="REYNALDO T. LLEGADO" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">REYNALDO T. LLEGADO</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY KAGAWAD</p>
            <p class="text-sm text-red-600 font-medium mt-2">BDRRMC / BPOC / VICE CHAIR ON COMMITTEE ON TRANSPORTATION</p>
        </div>
      </div>

      <div class="flex flex-wrap justify-center gap-x-12 gap-y-16 mb-16">
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad7.png" alt="AARON KYLE R. MELGAR" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">AARON KYLE R. MELGAR</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY KAGAWAD</p>
            <p class="text-sm text-red-600 font-medium mt-2">LIVELIHOOD / DAP / WAYS AND MEANS</p>
        </div>
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad8.png" alt="PATRICK KAILE F. LIWANAG" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">PATRICK KAILE F. LIWANAG</h3>
            <p class="text-md text-green-800 font-semibold mt-1">SANGGUNIANG KABATAAN CHAIRPERSON</p>
            <p class="text-sm text-red-600 font-medium mt-2">EDUCATION / SPORTS / PHYSICAL FITNESS</p>
        </div>
      </div>

      <div class="flex flex-wrap justify-center gap-x-12 gap-y-16">
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad9.png" alt="ROLANDO V. PASCOR" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">ROLANDO V. PASCOR</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY SECRETARY</p>
        </div>
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad10.png" alt="EVARISTA K. PELAYO" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">EVARISTA K. PELAYO</h3>
            <p class="text-md text-green-800 font-semibold mt-1">BARANGAY TREASURER</p>
        </div>
        <div class="flex flex-col items-center text-center w-72">
            <img src="../../images/Kagawad11.png" alt="ROLANDO H. MEJILA" class="w-64 h-auto mb-5">
            <h3 class="text-xl font-bold text-gray-900 tracking-wide">ROLANDO H. MEJILA</h3>
            <p class="text-md text-green-800 font-semibold mt-1">NICKNAME: BHOJIE - BARANGAY ADMINISTRATOR</p>
        </div>
      </div>

    </div>
  </main>
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

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