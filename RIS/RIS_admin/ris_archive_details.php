<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if user has archive access
if (!isset($_SESSION['archive_access']) || $_SESSION['archive_access'] !== true) {
    header("Location: ris_archive_password.php");
    exit();
}

// Database connection
include '../RIS_login/db_connect.php';

// Get archived registration ID from URL
$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: ris_registration_archive.php");
    exit();
}

// Fetch archived registration data
$stmt = $conn->prepare("SELECT * FROM registration_archive WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: ris_registration_archive.php");
    exit();
}
$row = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bagbag eServices - Archived Registration Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">Loading...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
      <div class="flex items-center space-x-4">
        <button
          class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
          onclick="window.location.href='ris_admin_dashboard.php'"
          title="Home"
        >
          <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
        </button>
        <button
          class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-500 text-white hover:bg-gray-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
          onclick="window.location.href='ris_registration_archive.php'"
          title="Back to Archive"
        >
          <i class="fas fa-arrow-left text-white" style="font-size: 1.2rem;"></i>
        </button>
        <h1 class="text-xl font-bold text-green-800">Archived Registration Details</h1>
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
              <a href="../RIS_login/ris_logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <main class="flex-1 container mx-auto px-6 py-6">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-orange-100">
        <h2 class="text-lg font-semibold text-orange-900">Registration Details (Archived)</h2>
        <p class="text-sm text-orange-700 mt-1">ID: <?= htmlspecialchars($row['id']) ?> | Rejected on: <?= htmlspecialchars($row['rejected_at']) ?></p>
      </div>

      <div class="p-6">
        <!-- Rejection Remarks -->
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <h3 class="text-md font-semibold text-red-800 mb-2">Rejection Remarks</h3>
          <p class="text-red-700 whitespace-pre-line"><?= htmlspecialchars($row['remarks']) ?></p>
        </div>

        <!-- Personal Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['email']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['phone']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['dob']) ?> (Age: <?= htmlspecialchars($row['age']) ?>)</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Place of Birth</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['pob']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Gender</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['gender']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Civil Status</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['civil_status']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nationality</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['nationality']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Religion</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['religion'] ?? 'Not specified') ?></p>
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Address & Residency</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['address']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Resident Type</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['resident_type']) ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Length of Stay</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['stay_length'] ?? 'Not specified') ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Employment Status</label>
                <p class="text-gray-900"><?= htmlspecialchars($row['employment_status']) ?></p>
              </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4 mt-6">Special Categories</h3>
            <div class="space-y-2">
              <div class="flex items-center">
                <input type="checkbox" class="mr-2" <?= $row['is_senior_citizen'] ? 'checked' : '' ?> disabled>
                <label class="text-sm text-gray-700">Senior Citizen</label>
              </div>
              <div class="flex items-center">
                <input type="checkbox" class="mr-2" <?= $row['is_pwd'] ? 'checked' : '' ?> disabled>
                <label class="text-sm text-gray-700">Person with Disability</label>
              </div>
              <div class="flex items-center">
                <input type="checkbox" class="mr-2" <?= $row['is_solo_parent'] ? 'checked' : '' ?> disabled>
                <label class="text-sm text-gray-700">Solo Parent</label>
              </div>
              <div class="flex items-center">
                <input type="checkbox" class="mr-2" <?= $row['is_voter'] ? 'checked' : '' ?> disabled>
                <label class="text-sm text-gray-700">Registered Voter</label>
              </div>
              <div class="flex items-center">
                <input type="checkbox" class="mr-2" <?= $row['is_student'] ? 'checked' : '' ?> disabled>
                <label class="text-sm text-gray-700">Student</label>
              </div>
              <div class="flex items-center">
                <input type="checkbox" class="mr-2" <?= $row['is_indigenous'] ? 'checked' : '' ?> disabled>
                <label class="text-sm text-gray-700">Indigenous Person</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Documents -->
        <div class="mb-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Documents</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Valid ID</label>
              <?php if ($row['valid_id_type']): ?>
                <p class="text-sm text-gray-600 mb-2">Type: <?= htmlspecialchars($row['valid_id_type']) ?> | Number: <?= htmlspecialchars($row['valid_id_number']) ?></p>
              <?php endif; ?>
              <?php if ($row['valid_id_image']): ?>
                <img src="../registrations/<?= htmlspecialchars($row['valid_id_image']) ?>" alt="Valid ID" class="max-w-full h-auto border rounded shadow">
              <?php else: ?>
                <p class="text-gray-500">No image uploaded</p>
              <?php endif; ?>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Selfie with ID</label>
              <?php if ($row['selfie_with_id']): ?>
                <img src="../registrations/<?= htmlspecialchars($row['selfie_with_id']) ?>" alt="Selfie with ID" class="max-w-full h-auto border rounded shadow">
              <?php else: ?>
                <p class="text-gray-500">No image uploaded</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Archive Info -->
        <div class="border-t pt-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-md font-semibold text-gray-800 mb-2">Archive Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-700">Original Registration Date:</span>
                <span class="text-gray-900 ml-2"><?= htmlspecialchars($row['created_at']) ?></span>
              </div>
              <div>
                <span class="font-medium text-gray-700">Archived Date:</span>
                <span class="text-gray-900 ml-2"><?= htmlspecialchars($row['rejected_at']) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <script>
    function updateTime() {
      const now = new Date();
      const options = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' };
      document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();

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
<?php $conn->close(); ?>
