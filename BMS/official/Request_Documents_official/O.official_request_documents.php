<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';


// Default active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'barangay_clearance';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Document types mapping
$doc_types = [
    'barangay_clearance' => 'Barangay Clearance',
    'business_permit' => 'Business Permit',
    'certificate_of_residency' => 'Certificate of Residency',
    'certificate_of_indigency' => 'Certificate of Indigency',
    'cedula' => 'Cedula',
    'solo_parents' => 'Solo Parent Certificate',
    'first_time_job_seekers' => 'First Time Job Seeker'
];

// Helper: Sanitize output
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Build query to get only **validated** requests by Staff
$table = $conn->real_escape_string($active_tab);
$search_like = "%" . $conn->real_escape_string($search) . "%";

// Only show records where status = 'Validated'
$query = "SELECT * FROM `$table` WHERE status = 'Validated'";
$params = [];
$types = "";

if ($status_filter !== 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND (first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?";
    if ($active_tab === 'business_permit') {
        $query .= " OR business_name LIKE ?";
        $params = array_merge($params, [$search_like, $search_like, $search_like, $search_like]);
        $types .= "ssss";
    } else {
        $params = array_merge($params, [$search_like, $search_like, $search_like]);
        $types .= "sss";
    }
    $query .= ")";
}

$stmt = $conn->prepare($query);
if ($types) $stmt->bind_param($types, ...$params);
$result = $stmt->execute() ? $stmt->get_result() : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Document Requests - Official Portal</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">Loading...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow"/>
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
                onclick="window.location.href='../official_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Document Approval Requests</h1>
        </div>

      <!-- User Info with Dropdown -->
      <div class="relative inline-block text-right">
        <button id="userMenuButton" class="flex items-center font-medium cursor-pointer text-sm focus:outline-none whitespace-nowrap">
          <span class="text-gray-800">Logged in:</span>
          <span class="text-blue-700 ml-1"><?= e($_SESSION['full_name']) ?></span>
          <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
        </button>
        <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl hidden z-10">
          <ul class="py-2 text-sm">
            <li>
              <a href="#" class="block px-5 py-2 text-gray-700 hover:bg-green-50 hover:text-green-800 transition flex items-center">
                <i class="fas fa-user text-green-600 mr-3"></i> Profile
              </a>
            </li>
            <li>
              <a href="../../login/logout_official.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- Document Type Tabs -->
  <div class="container mx-auto mt-6 px-6">
    <div class="bg-white rounded-lg shadow-md p-1 inline-flex space-x-1 mb-6 flex-wrap">
      <?php foreach ($doc_types as $key => $label): ?>
        <a href="?tab=<?= $key ?>&status=<?= $status_filter ?>&search=<?= e($search) ?>"
           class="px-4 py-2 text-sm font-medium rounded-md transition
                  <?= $active_tab === $key ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-100' ?>">
          <?= $label ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
      <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <input type="hidden" name="tab" value="<?= e($active_tab) ?>">

        <!-- Search -->
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-600 mb-1">Search</label>
          <input type="text" name="search" value="<?= e($search) ?>"
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                 placeholder="Search by name...">
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
          <select name="status" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Approved" <?= $status_filter === 'Approved' ? 'selected' : '' ?>>Approved</option>
            <option value="Rejected" <?= $status_filter === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
          </select>
        </div>

        <!-- Submit -->
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition">
          Filter
        </button>
        <?php if ($search || $status_filter !== 'all'): ?>
          <a href="?tab=<?= $active_tab ?>" class="text-red-500 hover:underline text-sm self-center">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">
          <?= $doc_types[$active_tab] ?> Requests (For Approval)
        </h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <?php
              $headers = [];
              switch ($active_tab) {
                  case 'barangay_clearance':
                      $headers = ['First Name', 'Middle Name', 'Last Name', 'Status'];
                      break;
                  case 'business_permit':
                      $headers = ['Permit ID', 'First Name', 'Middle Name', 'Last Name', 'Status'];
                      break;
                  case 'certificate_of_residency':
                      $headers = ['Residency ID', 'First Name', 'Middle Name', 'Last Name', 'Status'];
                      break;
                  case 'certificate_of_indigency':
                      $headers = ['Indigency ID', 'First Name', 'Middle Name', 'Last Name', 'Status'];
                      break;
                  case 'cedula':
                      $headers = ['Cedula ID', 'First Name', 'Middle Name', 'Last Name', 'Status'];
                      break;
                  case 'solo_parents':
                      $headers = ['Solo Parent ID', 'First Name', 'Middle Name', 'Last Name', 'Status'];
                      break;
                  case 'first_time_job_seekers':
                      $headers = ['Job Seeker ID', 'First Name', 'Middle Name', 'Last Name', 'Status'];
                      break;
              }
              foreach ($headers as $th): ?>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <?= $th ?>
                </th>
              <?php endforeach; ?>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50 transition">
                  <?php switch ($active_tab):
                    case 'barangay_clearance': ?>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['middle_name']) ?></td>
                    <td><?= e($row['last_name']) ?></td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded-full
                        <?= $row['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                           ($row['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                        <?= e($row['status']) ?>
                      </span>
                    </td>
                    <?php break; ?>

                    <?php case 'business_permit': ?>
                    <td><?= e($row['business_permit_id']) ?></td>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['middle_name']) ?></td>
                    <td><?= e($row['last_name']) ?></td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded-full
                        <?= $row['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                           ($row['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                        <?= e($row['status']) ?>
                      </span>
                    </td>
                    <?php break; ?>

                    <?php case 'certificate_of_residency': ?>
                    <td><?= e($row['residency_id']) ?></td>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['middle_name']) ?></td>
                    <td><?= e($row['last_name']) ?></td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded-full
                        <?= $row['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                           ($row['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                        <?= e($row['status']) ?>
                      </span>
                    </td>
                    <?php break; ?>

                    <?php case 'certificate_of_indigency': ?>
                    <td><?= e($row['indigency_id']) ?></td>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['middle_name']) ?></td>
                    <td><?= e($row['last_name']) ?></td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded-full
                        <?= $row['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                           ($row['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                        <?= e($row['status']) ?>
                      </span>
                    </td>
                    <?php break; ?>

                    <?php case 'cedula': ?>
                    <td><?= e($row['cedula_id']) ?></td>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['middle_name']) ?></td>
                    <td><?= e($row['last_name']) ?></td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded-full
                        <?= $row['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                           ($row['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                        <?= e($row['status']) ?>
                      </span>
                    </td>
                    <?php break; ?>

                    <?php case 'solo_parents': ?>
                    <td><?= e($row['solo_parent_id']) ?></td>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['middle_name']) ?></td>
                    <td><?= e($row['last_name']) ?></td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded-full
                        <?= $row['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                           ($row['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                        <?= e($row['status']) ?>
                      </span>
                    </td>
                    <?php break; ?>

                    <?php case 'first_time_job_seekers': ?>
                    <td><?= e($row['first_time_job_id']) ?></td>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['middle_name']) ?></td>
                    <td><?= e($row['last_name']) ?></td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded-full
                        <?= $row['status'] === 'Approved' ? 'bg-green-100 text-green-800' :
                           ($row['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                        <?= e($row['status']) ?>
                      </span>
                    </td>
                    <?php break; ?>
                  <?php endswitch; ?>

                  <!-- Actions -->
                  <td class="px-6 py-3 text-sm">
                    <div class="flex space-x-2">
                      <!-- View Details -->
                      <a href="O.view_document_details.php?tab=<?= $active_tab ?>&id=<?= $row['id'] ?>"
                         class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-eye mr-1"></i> View
                      </a>

                      <!-- Approve and Reject Buttons (Separated) -->
                      <?php if ($row['status'] === 'Validated'): ?>
                        <!-- Approve Button -->
                        <a href="O.approve_reject_document.php?tab=<?= $active_tab ?>&id=<?= $row['id'] ?>&action=approve"
                           class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition flex items-center">
                          <i class="fas fa-check mr-1"></i> Approve
                        </a>

                        <!-- Reject Button -->
                        <a href="O.approve_reject_document.php?tab=<?= $active_tab ?>&id=<?= $row['id'] ?>&action=reject"
                           class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition flex items-center">
                          <i class="fas fa-times mr-1"></i> Reject
                        </a>
                      <?php else: ?>
                        <span class="text-gray-400 text-xs">Action Done</span>
                      <?php endif; ?>

                      <!-- Print (Only if Approved) -->
                      <?php if ($row['status'] === 'Approved'): ?>
                        <a href="print_document.php?tab=<?= $active_tab ?>&id=<?= $row['id'] ?>" target="_blank"
                           class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition flex items-center">
                          <i class="fas fa-print mr-1"></i> Print
                        </a>
                      <?php else: ?>
                        <span class="text-gray-400 px-3 py-1 text-xs cursor-not-allowed">Print</span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center py-4 text-gray-500">
                  No validated requests found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <!-- JavaScript -->
  <script>
    // Update time
    function updateTime() {
      const now = new Date();
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      };
      const formattedDate = now.toLocaleString('en-US', options);
      document.getElementById('datetime').textContent = formattedDate.toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();

    // User Dropdown Toggle
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