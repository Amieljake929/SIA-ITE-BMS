

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bagbag eServices - Official Portal</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    /* Remove default video controls style */
    video {
      object-fit: cover;
    }

    #video-banner {
      height: 60vh;
      min-height: 300px;
    }

    @media (min-width: 768px) {
      #video-banner {
        height: 70vh;
      }
    }

    @media (min-width: 1024px) {
      #video-banner {
        height: 80vh;
      }
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">THURSDAY, AUGUST 7, 2025, 11:16:33 AM</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <!-- Main Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
      <div class="flex items-center space-x-4"></div>

      <!-- User Info with Dropdown -->
      <div class="relative inline-block text-right">
        <button id="userMenuButton" class="flex items-center font-medium cursor-pointer text-sm focus:outline-none whitespace-nowrap">
          <span class="text-gray-800">Logged in:</span>
          <span class="text-blue-700 ml-1">
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

  <!-- Background Video Section -->
  <section id="video-banner" class="relative w-full overflow-hidden">
    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
      <!-- Gamitin ang sariling video path -->
      <source src="../images/bagbag.mp4" type="video/mp4">
      <!-- Kung hindi suportado ang video -->
      Your browser does not support the video tag.
    </video>

    <!-- Overlay para sa text -->
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>

    <!-- Text Overlay -->
    <div class="absolute bottom-10 left-6 text-white max-w-lg z-10">
      <h2 class="text-2xl md:text-3xl font-bold">Welcome to Bagbag</h2>
      <p class="text-sm md:text-base mt-1 hidden sm:block">Your official digital gateway to barangay services.</p>
    </div>
  </section>

  <!-- Scroll Indicator -->
  <div class="flex justify-center mt-4">
    <div class="text-white text-2xl animate-bounce">
      <i class="fas fa-chevron-down text-green-700 bg-white bg-opacity-20 p-2 rounded-full w-12 h-12 flex items-center justify-center"></i>
    </div>
  </div>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8 flex-grow">
    <h1 class="text-2xl md:text-3xl font-bold text-center text-green-800 mb-10 tracking-wide">
      PLEASE CHOOSE FROM THE FOLLOWING SERVICES
    </h1>

    <!-- Service Cards Grid -->
    <div class="grid grid-cols-2 gap-5 sm:gap-6 max-w-4xl mx-auto mb-12">
      <!-- Card 1 -->
      <a href="#" class="group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-gavel"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Official Committee</h3>
      </a>

      <!-- Card 2 -->
      <a href="#" class="group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-phone-alt"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Emergency Contact</h3>
      </a>

      <!-- Card 3 -->
      <a href="#" class="group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Programs</h3>
      </a>

      <!-- Card 4 -->
      <a href="#" class="group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-file-signature"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Submit Request</h3>
      </a>

      <!-- Card 5 -->
      <a href="#" class="group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Blotter & Reports</h3>
      </a>

      <!-- Card 6 -->
      <a href="#" class="group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-users"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Community Reports</h3>
      </a>
    </div>

    <!-- Vision, Mission, and History Section -->
    <section class="bg-white p-6 rounded-xl shadow-md mb-8 max-w-6xl mx-auto">
      <h2 class="text-2xl font-bold text-green-800 text-center mb-6">Our Vision, Mission & History</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Vision -->
        <div class="p-5 border border-gray-100 rounded-lg bg-gray-50 hover:shadow-sm transition-shadow duration-200">
          <h3 class="text-xl font-semibold text-green-700 mb-3 flex items-center">
            <i class="fas fa-eye mr-2 text-green-600"></i> Vision
          </h3>
          <p class="text-gray-600 leading-relaxed">
            A progressive, united, and empowered community where every resident enjoys peace, prosperity, and quality of life.
          </p>
        </div>

        <!-- Mission -->
        <div class="p-5 border border-gray-100 rounded-lg bg-gray-50 hover:shadow-sm transition-shadow duration-200">
          <h3 class="text-xl font-semibold text-green-700 mb-3 flex items-center">
            <i class="fas fa-bullseye mr-2 text-green-600"></i> Mission
          </h3>
          <p class="text-gray-600 leading-relaxed">
            To provide responsive governance, promote sustainable development, and foster active citizen participation for a safer and more inclusive barangay.
          </p>
        </div>

        <!-- History -->
        <div class="p-5 border border-gray-100 rounded-lg bg-gray-50 hover:shadow-sm transition-shadow duration-200">
          <h3 class="text-xl font-semibold text-green-700 mb-3 flex items-center">
            <i class="fas fa-book mr-2 text-green-600"></i> History
          </h3>
          <p class="text-gray-600 leading-relaxed">
            Bagbag has a rich history of community resilience and cooperation. From its humble beginnings, it has grown into a vibrant urban barangay committed to service and progress.
          </p>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

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