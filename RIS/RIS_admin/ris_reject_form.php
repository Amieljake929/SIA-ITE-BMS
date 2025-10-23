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
$stmt = $conn->prepare("SELECT * FROM registration WHERE id = ? AND status = 'pending'");
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
  <title>Bagbag eServices - Reject Resident Registration</title>
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
          onclick="window.location.href='ris_view_details.php?id=<?= urlencode($row['id']) ?>'"
          title="Back"
        >
          <i class="fas fa-arrow-left" style="font-size: 1.2rem;"></i>
        </button>
        <h1 class="text-xl font-bold text-green-800">Reject Resident Registration</h1>
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
      <div class="px-6 py-4 border-b border-gray-200 bg-red-100">
        <h2 class="text-lg font-semibold text-red-900">Reject Registration for: <?= htmlspecialchars($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']) ?></h2>
      </div>
      <div class="p-6">
        <form action="ris_approve_reject.php" method="POST">
          <input type="hidden" name="action" value="reject">
          <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">

          <div class="mb-4">
            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
              Rejection Remarks <span class="text-red-500">*</span>
            </label>
            <textarea
              id="remarks"
              name="remarks"
              rows="6"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 resize-vertical"
              placeholder="Please provide detailed reasons for rejection and instructions for resubmission..."
              required
            ></textarea>
            <p class="mt-1 text-sm text-gray-500">
              Explain what needs to be corrected or improved for the resident to resubmit their registration.
            </p>
          </div>

          <div class="flex gap-4">
            <button
              type="submit"
              onclick="return confirm('Are you sure you want to reject this registration? This will send an email to the resident and archive the application.');"
              class="inline-flex items-center px-6 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition"
            >
              <i class="fas fa-times mr-2"></i> Reject & Send Email
            </button>
            <a
              href="ris_view_details.php?id=<?= urlencode($row['id']) ?>"
              class="inline-flex items-center px-6 py-2 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700 transition"
            >
              <i class="fas fa-arrow-left mr-2"></i> Cancel
            </a>
          </div>
        </form>
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
