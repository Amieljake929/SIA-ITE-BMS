<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
$conn = new mysqli("localhost:3307", "root", "", "bms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Get the resident_id linked to the user_id
$stmt = $conn->prepare("SELECT r.id FROM residents r WHERE r.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$resident = $result->fetch_assoc();

if (!$resident) {
    die("Error: No associated resident profile found for this user.");
}

$resident_id = $resident['id'];
$stmt->close();

// Now check for pending blotter reports using the correct resident_id
$stmt = $conn->prepare("SELECT id, status FROM blotter_and_reports WHERE resident_id = ? AND status = 'Pending' ORDER BY report_date DESC LIMIT 1");
$stmt->bind_param("i", $resident_id);
$stmt->execute();
$result = $stmt->get_result();
$pending_report = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Blotter Report - Bagbag</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    input:focus, select:focus, textarea:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
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
      <h1 class="text-xl font-bold text-green-800">Blotter Report Submission</h1>

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
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow px-6 py-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">

      <h2 class="text-2xl font-bold text-green-800 mb-4 text-center">Blotter Report Form</h2>
      <p class="text-gray-600 text-center mb-8 text-sm">Please provide accurate information. All fields are required unless specified.</p>

      <?php if ($pending_report): ?>
        <!-- ❌ Alert if Pending Report Exists -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm text-yellow-700">
                You already have a <strong>Pending</strong> blotter report. 
                Please wait for processing before submitting another.
              </p>
            </div>
          </div>
        </div>

        <div class="text-center">
          <button disabled class="bg-gray-400 cursor-not-allowed text-white font-semibold px-8 py-3 rounded-lg shadow">
            <i class="fas fa-ban mr-2"></i> Report Already Pending
          </button>
        </div>

        <div class="text-center mt-6">
          <a href="../R.submit_request.php" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200">
            <i class="fas fa-home mr-2"></i> Back to Home
          </a>
        </div>

      <?php else: ?>
        <!-- ✅ Blotter Form -->
        <form action="R.submit_blotter_reports.php" method="POST">

          <!-- Basic Information -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Basic Information of Complainant</h3>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                <input type="number" name="age" min="1" max="120" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number / Email</label>
                <input type="text" name="contact" placeholder="09XXXXXXXXX or email@example.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Complete Address</label>
              <textarea name="address" rows="2" placeholder="House No., Street, Purok/Sitio, Barangay Bagbag" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required></textarea>
            </div>
          </section>

          <!-- Incident Details -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Incident Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type of Incident</label>
                <select name="incident_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select Type</option>
                  <option value="Theft">Pagnanakaw</option>
                  <option value="Fight">Awayan</option>
                  <option value="Threat">Pananakot</option>
                  <option value="Missing Item">Nawawalang Gamit</option>
                  <option value="Accident">Aksidente</option>
                  <option value="Harassment">Pag-uusig</option>
                  <option value="Others">Others</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Incident</label>
                <input type="date" name="incident_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time of Incident</label>
                <input type="time" name="incident_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Place of Incident</label>
                <input type="text" name="incident_location" placeholder="Complete address where incident occurred" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Narrative of the Incident</label>
              <textarea name="narrative" rows="4" placeholder="Describe what happened in detail. Minimum of 100 characters." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" minlength="100" required></textarea>
              <p class="text-xs text-gray-500 mt-1">Please provide a detailed account to assist in investigation.</p>
            </div>
          </section>

          <!-- Involved Parties -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Involved Parties (if known)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="involved_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="involved_address" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Relation to Complainant</label>
                <select name="relation" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
                  <option value="">Select</option>
                  <option value="Family">Kapamilya</option>
                  <option value="Neighbor">Kapitbahay</option>
                  <option value="Friend">Kaibigan</option>
                  <option value="Unknown">Hindi Kilala</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description (Gender, Age, Clothing, etc.)</label>
              <textarea name="description" rows="2" placeholder="e.g. Lalaki, ~30 yrs old, suot pulang t-shirt at itim na pants" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition"></textarea>
            </div>
          </section>

          <!-- Witnesses -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Witnesses (if any)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Witness Name</label>
                <input type="text" name="witness_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                <input type="text" name="witness_contact" placeholder="09XXXXXXXXX" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Residence</label>
                <input type="text" name="witness_address" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
              </div>
            </div>
          </section>

          <!-- Purpose of Blotter -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Purpose of Reporting</h3>
            <div class="space-y-2">
              <label class="inline-flex items-center">
                <input type="checkbox" name="purpose[]" value="Police Report" class="rounded text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Para sa Police Report</span>
              </label>
              <label class="inline-flex items-center">
                <input type="checkbox" name="purpose[]" value="Insurance Claim" class="rounded text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Para sa Insurance Claim</span>
              </label>
              <label class="inline-flex items-center">
                <input type="checkbox" name="purpose[]" value="Complaint" class="rounded text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Para sa Reklamo</span>
              </label>
              <label class="inline-flex items-center">
                <input type="checkbox" name="purpose[]" value="For Record Only" class="rounded text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Para lang Maitala</span>
              </label>
            </div>
          </section>

          <!-- Signature -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Confirmation</h3>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Electronic Signature</label>
              <input type="text" name="signature" placeholder="Type your full name to sign" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              <p class="text-xs text-gray-500 mt-1">By typing your name, you confirm that the information provided is true and correct.</p>
            </div>
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Date of Report</label>
              <input type="date" name="report_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
            </div>
          </section>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
              <i class="fas fa-file-alt mr-2"></i> Submit Blotter Report
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

    // Auto-set today's date for report_date
    document.addEventListener("DOMContentLoaded", function () {
      const today = new Date().toISOString().split("T")[0];
      const dateInputs = document.querySelectorAll('input[name="report_date"], input[name="incident_date"]');
      dateInputs.forEach(input => {
        if (input) input.value = today;
      });
    });

    // Toggle User Dropdown
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