<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BPSO') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "bms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define age group queries across both tables
$age_groups = [
    '0-17' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age BETWEEN 0 AND 17) +
                  (SELECT COUNT(*) FROM officials WHERE age BETWEEN 0 AND 17) AS count",
    '18-35' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age BETWEEN 18 AND 35) +
                  (SELECT COUNT(*) FROM officials WHERE age BETWEEN 18 AND 35) AS count",
    '36-60' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age BETWEEN 36 AND 60) +
                  (SELECT COUNT(*) FROM officials WHERE age BETWEEN 36 AND 60) AS count",
    '60+' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age > 60) +
                  (SELECT COUNT(*) FROM officials WHERE age > 60) AS count"
];

$population_data = [];

foreach ($age_groups as $label => $query) {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $population_data[$label] = (int)$row['count'];
}

$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Barangay Resident Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      color: #212529;
      font-size: 16px;
      line-height: 1.7;
    }
    .card {
      transition: all 0.3s ease;
      border: 1px solid #e0e0e0;
      border-radius: 12px;
      overflow: hidden;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
      border-color: #3a9d6a;
    }
    .card:focus {
      outline: 3px solid #f9c846;
      outline-offset: 2px;
    }
    .icon-wrapper {
      background: linear-gradient(135deg, #e8f5e8, #f0f9f0);
      border: 2px solid #3a9d6a;
      border-radius: 50%;
      width: 80px;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px auto;
    }
    .icon-wrapper i {
      font-size: 32px;
      color: #3a9d6a;
    }
    .logo-circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border: 3px solid #3a9d6a;
      overflow: hidden;
    }
    .logo-circle img {
      width: 80%;
      height: 80%;
      object-fit: contain;
    }
    .btn-primary {
      background-color: #3a9d6a;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: 600;
    }
    .btn-primary:hover {
      background-color: #2d7c4a;
    }
    .text-accent {
      color: #f9c846;
    }
    .bg-header {
      background: linear-gradient(to right, #1e40af, #1e3a8a);
    }
    .back-to-top {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #3a9d6a;
      color: white;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      transition: background 0.3s;
      z-index: 100;
    }
    .back-to-top:hover {
      background-color: #2d7c4a;
      cursor: pointer;
    }

    /* Slideshow Styles */
    .slideshow-container {
      position: relative;
      max-width: 100%;
      margin: 0 auto;
      overflow: hidden;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      height: 560px;
    }
    .slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 1s ease-in-out;
    }
    .slide.active {
      opacity: 1;
      z-index: 1;
    }
    .slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      border-radius: 12px;
    }
    .slideshow-dots {
      text-align: center;
      margin-top: 12px;
    }
    .dot {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin: 0 6px;
      background-color: #ccc;
      border-radius: 50%;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .dot.active {
      background-color: #3a9d6a;
    }
    .prev, .next {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 40px;
      height: 40px;
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      cursor: pointer;
      z-index: 10;
      border: 2px solid white;
    }
    .prev {
      left: 10px;
    }
    .next {
      right: 10px;
    }
    .prev:hover, .next:hover {
      background-color: #3a9d6a;
    }

    /* Dropdown Menu */
    .dropdown {
      position: relative;
      display: inline-block;
    }
    .menu-button {
      width: 50px;
      height: 50px;
      border: 2px solid #fff;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      background-color: transparent;
      transition: all 0.3s ease;
      font-size: 24px;
      color: white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .menu-button:hover {
      background-color: rgba(255,255,255,0.1);
      transform: scale(1.05);
    }
    .menu-button i {
      font-size: 20px;
      color: white;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      min-width: 180px;
      background-color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border-radius: 8px;
      z-index: 50;
      overflow: hidden;
      margin-top: 8px;
    }
    .dropdown-content a {
      color: #2d3748;
      padding: 12px 16px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      transition: background-color 0.2s;
    }
    .dropdown-content a:hover {
      background-color: #f7fafc;
    }
    .dropdown-content a i {
      width: 20px;
      color: #3a9d6a;
    }
    .dropdown.active .dropdown-content {
      display: block;
    }

    /* Marquee Style */
    .marquee {
      background-color: #1e40af;
      color: white;
      padding: 10px 0;
      white-space: nowrap;
      overflow: hidden;
      position: relative;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .marquee-content {
      display: inline-block;
      animation: marquee 15s linear infinite;
      font-weight: 600;
      font-size: 1.1rem;
    }
    @keyframes marquee {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
    }

    /* Responsive */
    @media (max-width: 640px) {
      .slideshow-container {
        height: 280px;
      }
      .slide img {
        height: 280px;
      }
      .prev, .next {
        width: 32px;
        height: 32px;
        font-size: 16px;
      }
      .menu-button {
        width: 40px;
        height: 40px;
      }
    }
  </style>
</head>
<body class="font-sans leading-relaxed selection:bg-yellow-200 selection:text-gray-800">

  <!-- Header -->
  <header class="text-white shadow-lg relative">
    <div class="absolute inset-0 bg-gradient-to-r from-green-500 via-yellow-400 to-yellow-400"></div>
    <div class="absolute inset-0 bg-black bg-opacity-30"></div>

    <div class="container mx-auto px-4 py-4 relative z-10">
      <div class="header-content flex items-center justify-between gap-2 text-white">
        <!-- Left: Date -->
        <div class="header-date text-sm opacity-90">
          <i class="far fa-calendar-alt mr-1"></i>
          <span id="current-date">Thursday, August 7, 2025</span>
        </div>

        <!-- Center: Logo -->
        <div class="header-logo logo-circle mx-4">
          <img src="../images/Bagbag.png" alt="Barangay Logo">
        </div>

        <!-- Right: Menu Button -->
        <div class="header-user dropdown" id="menuDropdown">
          <button class="menu-button" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
          </button>

          <!-- Dropdown Links -->
          <div class="dropdown-content">
            <a href="#">
              <i class="fas fa-home"></i> Home
            </a>
            <a href="#">
              <i class="fas fa-user-circle"></i> My Profile
            </a>
            <a href="../login/logout_admin_and_officials.php">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Marquee Navigation -->
  <div class="marquee">
    <div class="marquee-content">
      Welcome to our Brgy Banaba Services System &nbsp; • &nbsp; 
      Accessible, Trusted, and Efficient Services for All Residents &nbsp; • &nbsp; 
      We're here to help you every step of the way!
    </div>
  </div>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8 max-w-6xl">
    <section class="text-center mb-8 px-4">
      <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-4 leading-tight">
        Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
      </h1>
      <p class="text-gray-600 text-base md:text-lg max-w-2xl mx-auto mb-6">
        To our Barangay Resident Dashboard
      </p><br><br>

      <!-- Image Slideshow -->
      <div class="slideshow-container mx-auto">
        <div class="slide active">
          <img src="../images/4-bagbag.png" alt="Community Service">
        </div>
        <div class="slide">
          <img src="../images/BARANGAY-HOTLINE.png" alt="Health Program">
        </div>
        <div class="slide">
          <img src="../images/committess.png" alt="Education">
        </div>
        <div class="slide">
          <img src="../images/A.jpg" alt="Environment">
        </div>
        <div class="slide">
          <img src="../images/B.jpg" alt="Security">
        </div>
        <div class="slide">
          <img src="../images/2.jpg" alt="Barangay Event">
        </div>
        <div class="slide">
          <img src="../images/4.jpg" alt="Financial Assistance">
        </div>
        <div class="slide">
          <img src="../images/1.jpg" alt="Senior Citizens">
        </div>

        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
        <button class="next" onclick="moveSlide(1)">&#10095;</button>
      </div>

      <!-- Indicators -->
      <div class="slideshow-dots mt-4">
        <span class="dot active" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
        <span class="dot" onclick="currentSlide(4)"></span>
        <span class="dot" onclick="currentSlide(5)"></span>
        <span class="dot" onclick="currentSlide(6)"></span>
        <span class="dot" onclick="currentSlide(7)"></span>
        <span class="dot" onclick="currentSlide(8)"></span>
      </div><br>
    </section>

    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-4 leading-tight text-center">
      Please choose from the following services
    </h1>
    <p class="text-gray-600 text-base md:text-lg max-w-2xl mx-auto mb-6 text-center">
      We're here to help you access barangay services easily and safely.
    </p>

    <!-- Service Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-2 md:px-6">
      <!-- Official Committee -->
      <a href="#" class="card bg-white p-6 text-center shadow-md hover:shadow-xl focus:shadow-xl focus:outline-none">
        <div class="icon-wrapper">
          <i class="fas fa-users"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Official Committee</h3>
        <p class="text-gray-600 text-sm">Meet your barangay leaders and officials</p>
      </a>

      <!-- Emergency Contact -->
      <a href="#" class="card bg-white p-6 text-center shadow-md hover:shadow-xl focus:shadow-xl focus:outline-none">
        <div class="icon-wrapper">
          <i class="fas fa-phone-alt"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Emergency Contact</h3>
        <p class="text-gray-600 text-sm">Call help fast — police, fire, medical</p>
      </a>

      <!-- Program -->
      <a href="#" class="card bg-white p-6 text-center shadow-md hover:shadow-xl focus:shadow-xl focus:outline-none">
        <div class="icon-wrapper">
          <i class="fas fa-calendar-check"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Program</h3>
        <p class="text-gray-600 text-sm">Upcoming events and community programs</p>
      </a>

      <!-- Submit Request -->
      <a href="#" class="card bg-white p-6 text-center shadow-md hover:shadow-xl focus:shadow-xl focus:outline-none">
        <div class="icon-wrapper">
          <i class="fas fa-file-invoice"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Submit Request</h3>
        <p class="text-gray-600 text-sm">Send a request or concern to the office</p>
      </a>

      <!-- Blotter and Reports -->
      <a href="#" class="card bg-white p-6 text-center shadow-md hover:shadow-xl focus:shadow-xl focus:outline-none">
        <div class="icon-wrapper">
          <i class="fas fa-book"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Blotter & Reports</h3>
        <p class="text-gray-600 text-sm">View or file official records</p>
      </a>

      <!-- Community Reports -->
      <a href="#" class="card bg-white p-6 text-center shadow-md hover:shadow-xl focus:shadow-xl focus:outline-none">
        <div class="icon-wrapper">
          <i class="fas fa-comments"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Community Reports</h3>
        <p class="text-gray-600 text-sm">Share feedback or report issues</p>
      </a>

      <!-- My Profile -->
      <a href="#" class="card bg-white p-6 text-center shadow-md hover:shadow-xl focus:shadow-xl focus:outline-none">
        <div class="icon-wrapper">
          <i class="fas fa-user-circle"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">My Profile</h3>
        <p class="text-gray-600 text-sm">Update your personal information</p>
      </a>
    </div>
  </main>

  <!-- Back to Top Button -->
  <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="back-to-top hidden md:flex">
    <i class="fas fa-arrow-up"></i>
  </button>

  <!-- Footer -->
  <footer class="bg-header text-white text-center py-5 mt-12">
    <div class="container mx-auto px-4">
      <p class="text-sm opacity-90">
        © 2025 Barangay Management System | Designed with care for senior citizens
      </p>
      <p class="text-xs mt-1 opacity-75">
        Simple • Accessible • Trusted
      </p>
    </div>
  </footer>

  

  <!-- Scripts -->
  <script>
    // Update current date
    const now = new Date();
    document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });

    // Slideshow functionality
    let slideIndex = 1;
    showSlides(slideIndex);

    function moveSlide(n) {
      showSlides(slideIndex += n);
    }

    function currentSlide(n) {
      showSlides(slideIndex = n);
    }

    function showSlides(n) {
      const slides = document.querySelectorAll('.slide');
      const dots = document.querySelectorAll('.dot');

      if (n > slides.length) { slideIndex = 1; }
      if (n < 1) { slideIndex = slides.length; }

      slides.forEach(slide => {
        slide.classList.remove('active');
        slide.style.zIndex = 0;
      });

      dots.forEach(dot => dot.classList.remove('active'));

      const currentSlide = slides[slideIndex - 1];
      currentSlide.classList.add('active');
      currentSlide.style.zIndex = 1;

      dots[slideIndex - 1].classList.add('active');
    }

    // Auto-play slideshow
    setInterval(() => {
      moveSlide(1);
    }, 5000);

    // Dropdown Menu Toggle
    const dropdown = document.getElementById('menuDropdown');
    dropdown.addEventListener('click', function (e) {
      e.stopPropagation();
      this.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
      }
    });
  </script>
</body>
</html>