<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "ris");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get residents data
$sql = "SELECT * FROM registration ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bagbag eServices - Resident Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">Loading...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <!-- Main Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between">
      <h1 class="text-lg font-bold text-green-800">Resident Registrations</h1>
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

  <!-- Main Content -->
  <main class="flex-1 container mx-auto px-6 py-6">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-green-100">
        <h2 class="text-lg font-semibold text-green-900">List of Registered Residents</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700">
          <thead class="bg-green-800 text-white">
            <tr>
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Full Name</th>
              <th class="px-4 py-2">Gender</th>
              <th class="px-4 py-2">Birth Date</th>
              <th class="px-4 py-2">Birth Place</th>
              <th class="px-4 py-2">Civil Status</th>
              <th class="px-4 py-2">Nationality</th>
              <th class="px-4 py-2">Address</th>
              <th class="px-4 py-2">Phone</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Occupation</th>
              <th class="px-4 py-2">Company</th>
              <th class="px-4 py-2">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-2 font-mono"><?php echo htmlspecialchars($row['id']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['full_name']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['gender']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['birth_date']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['birth_place']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['civil_status']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['nationality']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['address']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['phone']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['occupation']); ?></td>
                  <td class="px-4 py-2"><?php echo htmlspecialchars($row['company_name']); ?></td>
                  <td class="px-4 py-2 text-xs text-gray-500"><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="13" class="px-4 py-6 text-center text-gray-500">No residents registered yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <!-- Scripts -->
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
