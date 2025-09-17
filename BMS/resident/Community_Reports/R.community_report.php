<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';


$user_id = $_SESSION['user_id'];

// Check if there's ANY active report (Pending, Assigned, or For Closing)
$stmt = $conn->prepare("SELECT community_report_id, status FROM community_reports 
                        WHERE user_id = ? 
                        AND status IN ('Pending', 'Assigned', 'For Closing') 
                        ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$active_report = $result->fetch_assoc(); // Magbabalik kung meron
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Community Report Form</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    input:focus, select:focus, textarea:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    }
    textarea {
      resize: vertical;
      min-height: 100px;
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
      <h1 class="text-xl font-bold text-green-800">Community Report Form</h1>

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
              <a href="#" class="block px-5 py-2 text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-user text-green-600 mr-3"></i> Profile
              </a>
            </li>
            <li>
              <a href="../../login/logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
            <li>
              <a href="R.report_history.php" class="block p-4 bg-white shadow rounded-lg hover:shadow-md transition">
              <i class="fas fa-history text-green-600 mr-2"></i> View Report History
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow px-6 py-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">

      <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Community Report</h2>
      <p class="text-gray-600 text-center mb-8 text-sm">
        Please provide accurate and detailed information about the incident. All reports are confidential.
      </p>

      <?php if ($active_report): ?>
        <!-- ❌ Active Report Alert (Pending, Assigned, or For Closing) -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm text-yellow-700">
                You already have a report with status: 
                <strong><?= htmlspecialchars($active_report['status']) ?></strong>. 
                Please wait until it is resolved before submitting a new one.
              </p>
            </div>
          </div>
        </div>

        <div class="text-center">
          <button disabled class="bg-gray-400 cursor-not-allowed text-white font-semibold px-8 py-3 rounded-lg shadow">
            <i class="fas fa-ban mr-2"></i> Report Already Active
          </button>
        </div>

        <div class="text-center mt-6">
          <a href="../resident_dashboard.php" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200">
            <i class="fas fa-home mr-2"></i> Back to Home
          </a>
        </div>

      <?php else: ?>
        <!-- ✅ Community Report Form (No active report) -->
        <form id="reportForm" action="R.submit_community_report.php" method="POST" enctype="multipart/form-data">

          <!-- Complainant Full Name -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Complainant Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                <input type="text" name="middle_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">House No.</label>
                <input type="text" name="house_no" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                <input type="text" name="street" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Purok/Sitio</label>
                <input type="text" name="purok" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                <input type="text" name="barangay" value="Bagbag" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-600">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City/Municipality</label>
                <input type="text" name="city" value="Quezon City" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-600">
              </div>
            </div>
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
              <input type="text" name="province" value="Metro Manila" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-600">
            </div>
          </section>

          <!-- Contact Info -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                <input type="tel" name="contact_number" placeholder="09XXXXXXXXX" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address (Optional)</label>
                <input type="email" name="email" placeholder="you@example.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
            </div>
          </section>

          <!-- Incident Details -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Incident Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Incident Type</label>
                <select name="incident_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select Type</option>
                  <option value="Threat">Threat</option>
                  <option value="Theft">Theft</option>
                  <option value="Noise Complaint">Noise Complaint</option>
                  <option value="Neighbor Dispute">Neighbor Dispute</option>
                  <option value="Domestic Violence">Domestic Violence</option>
                  <option value="Vandalism">Vandalism</option>
                  <option value="Suspicious Activity">Suspicious Activity</option>
                  <option value="Harassment">Harassment</option>
                  <option value="Disaster">Disaster / Calamity</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Incident</label>
                <input type="date" name="incident_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time of Incident (Optional)</label>
                <input type="time" name="incident_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location of Incident</label>
                <input type="text" name="incident_location" placeholder="House #, Street, or Landmark" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Detailed Report</label>
              <textarea name="incident_details" rows="4" placeholder="Describe what happened, who was involved, and how it started..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required></textarea>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Accused (Names & Addresses or Description)</label>
              <textarea name="accused_names_residences" rows="3" placeholder="If known, include full names and addresses. If not, describe appearance and actions." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition"></textarea>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Requested Action</label>
              <textarea name="requested_action" rows="3" placeholder="What do you want to happen? (e.g., investigation, mediation)" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Upload Evidence (Optional)</label>
              <input 
                type="file" 
                name="evidence" 
                id="evidence" 
                accept="image/*,video/mp4,.pdf,.doc,.docx" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition"
                onchange="validateFileSize(this)">
              <p class="text-xs text-gray-500 mt-1">
                Supports images, videos (MP4), PDFs, and documents. 
                <strong>Max: 10MB for images, 40MB for video.</strong>
              </p>
              <p id="fileError" class="text-xs text-red-600 mt-1 hidden">File is too large.</p>
            </div>
          </section>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
              <i class="fas fa-exclamation-triangle mr-2"></i> Submit Community Report
            </button>
          </div>
        </form>
      <?php endif; ?>

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

    document.addEventListener("DOMContentLoaded", function () {
      // Confirm on submit
      const form = document.getElementById('reportForm');
      form.addEventListener('submit', function(e) {
        if (!confirm("Are you sure you want to submit this community report? It will be reviewed by the barangay officials.")) {
          e.preventDefault();
        }
      });
    });

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

    // File Size Validation
    function validateFileSize(input) {
      const file = input.files[0];
      const errorElement = document.getElementById('fileError');
      
      if (!file) {
        errorElement.classList.add('hidden');
        return;
      }

      const isImage = file.type.startsWith('image/');
      const isVideo = file.type.startsWith('video/');
      
      if (isImage && file.size > 10 * 1024 * 1024) { // 10MB
        errorElement.textContent = "Image is too large. Maximum size is 10MB.";
        errorElement.classList.remove('hidden');
        input.value = ""; // Clear file input
        return false;
      }
      
      if (isVideo && file.size > 40 * 1024 * 1024) { // 40MB
        errorElement.textContent = "Video is too large. Maximum size is 40MB.";
        errorElement.classList.remove('hidden');
        input.value = ""; // Clear file input
        return false;
      }

      errorElement.classList.add('hidden');
      return true;
    }

    // Validate before form submit
    document.getElementById('reportForm').addEventListener('submit', function(e) {
      const fileInput = document.getElementById('evidence');
      if (fileInput.files.length > 0) {
        if (!validateFileSize(fileInput)) {
          e.preventDefault();
          alert("Please fix the file size issue before submitting.");
        }
      }
    });
  </script>

  <!-- Success Alert -->
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') && urlParams.get('success') === 'community_report') {
      alert("✅ Success! Your community report has been submitted.\n\nStatus: Pending Review\nBarangay officials will contact you if needed.");
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  </script>
</body>
</html>