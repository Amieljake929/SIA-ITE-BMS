<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../login/db_connect.php';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BagbagCare - Residents Portal</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    /* Smooth transition for slideshow */
    #slideshow {
      transition: background-image 1s ease-in-out;
      cursor: pointer;
      position: relative;
    }

    #slideshow:hover::after {
      content: 'Click to view';
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      background-color: rgba(0, 0, 0, 0.6);
      color: white;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 0.875rem;
      opacity: 1;
      transition: opacity 0.3s;
    }

    /* Ensure 2 cards per row on all devices */
    .card-container {
      width: calc(107% - 0.75rem);
    }

    /* Lightbox Modal */
    #lightbox {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    #lightbox.show {
      opacity: 1;
    }

    #lightbox img {
      max-width: 90%;
      max-height: 90vh;
      object-fit: contain;
      border-radius: 8px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }

    /* Responsive slideshow height */
    #slideshow {
      height: 16rem;
    }

    @media (min-width: 640px) {
      #slideshow { height: 26rem; }
    }
    @media (min-width: 768px) {
      #slideshow { height: 28rem; }
    }
    @media (min-width: 1024px) {
      #slideshow { height: 30rem; }
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
            <h1 class="text-xl font-bold text-green-800">BagbagCare | Resident</h1>
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
              <a href="../login/logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- Slideshow (Clickable) -->
  <section id="slideshow" class="w-full bg-center bg-cover bg-no-repeat relative cursor-pointer transition-all duration-1000 ease-in-out" onclick="openLightbox()">
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    <div class="absolute bottom-6 left-6 text-white max-w-lg z-10">
      <h2 id="slideTitle" class="text-2xl md:text-3xl font-bold"></h2>
      <p id="slideDesc" class="text-sm md:text-base mt-1 hidden sm:block"></p>
    </div>
  </section>

  <!-- Lightbox Modal -->
  <div id="lightbox" class="hidden fixed inset-0 items-center justify-center">
    <span id="closeLightbox" class="absolute top-5 right-5 text-white text-3xl cursor-pointer hover:text-gray-300 z-20">&times;</span>
    <img id="lightboxImage" src="" alt="Enlarged slide image" />
  </div>

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
      <a href="Official_Committee/R.official_committee.php" class="card-container group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-gavel"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Official Committee</h3>
      </a>

      <!-- Card 2 -->
      <a href="#" class="card-container group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-phone-alt"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Emergency Contact</h3>
      </a>

      <!-- Card 3 -->
      <a href="Proragms/R_programs.php" class="card-container group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Programs</h3>
      </a>

      <!-- Card 4 -->
      <a href="R.submit_request.php" class="card-container group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-file-signature"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Submit Request</h3>
      </a>

      <!-- Card 5 -->
      <a href="Blotter_Reports/R.blotter_reports.php" class="card-container group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
        <div class="text-green-600 mb-3 text-4xl group-hover:scale-110 transition-transform duration-300">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Blotter & Reports</h3>
      </a>

      <!-- Card 6 -->
      <a href="Community_Reports/R.community_report.php" class="card-container group bg-white border border-gray-200 p-5 rounded-xl shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-44">
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

    // Slideshow Configuration
    const slides = [
      { img: '../images/4-bagbag.png', title: 'Welcome to Bagbag', desc: 'Your official digital gateway to barangay services.' },
      { img: '../images/BARANGAY-HOTLINE.png', title: 'Community First', desc: 'Empowering residents through digital access.' },
      { img: '../images/committess.png', title: 'Emergency Ready', desc: 'Quick access to emergency contacts and support.' },
      { img: '../images/A.jpg', title: 'Barangay Programs', desc: 'Stay updated with upcoming events and initiatives.' },
      { img: '../images/B.jpg', title: 'Official Services', desc: 'Apply, report, and request online with ease.' },
    ];

    let currentSlide = 0;

    function showSlide(index) {
      const slide = slides[index];
      const slideshow = document.getElementById('slideshow');
      slideshow.style.backgroundImage = `url('${slide.img}')`;
      document.getElementById('slideTitle').textContent = slide.title;
      document.getElementById('slideDesc').textContent = slide.desc;
    }

    // Auto-cycle slides
    setInterval(() => {
      currentSlide = (currentSlide + 1) % slides.length;
      showSlide(currentSlide);
    }, 5000);

    // Initial slide
    showSlide(currentSlide);

    // Lightbox Functions
    function openLightbox() {
      const imgSrc = slides[currentSlide].img;
      const lightbox = document.getElementById('lightbox');
      const lightboxImg = document.getElementById('lightboxImage');
      lightboxImg.src = imgSrc;
      lightbox.style.display = 'flex';
      setTimeout(() => lightbox.classList.add('show'), 10);
    }

    function closeLightbox() {
      const lightbox = document.getElementById('lightbox');
      lightbox.classList.remove('show');
      setTimeout(() => {
        lightbox.style.display = 'none';
      }, 300);
    }

    // Close with X button
    document.getElementById('closeLightbox').addEventListener('click', closeLightbox);

    // Close when clicking outside image
    document.getElementById('lightbox').addEventListener('click', (e) => {
      if (e.target === e.currentTarget) closeLightbox();
    });

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