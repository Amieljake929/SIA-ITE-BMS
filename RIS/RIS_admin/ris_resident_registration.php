<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../RIS_login/db_connect.php';


// UPDATED: Get residents data and construct full_name using CONCAT_WS
$sql = "
    SELECT *,
           CONCAT_WS(' ', first_name, middle_name, last_name) AS full_name
    FROM registration
    WHERE status != 'rejected' OR status IS NULL
    ORDER BY created_at DESC
";
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

            <h1 class="text-xl font-bold text-green-800">Resident Registration</h1>
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

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
  <div class="flex-1">
    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
    <input type="text" id="searchInput" placeholder="Search by name..."
           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
  </div>

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
              <td class="px-4 py-2 text-xs text-gray-500"><?= htmlspecialchars($row['created_at']) ?></td>
              <td class="px-4 py-2">
                <a href="ris_view_details.php?id=<?= urlencode($row['id']) ?>"
                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition">
                  <i class="fas fa-eye mr-1"></i> View Details
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="px-4 py-6 text-center text-gray-500">No residents registered yet.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
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

 <script>
   // Live Search & Filter
   function filterTable() {
     const searchInput = document.getElementById('searchInput').value.toLowerCase();
     const statusFilter = document.getElementById('statusFilter').value;
     const tableRows = document.querySelectorAll('#tableBody tr');

     tableRows.forEach(row => {
       const name = row.dataset.name || '';
       const status = row.dataset.status || '';

       const matchesSearch = name.includes(searchInput);

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
