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

// Check if there's a pending clearance request
$stmt = $conn->prepare("SELECT id, status FROM barangay_clearance WHERE user_id = ? AND status = 'Pending' ORDER BY application_date DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$pending_request = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Barangay Clearance Application</title>

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
      <h1 class="text-xl font-bold text-green-800">Barangay Clearance Request</h1>

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

      <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Barangay Clearance Application</h2>
      <p class="text-gray-600 text-center mb-8 text-sm">Please fill out the form completely and truthfully.</p>

      <?php if ($pending_request): ?>
        <!-- ❌ Pending Request Alert -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm text-yellow-700">
                You already have a <strong>Pending</strong> clearance request. 
                Please wait for approval before submitting another one.
              </p>
            </div>
          </div>
        </div>

        <div class="text-center">
          <button disabled class="bg-gray-400 cursor-not-allowed text-white font-semibold px-8 py-3 rounded-lg shadow">
            <i class="fas fa-ban mr-2"></i> Request Already Submitted
          </button>
        </div>

        <div class="text-center mt-6">
  <a href="../R.submit_request.php" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200">
    <i class="fas fa-home mr-2"></i> Back to Home
  </a>
</div>
        

      <?php else: ?>
        <!-- ✅ Show Form if No Pending Request -->
        <form action="R.submit_clearance.php" method="POST">

          <!-- Personal Information -->
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="dob" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                <input type="number" name="age" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                <select name="civil_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select</option>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Widowed">Widowed</option>
                  <option value="Separated">Separated</option>
                  <option value="Divorced">Divorced</option>
                </select>
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                <input type="text" name="nationality" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>
          </section>

          <!-- Address -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Address</h3>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Years of Residency</label>
                <input type="number" name="residency_years" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>
          </section>

          <!-- Identification -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Identification</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Government ID Type</label>
                <select name="id_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                  <option value="">Select ID</option>
                  <option value="PhilSys">PhilSys</option>
                  <option value="Driver's License">Driver's License</option>
                  <option value="Passport">Passport</option>
                  <option value="SSS/GSIS">SSS/GSIS</option>
                  <option value="PRC">PRC</option>
                  <option value="Voter's ID">Voter's ID</option>
                  <option value="Others">Others</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID Number</label>
                <input type="text" name="id_number" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
              <input type="tel" name="contact_number" placeholder="09XXXXXXXXX" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
            </div>
          </section>

          <!-- Purpose -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Purpose of Request</h3>
            <div>
              <textarea name="purpose" rows="3" placeholder="e.g. Employment, School Requirement, Business Permit, Loan Application, Travel, etc." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required></textarea>
            </div>
          </section>

          <!-- Other Information -->
          <section class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">Additional Information</h3>
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
              <i class="fas fa-paper-plane mr-2"></i> Submit Request
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
      const today = new Date().toISOString().split("T")[0];
      const dateInput = document.querySelector('input[name="application_date"]');
      if (dateInput) dateInput.value = today;
    });

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