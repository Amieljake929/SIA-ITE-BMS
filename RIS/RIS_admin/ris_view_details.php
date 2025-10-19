<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../RIS_login/db_connect.php';

// Get resident ID from URL
$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: ris_resident_registration.php");
    exit();
}

// Fetch resident data
$stmt = $conn->prepare("SELECT * FROM registration WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: ris_resident_registration.php");
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
  <title>Bagbag eServices - Resident Details</title>
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
          onclick="window.location.href='ris_resident_registration.php'"
          title="Back"
        >
          <i class="fas fa-arrow-left" style="font-size: 1.2rem;"></i>
        </button>
        <h1 class="text-xl font-bold text-green-800">Resident Details</h1>
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
    <?php if (isset($_SESSION['message'])): ?>
      <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded shadow-sm text-sm" id="message">
        <?= htmlspecialchars($_SESSION['message']); ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-green-100">
        <h2 class="text-lg font-semibold text-green-900">Resident Information</h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-md font-semibold text-gray-800 mb-4">Personal Details</h3>
            <div class="space-y-2">
              <p><strong>ID:</strong> <?= htmlspecialchars($row['id']) ?></p>
              <p><strong>Status:</strong>
                <span class="px-2 py-1 rounded-full text-xs font-medium
                  <?= $row['status'] === 'approved' ? 'bg-green-100 text-green-800' :
                      ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                  <?= ucfirst(htmlspecialchars($row['status'])) ?>
                </span>
              </p>
              <p><strong>Full Name:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']) ?></p>
              <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
              <p><strong>Birth Date:</strong> <?= htmlspecialchars($row['dob']) ?></p>
              <p><strong>Birth Place:</strong> <?= htmlspecialchars($row['pob']) ?></p>
              <p><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?></p>
              <p><strong>Civil Status:</strong> <?= htmlspecialchars($row['civil_status']) ?></p>
              <p><strong>Nationality:</strong> <?= htmlspecialchars($row['nationality']) ?></p>
              <p><strong>Religion:</strong> <?= htmlspecialchars($row['religion'] ?? 'N/A') ?></p>
            </div>
          </div>
          <div>
            <h3 class="text-md font-semibold text-gray-800 mb-4">Contact & Address</h3>
            <div class="space-y-2">
              <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
              <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
              <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
              <p><strong>Resident Type:</strong> <?= htmlspecialchars($row['resident_type']) ?></p>
              <p><strong>Stay Length:</strong> <?= htmlspecialchars($row['stay_length'] ?? 'N/A') ?> years</p>
              <p><strong>Employment Status:</strong> <?= htmlspecialchars($row['employment_status']) ?></p>
              <p><strong>Valid ID Type:</strong> <?= htmlspecialchars($row['valid_id_type'] ?? 'N/A') ?></p>
              <p><strong>Valid ID Number:</strong> <?= htmlspecialchars($row['valid_id_number'] ?? 'N/A') ?></p>
            </div>
          </div>
        </div>
        <div class="mt-6">
          <h3 class="text-md font-semibold text-gray-800 mb-4">Additional Information</h3>
          <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center">
              <p class="text-sm font-medium">Senior Citizen</p>
              <i class="fas fa-<?= $row['is_senior_citizen'] ? 'check text-green-600' : 'times text-gray-400' ?> text-lg"></i>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium">PWD</p>
              <i class="fas fa-<?= $row['is_pwd'] ? 'check text-green-600' : 'times text-gray-400' ?> text-lg"></i>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium">Solo Parent</p>
              <i class="fas fa-<?= $row['is_solo_parent'] ? 'check text-green-600' : 'times text-gray-400' ?> text-lg"></i>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium">Voter</p>
              <i class="fas fa-<?= $row['is_voter'] ? 'check text-green-600' : 'times text-gray-400' ?> text-lg"></i>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium">Student</p>
              <i class="fas fa-<?= $row['is_student'] ? 'check text-green-600' : 'times text-gray-400' ?> text-lg"></i>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium">Indigenous</p>
              <i class="fas fa-<?= $row['is_indigenous'] ? 'check text-green-600' : 'times text-gray-400' ?> text-lg"></i>
            </div>
          </div>
        </div>
        <div class="mt-6">
          <h3 class="text-md font-semibold text-gray-800 mb-4">Uploaded Documents</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <p class="text-sm font-medium mb-2">Valid ID Image</p>
              <?php if ($row['valid_id_image']): ?>
                <img src="../registrations/<?= htmlspecialchars($row['valid_id_image']) ?>" alt="Valid ID" class="max-w-full h-auto border rounded shadow">
              <?php else: ?>
                <p class="text-gray-500">No image uploaded</p>
              <?php endif; ?>
            </div>
            <div>
              <p class="text-sm font-medium mb-2">Selfie with ID</p>
              <?php if ($row['selfie_with_id']): ?>
                <img src="../registrations/<?= htmlspecialchars($row['selfie_with_id']) ?>" alt="Selfie with ID" class="max-w-full h-auto border rounded shadow">
              <?php else: ?>
                <p class="text-gray-500">No image uploaded</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php if ($row['status'] === 'pending'): ?>
          <div class="mt-6 border-t pt-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4">Actions</h3>
            <div class="flex gap-4">
              <a href="ris_approve_reject.php?action=approve&id=<?= urlencode($row['id']) ?>"
                 onclick="return confirm('Approve this resident? This will generate a Resident ID.');"
                 class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition">
                <i class="fas fa-check mr-2"></i> Approve
              </a>
              <a href="ris_approve_reject.php?action=reject&id=<?= urlencode($row['id']) ?>"
                 onclick="return confirm('Reject this resident? This cannot be undone.');"
                 class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition">
                <i class="fas fa-times mr-2"></i> Reject
              </a>
            </div>
          </div>
        <?php endif; ?>
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
