<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
include '../../login/db_connect.php';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BagbagCare - Resident Portal</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">THURSDAY, AUGUST 7, 2025, 11:16:33 AM</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">

  <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../resident_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Emergency Contacts</h1>
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
              <a href="../login/logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  
  <main class="flex-grow">
    <div class="container mx-auto px-6 py-12 md:py-16"> <?php
      // Data mula sa screenshots
      $contacts = [
          ['name' => 'Rolando H. Mejila (Bhojie)', 'position' => 'Barangay Administrator', 'contact' => '8779-77-83 Local 104'],
          ['name' => 'Rolando V. Pascor (Rolly)', 'position' => 'Barangay Secretary', 'contact' => '8779-77-83 Local 101'],
          ['name' => 'Leopoldo L. Musnit (Paul)', 'position' => 'BPSIO Executive Officer', 'contact' => '0939-181-10-19'],
          ['name' => 'Mary Ann U. Ambita (Mean)', 'position' => 'Gender and Development', 'contact' => '0917-842-96-74'],
          ['name' => 'Ma. Elena Villareal (Elena)', 'position' => 'VAWC', 'contact' => '0947-616-72-93'],
          ['name' => 'Yasmira D. Randa (Yas)', 'position' => 'BCPC', 'contact' => '0930-925-00-12'],
          ['name' => 'Constancia V. Ambita (Inang Kapitana)', 'position' => 'Task Force Kalinisan', 'contact' => '8779-77-83 Local 101'],
          ['name' => 'Alexander Margate (Jonjon)', 'position' => 'Task Force Palengke', 'contact' => '0923-640-59-93'],
          ['name' => 'Richard F. Cejas (Richard)', 'position' => 'Disaster Management', 'contact' => '0923-284-70-38'],
          ['name' => 'Alex Gutierrez (Alex)', 'position' => 'BHERT (Covid & Emergency)', 'contact' => '0915-422-15-15'],
          ['name' => 'Renato Chicano (Nato)', 'position' => 'Business Inspector', 'contact' => '0909-053-14-01'],
          ['name' => 'Isabela Quintos (Sabel)', 'position' => 'Environmental Police', 'contact' => '0910-444-55-27'],
          ['name' => 'Jennifer Silvestre (Jen)', 'position' => 'DAP/PWD', 'contact' => '0906-817-41-00'],
          ['name' => 'Bebelita F. Naro (Bebe/Baby)', 'position' => 'Senior Citizen', 'contact' => '0966-688-25-84'],
          ['name' => 'Rodel Edroso (Rodel)', 'position' => 'Solo Parents', 'contact' => '0921-869-00-00'],
          ['name' => 'Leo Avila (Leo)', 'position' => 'LGBTQI', 'contact' => '0950-156-91-29'],
          ['name' => 'Jeffrey Lachica (Jeff)', 'position' => 'Lupon President', 'contact' => '0919-829-80-77'],
          ['name' => 'Manuel Rodriguez (Manny)', 'position' => 'Solid Waste/Clearing', 'contact' => '0939-565-50-92'],
          ['name' => 'Roldan Sta. Ines (Roldan)', 'position' => 'Traffic', 'contact' => '0928-572-72-38'],
          ['name' => 'Cezar Evasco (Cezar)', 'position' => 'Maintenance/Infra', 'contact' => '0920-652-55-02'],
          ['name' => 'Imelda Carreon (Inday)', 'position' => 'Purok King Cluster', 'contact' => '0999-379-61-61'],
          ['name' => 'Emily Jacobo (Emily)', 'position' => 'Purok Pagkabuhay Cluster', 'contact' => '0927-822-43-13'],
          ['name' => 'Erlinda Calixtro Salin (Linda)', 'position' => 'Purok Quirino Cluster', 'contact' => '0933-869-70-38'],
          ['name' => 'Emeteria Sanchez (Emy)', 'position' => 'BADAC (Anti-Drugs)', 'contact' => '0906-401-05-33'],
          ['name' => 'Efren Antido (Efren)', 'position' => 'CFAG/Fire', 'contact' => '0953-300-99-72'],
          ['name' => 'Eduardo Garcia (Eddie)', 'position' => 'Maintenance/Transport', 'contact' => '0949-752-07-64'],
          ['name' => 'Aurora Bufiao (Au)', 'position' => 'Land and Housing', 'contact' => '0968-609-95-07'],
          ['name' => 'Roberto Olivarez (Obet)', 'position' => 'Land & Housing Inspector', 'contact' => '0912-945-28-36'],
          ['name' => 'Richard V. Ambita (Kap Rex)', 'position' => 'Punong Barangay', 'contact' => '0949-792-61-28']
      ];

      // Helper function para sa pag-format ng contact number
      function format_phone_for_tel($number) {
          // Palitan ang 'Local ' ng comma (para sa extension)
          $tel = str_ireplace('Local ', ',', $number);
          // Tanggalin lahat ng non-numeric characters maliban sa comma
          $tel = preg_replace('/[^\d,]/', '', $tel);
          return $tel;
      }

      // Helper function para ihiwalay ang pangalan at nickname
      function parse_name($full_name) {
          $parts = explode('(', $full_name);
          $name = trim($parts[0]);
          $nickname = isset($parts[1]) ? '(' . trim($parts[1], ' )') . ')' : '';
          return ['name' => $name, 'nickname' => $nickname];
      }
      ?>

      <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-2xl font-bold text-green-800">Key Contacts</h2>
          <p class="text-sm text-gray-600 mt-1">Mga opisyal ng barangay at kanilang mga contact number para sa inyong mga katanungan at pangangailangan.</p>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-700">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                  Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                  Position / Concerns
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                  Contact Number
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php foreach ($contacts as $contact): ?>
              <?php $name_parts = parse_name($contact['name']); ?>
              <tr class="hover:bg-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($name_parts['name']) ?></div>
                  <?php if (!empty($name_parts['nickname'])): ?>
                  <div class="text-sm text-gray-500"><?= htmlspecialchars($name_parts['nickname']) ?></div>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-normal"> <div class="text-sm text-gray-800"><?= htmlspecialchars($contact['position']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <a href="tel:<?= format_phone_for_tel($contact['contact']) ?>" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center group">
                    <i class="fas fa-phone-alt fa-fw mr-2 text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                    <?= htmlspecialchars($contact['contact']) ?>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
          <i class="fas fa-info-circle mr-1"></i> Tip: Pindutin ang contact number para direktang tumawag (sa mga mobile device).
        </div>
      </div>

    </div>
  </main>
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

  <div id="mobileMenu" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-4/5 max-w-xs rounded-lg shadow-xl p-6">
      <h3 class="text-lg font-bold text-gray-800 mb-4">Navigation</h3>
      <ul class="space-y-3">
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Home</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Services</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">About</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Contact</a></li>
        <li><a href="logout.php" class="block text-green-700 hover:text-green-900 font-medium">Logout</a></li>
      </ul>
      <button id="closeMenu" class="mt-4 text-red-500 text-sm">Close</button>
    </div>
  </div>

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
      // Gumamit ng 'en-US' para sa format pero pwede palitan if needed
      const formattedDate = now.toLocaleString('en-US', options);
      document.getElementById('datetime').textContent = formattedDate.toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();




    // Close with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeLightbox(); // Tandaan: 'closeLightbox' ay not defined sa code mo, baka placeholder 'to?
    });

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