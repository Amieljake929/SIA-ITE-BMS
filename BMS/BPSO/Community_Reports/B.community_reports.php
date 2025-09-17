<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BPSO') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch reports assigned to this BPSO
$sql = "SELECT * FROM community_reports 
        WHERE assigned_to = ? 
        AND status IN ('Assigned', 'For Closing', 'Completed')
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Collect unique values for filters
$residents = [];
$incidents = [];
$statuses = ['Assigned', 'For Closing', 'Completed']; // already filtered in SQL

while ($row = $result->fetch_assoc()) {
    $residentName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
    $incidentType = htmlspecialchars($row['incident_type']);
    if (!in_array($residentName, $residents)) $residents[] = $residentName;
    if (!in_array($incidentType, $incidents)) $incidents[] = $incidentType;
}

// Reset pointer for table rendering
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BPSO Reports - Bagbag eServices</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
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

  <!-- Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
        <!-- Home Icon Button and Title -->
        <div class="flex items-center space-x-4">
            <!-- Home Icon Button -->
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../BPSO_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Community Report - BPSO</h1>
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
              <a href="../../login/logout_official.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container mx-auto px-6 py-8 flex-grow">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Your Assigned Reports</h2>

    <!-- Filters -->
    <div class="mb-6 p-4 bg-white shadow rounded-lg flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
        <select id="filterStatus" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Statuses</option>
          <?php foreach ($statuses as $status): ?>
            <option value="<?= $status ?>"><?= $status ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Resident</label>
        <select id="filterResident" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Residents</option>
          <?php foreach ($residents as $resident): ?>
            <option value="<?= $resident ?>"><?= $resident ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Incident</label>
        <select id="filterIncident" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Incidents</option>
          <?php foreach ($incidents as $incident): ?>
            <option value="<?= $incident ?>"><?= $incident ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <button id="clearFilters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded text-sm font-medium transition">
          Clear Filters
        </button>
      </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200" id="reportsTable">
          <thead class="bg-green-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Resident</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Incident</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200" id="tableBody">
            <?php while ($row = $result->fetch_assoc()): ?>
              <?php
                $residentName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                $incidentType = htmlspecialchars($row['incident_type']);
                $status = htmlspecialchars($row['status']);
              ?>
              <tr class="hover:bg-gray-50 filterable-row"
                  data-resident="<?= $residentName ?>"
                  data-incident="<?= $incidentType ?>"
                  data-status="<?= $status ?>">
                <td class="px-6 py-4 text-sm font-medium"><?= htmlspecialchars($row['id']) ?></td>
                <td class="px-6 py-4 text-sm"><?= $residentName ?></td>
                <td class="px-6 py-4 text-sm"><?= $incidentType ?></td>
                <td class="px-6 py-4 text-sm">
                  <?php
                  $color = '';
                  if ($status === 'Assigned') $color = 'yellow';
                  elseif ($status === 'For Closing') $color = 'blue';
                  elseif ($status === 'Completed') $color = 'green';
                  else $color = 'gray';
                  ?>
                  <span class="px-2 py-1 text-xs rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800"><?= $status ?></span>
                </td>
                <td class="px-6 py-4 text-sm font-medium">
                  <?php if ($row['status'] !== 'Completed'): ?>
                    <a href="action_report.php?id=<?= $row['id'] ?>" class="text-green-600 hover:text-green-800">
                      <i class="fas fa-wrench"></i> Take Action
                    </a>
                  <?php else: ?>
                    <span class="text-gray-500 text-xs">No action needed</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-gray-500 text-center py-8">Walang reports na na-assign sa iyo.</p>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved.
  </footer>

    <!-- JavaScript -->
  <script>
    function updateTime() {
      const now = new Date();
      const options = {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      };
      document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Dropdown Toggle
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

    // Store original rows for re-filtering
    const originalRows = Array.from(document.querySelectorAll('.filterable-row'));

    // Filtering Logic
    function applyFilters() {
      const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
      const residentFilter = document.getElementById('filterResident').value.toLowerCase();
      const incidentFilter = document.getElementById('filterIncident').value.toLowerCase();

      const tableBody = document.getElementById('tableBody');
      let visibleCount = 0;

      // First, restore all original rows
      tableBody.innerHTML = '';
      originalRows.forEach(row => tableBody.appendChild(row));

      // Then apply filters
      const rows = document.querySelectorAll('.filterable-row');
      rows.forEach(row => {
        const resident = row.dataset.resident.toLowerCase();
        const incident = row.dataset.incident.toLowerCase();
        const status = row.dataset.status.toLowerCase();

        const matchesStatus = statusFilter === '' || status.includes(statusFilter);
        const matchesResident = residentFilter === '' || resident.includes(residentFilter);
        const matchesIncident = incidentFilter === '' || incident.includes(incidentFilter);

        if (matchesStatus && matchesResident && matchesIncident) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      // Show "No results" only if ALL rows are hidden
      if (visibleCount === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">No matching reports found.</td></tr>';
      }
    }

    document.getElementById('filterStatus').addEventListener('change', applyFilters);
    document.getElementById('filterResident').addEventListener('change', applyFilters);
    document.getElementById('filterIncident').addEventListener('change', applyFilters);

    document.getElementById('clearFilters').addEventListener('click', () => {
      document.getElementById('filterStatus').value = '';
      document.getElementById('filterResident').value = '';
      document.getElementById('filterIncident').value = '';
      applyFilters();
    });
  </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>