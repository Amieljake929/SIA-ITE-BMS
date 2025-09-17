<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';

$user_id = $_SESSION['user_id'];

// Check for any active request (Pending, Validated, or Approved)
$stmt = $conn->prepare("SELECT id, status, residency_id FROM certificate_of_residency WHERE user_id = ? AND status IN ('Pending', 'Validated', 'Approved') ORDER BY application_date DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$active_request = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Certificate of Residency Application</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

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
        <!-- Home Icon Button and Title -->
        <div class="flex items-center space-x-4">
            <!-- Home Icon Button -->
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../resident_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Certificate Of Residency Application</h1>
        </div>
    
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
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">

      <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Certificate of Residency</h2>
      <p class="text-gray-600 text-center mb-8 text-sm">Please provide accurate information for your residency certification.</p>

      <!-- ✅ If Approved -->
      <?php if ($active_request && $active_request['status'] === 'Approved'): ?>
        <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-8 rounded-lg">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-green-800">Certificate Approved!</h3>
              <p class="text-green-700 mt-1">Your Certificate of Residency has been <strong>approved</strong>.</p>
              <p class="text-sm text-green-600 mt-2">
                <strong>📄 Residency ID:</strong> <?= htmlspecialchars($active_request['residency_id']) ?><br>
                <strong>📝 Softcopy:</strong> You can download and print it below.<br>
                <strong>📬 Hardcopy:</strong> Ready to pick up at the Barangay Office.
              </p>
            </div>
          </div>
        </div>

        <div class="text-center space-y-4">
          <a href="print_document.php?tab=certificate_of_residency&id=<?= $active_request['id'] ?>" target="_blank"
             class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition transform hover:scale-105">
            <i class="fas fa-print mr-2"></i> Print Certificate
          </a>
          <div>
            <a href="../R.submit_request.php" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition">
              <i class="fas fa-home mr-2"></i> Back to Home
            </a>
          </div>
        </div>

      <!-- ⚠️ If Pending or Validated -->
      <?php elseif ($active_request && in_array($active_request['status'], ['Pending', 'Validated'])): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-lg">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-yellow-800">Request <?= htmlspecialchars($active_request['status']) ?></h3>
              <p class="text-yellow-700 mt-1">
                Your request is currently <strong><?= htmlspecialchars($active_request['status']) ?></strong>.
              </p>
              <p class="text-sm text-yellow-600 mt-2">
                Please wait for final approval. You cannot submit a new request until this is resolved.
              </p>
            </div>
          </div>
        </div>

        <div class="text-center">
          <button disabled class="bg-gray-400 cursor-not-allowed text-white font-semibold px-8 py-3 rounded-lg shadow opacity-70">
            <i class="fas fa-ban mr-2"></i> Request Already <?= htmlspecialchars($active_request['status']) ?>
          </button>
        </div>

        <div class="text-center mt-6">
          <a href="../R.submit_request.php" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200">
            <i class="fas fa-home mr-2"></i> Back to Home
          </a>
        </div>

      <!-- ✅ No Active Request - Show Form -->
      <?php else: ?>
        <!-- ✅ Show Form if No Pending/Validated/Approved Request -->
        <form id="residencyForm" action="R.submit_residency.php" method="POST">

          <!-- Full Name -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Personal Information</h3>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="dob" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Place of Birth</label>
                <input type="text" name="birth_place" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                <select name="civil_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select Status</option>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Widowed">Widowed</option>
                  <option value="Separated">Separated</option>
                </select>
              </div>
            </div>
            <div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
  <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
  <p id="emailError" class="text-red-500 text-xs mt-1 hidden">Please enter a valid email address.</p>
</div>
          </section>

          <!-- Address -->
          <section class="mb-8">
            <h3 class="text-lg font-semblold text-gray-800 mb-4 border-b pb-2 border-green-200">Residential Address</h3>
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

          <!-- Purpose & Contact -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Request Details</h3>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Purpose of Request</label>
              <textarea name="purpose" rows="3" placeholder="e.g. Employment, Scholarship, ID Application, Loan, etc." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
              <input type="tel" name="contact_number" placeholder="09XXXXXXXXX" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
            </div>
          </section>

          <!-- Signature & Date -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Confirmation</h3>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Applicant's Signature</label>
              <input type="text" name="signature" placeholder="Type your full name to sign" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              <p class="text-xs text-gray-500 mt-1">By typing your name, you agree that this serves as your digital signature.</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date of Application</label>
              <input type="date" name="application_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
            </div>
          </section>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
              <i class="fas fa-file-alt mr-2"></i> Submit Residency Request
            </button>
          </div>
        </form>
      <?php endif; ?>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <!-- JavaScript -->
  <script>
    // Update time function
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

    // DOM Content Loaded
    document.addEventListener("DOMContentLoaded", function () {
      // Set today's date
      const today = new Date().toISOString().split("T")[0];
      const dateInput = document.querySelector('input[name="application_date"]');
      if (dateInput) dateInput.value = today;

      // Dropdown Toggle - Fixed version
      const userMenuButton = document.getElementById('userMenuButton');
      const userDropdown = document.getElementById('userDropdown');
      
      if (userMenuButton && userDropdown) {
        // Toggle dropdown when button is clicked
        userMenuButton.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          userDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
          if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.add('hidden');
          }
        });

        // Close dropdown when pressing Escape key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') {
            userDropdown.classList.add('hidden');
          }
        });
      }

      // Form validation
      const form = document.getElementById('residencyForm');
      if (form) {
        const firstName = document.querySelector('input[name="first_name"]');
        const middleName = document.querySelector('input[name="middle_name"]');
        const lastName = document.querySelector('input[name="last_name"]');
        const signature = document.querySelector('input[name="signature"]');

        form.addEventListener('submit', function(e) {
          let fullName = `${firstName.value.trim()} ${middleName.value.trim()} ${lastName.value.trim()}`.replace(/\s+/g, ' ').trim();
          let sigValue = signature.value.trim();

          if (sigValue.toLowerCase() !== fullName.toLowerCase()) {
            e.preventDefault();
            alert("❌ Signature does not match your full name.\n\nExpected: " + fullName + "\nYou typed: " + sigValue);
            signature.focus();
            return false;
          }

          if (!confirm("Are you sure you want to submit your Certificate of Residency request?")) {
            e.preventDefault();
          }
        });
      }
    });

    // Success Alert
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') && urlParams.get('success') === 'residency') {
      alert("✅ Success! Your Certificate of Residency request has been submitted.\n\nStatus: Pending Approval\nYou will be notified once it's ready.");
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  </script>
</body>
</html>