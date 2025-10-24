<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: ../../login/login.php");
    exit();
}

include '../../login/db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch resident data for autofill
$resident_info = [];
$stmt_user = $conn->prepare("
    SELECT u.full_name, u.email, r.dob, r.age, r.gender, r.civil_status, r.nationality, r.address, r.phone
    FROM users u
    LEFT JOIN residents r ON u.id = r.user_id
    WHERE u.id = ?
");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $resident_info = $result_user->fetch_assoc();
}
$stmt_user->close();

// Parse the full name into parts
$first_name = '';
$middle_name = '';
$last_name = '';
if (!empty($resident_info['full_name'])) {
    $name_parts = explode(' ', trim($resident_info['full_name']));
    $last_name = array_pop($name_parts);
    $first_name = array_shift($name_parts);
    $middle_name = implode(' ', $name_parts);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Anti Rabies Registration</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <style>
    input:focus, select:focus, textarea:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    }
    .readonly-field {
        background-color: #f3f4f6;
        color: #4b5563;
        cursor: not-allowed;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">LOADING...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <button
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../resident_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Anti Rabies Registration</h1>
        </div>

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
                <a href="../../login/logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                  <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
                </a>
              </li>
            </ul>
          </div>
        </div>
    </div>
  </header>

  <main class="flex-grow px-6 py-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">

      <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Anti Rabies Registration Form</h2>
      <p class="text-gray-600 text-center mb-8 text-sm">Please fill out the required fields for Anti Rabies Registration.</p>

      <form id="antiRabiesForm" action="R.submit_anti_rabies.php" method="POST">

        <!-- A. Impormasyon ng May-ari (Pet Owner Information) -->
        <section class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">A. Pet Owner Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
              <input type="text" name="owner_first_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" value="<?= htmlspecialchars($first_name) ?>" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
              <input type="text" name="owner_middle_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" value="<?= htmlspecialchars($middle_name) ?>" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
              <input type="text" name="owner_last_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" value="<?= htmlspecialchars($last_name) ?>" readonly>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
              <input type="text" name="owner_address" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" value="<?= htmlspecialchars($resident_info['address'] ?? '') ?>" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
              <input type="tel" name="owner_contact" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" value="<?= htmlspecialchars($resident_info['phone'] ?? '') ?>" readonly>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address (Optional)</label>
            <input type="email" name="owner_email" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" value="<?= htmlspecialchars($resident_info['email'] ?? '') ?>" readonly>
          </div>
        </section>

        <!-- B. Impormasyon ng Alagang Hayop (Pet Information) -->
        <section class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">B. Pet Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Pet Name</label>
              <input type="text" name="pet_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Pet Type</label>
              <select name="pet_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                <option value="">Select Type</option>
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Breed</label>
              <input type="text" name="pet_breed" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Color / Markings</label>
              <input type="text" name="pet_color_markings" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
              <select name="pet_sex" id="pet_sex" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" required>
                <option value="">Select Sex</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
            <div id="female_status" style="display: none;">
              <label class="block text-sm font-medium text-gray-700 mb-1">If Female</label>
              <select name="pet_female_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
                <option value="">Select Status</option>
                <option value="Spayed">Spayed</option>
                <option value="Intact">Intact</option>
              </select>
            </div>
            <div id="male_status" style="display: none;">
              <label class="block text-sm font-medium text-gray-700 mb-1">If Male</label>
              <select name="pet_male_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
                <option value="">Select Status</option>
                <option value="Neutered">Neutered</option>
                <option value="Intact">Intact</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Age / Birthdate</label>
              <input type="date" name="pet_age_birthdate" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Number of Dogs</label>
              <input type="number" name="num_dogs" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" min="0">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Number of Cats</label>
              <input type="number" name="num_cats" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition" min="0">
            </div>
          </div>
        </section>

        <!-- C. Kasaysayan ng Pagbabakuna (Vaccination History) -->
        <section class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">C. Vaccination History</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Last Vaccination Date</label>
              <input type="date" name="last_vaccination_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Vaccination Brand</label>
              <input type="text" name="vaccination_brand" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Has Pet Booklet/Certificate?</label>
              <select name="has_pet_booklet" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tag/Tattoo/Microchip Number</label>
              <input type="text" name="tag_tattoo_microchip" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 transition">
            </div>
          </div>
        </section>

        <!-- D. Para sa Kasalukuyang Bakuna (For Current Vaccination - Admin Use) -->
        <section class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">D. For Current Vaccination (Admin Use)</h3>
          <p class="text-sm text-gray-500 mb-4">This section will be filled by the veterinarian or staff during vaccination.</p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Vaccination Date</label>
              <input type="date" name="current_vaccination_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
              <input type="text" name="current_brand" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number / Lot No.</label>
              <input type="text" name="serial_lot_no" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
              <input type="date" name="expiration_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Next Vaccination Date</label>
              <input type="date" name="next_vaccination_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Veterinarian Name</label>
              <input type="text" name="veterinarian_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">PRC License No.</label>
              <input type="text" name="prc_license" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Veterinarian Signature</label>
            <textarea name="veterinarian_signature" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 readonly-field" readonly></textarea>
          </div>
        </section>

        <!-- E. Pagpapatibay (Consent) -->
        <section class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 border-green-200">E. Consent</h3>
          <div class="space-y-4">
            <div class="flex items-start">
              <input type="checkbox" name="consent_accuracy" id="consent_accuracy" class="mt-1 mr-3" required>
              <label for="consent_accuracy" class="text-sm text-gray-700">
                I certify that all information provided is true and accurate.
              </label>
            </div>
            <div class="flex items-start">
              <input type="checkbox" name="consent_side_effects" id="consent_side_effects" class="mt-1 mr-3" required>
              <label for="consent_side_effects" class="text-sm text-gray-700">
                I agree to vaccinate my pet and understand the possible side effects (if any).
              </label>
            </div>
            <div class="flex items-start">
              <input type="checkbox" name="consent_data_privacy" id="consent_data_privacy" class="mt-1 mr-3" required>
              <label for="consent_data_privacy" class="text-sm text-gray-700">
                I agree to the Data Privacy Act that my information will be used for public health and animal disease monitoring purposes.
              </label>
            </div>
          </div>
        </section>

        <div class="text-center">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
            <i class="fas fa-paper-plane mr-2"></i> Submit Registration
          </button>
        </div>
      </form>

    </div>
  </main>

  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <script>
    // Date/Time Function
    function updateTime() {
      const now = new Date();
      const options = {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      };
      const formattedDate = now.toLocaleString('en-US', options);
      document.getElementById('datetime').textContent = formattedDate.toUpperCase();
    }
    setInterval(updateTime, 1000);
    updateTime();

    // User Dropdown Toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');
    if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', (e) => {
          e.stopPropagation();
          userDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
          if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.add('hidden');
          }
        });
    }

    // Show/hide sex-specific fields
    document.getElementById('pet_sex').addEventListener('change', function() {
      const sex = this.value;
      document.getElementById('female_status').style.display = sex === 'Female' ? 'block' : 'none';
      document.getElementById('male_status').style.display = sex === 'Male' ? 'block' : 'none';
    });

    // Form validation
    const form = document.getElementById('antiRabiesForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        if (!confirm("Are you sure you want to submit your Anti Rabies Registration?")) {
          e.preventDefault();
        }
      });
    }
  </script>
</body>
</html>
