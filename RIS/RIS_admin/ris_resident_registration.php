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
              <a href="../RIS_login/ris_logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
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

    <!-- Show message if exists -->
<?php if (isset($_SESSION['message'])): ?>
  <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded shadow-sm text-sm" id="message">
    <?= htmlspecialchars($_SESSION['message']); ?>
  </div>
  <?php unset($_SESSION['message']); ?>
<?php endif; ?>

    <!-- SEARCH & FILTER BAR -->
<div class="flex flex-col sm:flex-row gap-4 mb-6">
  <!-- Search Input -->
  <div class="flex-1">
    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
    <input type="text" id="searchInput" placeholder="Search by name, email, phone, address..." 
           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
  </div>

  <!-- Status Filter -->
  <div>
    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
    <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
      <option value="all">All</option>
      <option value="pending">Pending</option>
      <option value="approved">Approved</option>
      <option value="rejected">Rejected</option>
    </select>
  </div>
</div>

<!-- TABLE -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
  <div class="px-6 py-4 border-b border-gray-200 bg-green-100">
    <h2 class="text-lg font-semibold text-green-900">List of Registered Residents</h2>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left text-gray-700" id="residentTable">
      <thead class="bg-green-800 text-white">
        <tr>
          <th class="px-4 py-2">ID</th>
          <th class="px-4 py-2">Status</th>
          <th class="px-4 py-2">Full Name</th>
          <th class="px-4 py-2">Gender</th>
          <th class="px-4 py-2">Birth Date</th>
          <th class="px-4 py-2">Birth Place</th>
          <th class="px-4 py-2">Civil Status</th>
          <th class="px-4 py-2">Nationality</th>
          <th class="px-4 py-2">Address</th>
          <th class="px-4 py-2">Phone</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Employment</th>
          
          <!-- New: Demographic Indicators -->
          <th class="px-4 py-2">Senior</th>
          <th class="px-4 py-2">PWD</th>
          <th class="px-4 py-2">Solo Parent</th>
          <th class="px-4 py-2">Voter</th>
          <th class="px-4 py-2">Student</th>
          <th class="px-4 py-2">Indigenous</th>
          
          <th class="px-4 py-2">Created</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200" id="tableBody">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50" data-name="<?= htmlspecialchars(strtolower($row['full_name'])) ?>"
                data-email="<?= htmlspecialchars(strtolower($row['email'])) ?>"
                data-phone="<?= htmlspecialchars(strtolower($row['phone'])) ?>"
                data-address="<?= htmlspecialchars(strtolower($row['address'])) ?>"
                data-status="<?= htmlspecialchars($row['status']) ?>">
              
              <td class="px-4 py-2 font-mono text-xs"><?= htmlspecialchars($row['id']) ?></td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 rounded-full text-xs font-medium
                  <?= $row['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                      ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                  <?= ucfirst(htmlspecialchars($row['status'])) ?>
                </span>
              </td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['full_name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['gender']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['dob']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['pob']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['civil_status']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nationality']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['address']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['phone']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['employment_status']) ?></td>

              <!-- Demographic Indicators (Icons or ✅) -->
              <td class="px-4 py-2 text-center"><?= $row['is_senior_citizen'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>' ?></td>
              <td class="px-4 py-2 text-center"><?= $row['is_pwd'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>' ?></td>
              <td class="px-4 py-2 text-center"><?= $row['is_solo_parent'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>' ?></td>
              <td class="px-4 py-2 text-center"><?= $row['is_voter'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>' ?></td>
              <td class="px-4 py-2 text-center"><?= $row['is_student'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>' ?></td>
              <td class="px-4 py-2 text-center"><?= $row['is_indigenous'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>' ?></td>

              <td class="px-4 py-2 text-xs text-gray-500"><?= htmlspecialchars($row['created_at']) ?></td>
              <td class="px-4 py-2">
                <?php if ($row['status'] === 'pending'): ?>
                  <div class="flex gap-2">
                    <a href="ris_approve_reject.php?action=approve&id=<?= urlencode($row['id']) ?>"
                       onclick="return confirm('Approve this resident? This will generate a Resident ID.');"
                       class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition">
                      <i class="fas fa-check mr-1"></i> Approve
                    </a>
                    <a href="ris_approve_reject.php?action=reject&id=<?= urlencode($row['id']) ?>"
                       onclick="return confirm('Reject this resident? This cannot be undone.');"
                       class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition">
                      <i class="fas fa-times mr-1"></i> Reject
                    </a>
                  </div>
                <?php else: ?>
                  <span class="text-gray-500 text-xs">No action</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="20" class="px-4 py-6 text-center text-gray-500">No residents registered yet.</td>
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

 <script>
  // Live Search & Filter
  function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const tableRows = document.querySelectorAll('#tableBody tr');

    tableRows.forEach(row => {
      const name = row.dataset.name || '';
      const email = row.dataset.email || '';
      const phone = row.dataset.phone || '';
      const address = row.dataset.address || '';
      const status = row.dataset.status || '';

      const matchesSearch = name.includes(searchInput) ||
                            email.includes(searchInput) ||
                            phone.includes(searchInput) ||
                            address.includes(searchInput);

      const matchesStatus = statusFilter === 'all' || status === statusFilter;

      if (matchesSearch && matchesStatus) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  // Attach event listeners
  document.getElementById('searchInput').addEventListener('keyup', filterTable);
  document.getElementById('statusFilter').addEventListener('change', filterTable);
</script>
</body>
</html>
<?php $conn->close(); ?>
