<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../RIS_login/db_connect.php';

// Handle password verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    // Get admin password from database
    $stmt = $conn->prepare("SELECT password FROM ris_users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password correct, set session flag and redirect
            $_SESSION['archive_access'] = true;
            header("Location: ris_registration_archive.php");
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "Access denied.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bagbag eServices - Archive Access</title>
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
        <h1 class="text-xl font-bold text-green-800">Archive Access Verification</h1>
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

  <main class="flex-1 container mx-auto px-6 py-6 flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
      <div class="text-center mb-6">
        <div class="text-red-600 mb-4">
          <i class="fas fa-lock text-6xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Archive Access Required</h2>
        <p class="text-gray-600">Please enter your password to access the registration archive.</p>
      </div>

      <?php if (isset($error)): ?>
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            Password
          </label>
          <input
            type="password"
            id="password"
            name="password"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
            placeholder="Enter your password"
          >
        </div>

        <div class="flex space-x-3">
          <button
            type="submit"
            class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200"
          >
            <i class="fas fa-unlock mr-2"></i> Access Archive
          </button>
          <button
            type="button"
            onclick="window.location.href='ris_admin_dashboard.php'"
            class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200"
          >
            <i class="fas fa-times mr-2"></i> Cancel
          </button>
        </div>
      </form>

      <div class="mt-6 text-center text-sm text-gray-500">
        <p>This area contains sensitive resident information.</p>
        <p>Access is restricted to authorized administrators only.</p>
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
