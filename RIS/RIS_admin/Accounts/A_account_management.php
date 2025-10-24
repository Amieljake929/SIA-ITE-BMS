<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RIS - Admin Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <!-- Main Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
      <div class="flex items-center space-x-4">
        <button 
          class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
          onclick="window.location.href='../ris_admin_dashboard.php'"
          title="Home"
        >
          <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
        </button>
        <h1 class="text-xl font-bold text-green-800">Account Management</h1>
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
              <a href="../../RIS_login/ris_logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- CONTENT -->
  <main class="container mx-auto px-6 py-6 flex-1">

    <!-- Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-4">
      <button data-role="" class="role-tab px-4 py-2 rounded-full bg-green-700 text-white hover:bg-green-800">All</button>
      <button data-role="Official" class="role-tab px-4 py-2 rounded-full bg-white border border-green-700 text-green-800 hover:bg-green-50">Official</button>
      <button data-role="Staff"    class="role-tab px-4 py-2 rounded-full bg-white border border-green-700 text-green-800 hover:bg-green-50">Staff</button>
      <button data-role="BPSO"     class="role-tab px-4 py-2 rounded-full bg-white border border-green-700 text-green-800 hover:bg-green-50">BPSO</button>

      <div class="ml-auto">
        <button id="btnOpenAdd" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
          <i class="fa fa-plus mr-2"></i>Add User
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Full name</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody id="usersTbody" class="divide-y divide-gray-100 bg-white"></tbody>
        </table>
      </div>
      <div id="tableEmpty" class="p-6 text-center text-gray-600 hidden">No users found.</div>
    </div>
  </main>

  <!-- Add User Modal -->
  <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8 border border-gray-200">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-green-800">Add User (BMS)</h3>
        <button id="btnCloseAdd" class="text-gray-500 hover:text-gray-700 text-2xl">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <form id="addForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Full name</label>
          <input name="full_name" class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <div class="relative">
            <input type="password" name="password" id="password" class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-green-500 focus:border-transparent" required />
            <button type="button" id="togglePassword" class="absolute right-3 top-3 text-gray-500 focus:outline-none">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>
          <ul id="password-strength-requirements" class="mt-2 text-xs">
            <li id="req-length" class="strength-req text-red-500"><i class="fas fa-times-circle mr-1"></i> At least 8 characters</li>
            <li id="req-uppercase" class="strength-req text-red-500"><i class="fas fa-times-circle mr-1"></i> At least one uppercase letter (A-Z)</li>
            <li id="req-lowercase" class="strength-req text-red-500"><i class="fas fa-times-circle mr-1"></i> At least one lowercase letter (a-z)</li>
            <li id="req-number" class="strength-req text-red-500"><i class="fas fa-times-circle mr-1"></i> At least one number (0-9)</li>
            <li id="req-special" class="strength-req text-red-500"><i class="fas fa-times-circle mr-1"></i> At least one special character (!@#$%)</li>
          </ul>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Role</label>
          <select name="role" class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
            <option value="">Select…</option>
            <option>Official</option>
            <option>Staff</option>
            <option>BPSO</option>
          </select>
        </div>

        <div id="addError" class="text-sm text-red-600 hidden"></div>

        <div class="pt-4 flex justify-end gap-3">
          <button type="button" id="btnCancelAdd" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
          <button type="submit" id="submitBtn" class="px-6 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center mx-4">
      <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
      </svg>
      <h2 class="text-2xl font-semibold mb-2" id="successTitle">Success!</h2>
      <p class="text-gray-700 mb-4" id="successMessage">Operation completed successfully.</p>
      <button id="closeSuccessModalBtn" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 transition-colors w-full">OK</button>
    </div>
  </div>

  <!-- Error Modal -->
  <div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
    <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 max-w-sm text-center mx-4">
      <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-red-100">
        <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
      </div>
      <h2 class="text-xl sm:text-2xl font-semibold mb-2 text-gray-800">Oops!</h2>
      <p id="errorModalMessage" class="text-gray-600 mb-6"></p>
      <button id="closeErrorModalBtn" class="bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition-colors w-full">Try Again</button>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <script>
    // API config
    // API config
const BMS_USERS_API = '/SIA-ITE-BMS/BMS/api/users.php'; // <--- TAMA NA ITO!
    const API_TOKEN = 'RIS_TO_BMS_USERS_2025';
    const API_QS = 'api_token=' + encodeURIComponent(API_TOKEN);

    // clock
    function updateTime() {
      const now = new Date();
      const options = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' };
      document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();
    }
    setInterval(updateTime, 1000); updateTime();

    // dropdown
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');
    userMenuButton.addEventListener('click', (e) => { e.stopPropagation(); userDropdown.classList.toggle('hidden'); });
    document.addEventListener('click', (e) => { if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) userDropdown.classList.add('hidden'); });

    // table
    const tbody = document.getElementById('usersTbody');
    const tableEmpty = document.getElementById('tableEmpty');
    let currentRole = '';

    async function loadUsers(role = '') {
      currentRole = role;
      const params = new URLSearchParams();
      if (role) params.set('role', role);
      const url = `${BMS_USERS_API}?${API_QS}${params.toString() ? '&' + params.toString() : ''}`;

      const res = await fetch(url);
      if (!res.ok) {
        const txt = await res.text();
        console.error('Users API error:', res.status, txt);
        tbody.innerHTML = '';
        tableEmpty.classList.remove('hidden');
        tableEmpty.textContent = `Failed to load users (${res.status}). Check console.`;
        return;
      }
      const data = await res.json();
      const users = (data.items || []).filter(u => u.role !== 'Resident');

      tbody.innerHTML = '';
      if (users.length === 0) {
        tableEmpty.classList.remove('hidden');
      } else {
        tableEmpty.classList.add('hidden');
        for (const u of users) {
          const tr = document.createElement('tr');
          tr.className = 'hover:bg-gray-50';
          tr.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-700">${u.id}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(u.full_name || '')}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(u.email || '')}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(u.role || '')}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(u.status || '')}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(u.created_at || '')}</td>
            <td class="px-4 py-3 text-right">
              <button class="px-3 py-1 rounded-md bg-orange-600 text-white hover:bg-orange-700" data-del="${u.id}">
                <i class="fa fa-archive mr-1"></i>Remove
              </button>
            </td>
          `;
          tbody.appendChild(tr);
        }
      }
    }

    // tabs
    document.querySelectorAll('.role-tab').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.role-tab').forEach(b => b.classList.remove('bg-green-700','text-white'));
        document.querySelectorAll('.role-tab').forEach(b => b.classList.add('bg-white','text-green-800'));
        btn.classList.add('bg-green-700','text-white');
        btn.classList.remove('bg-white','text-green-800');
        loadUsers(btn.dataset.role || '');
      });
    });

    // delete
    tbody.addEventListener('click', async (e) => {
      const btn = e.target.closest('button[data-del]');
      if (!btn) return;
      const id = btn.getAttribute('data-del');
      if (!confirm(`Remove user #${id}? This will archive the user data.`)) return;

      const delUrl = `${BMS_USERS_API}?${API_QS}&id=${encodeURIComponent(id)}`;
      const res = await fetch(delUrl, { method: 'DELETE' });
      const data = await res.json();
      if (data.ok) {
        showSuccessModal('User Removed Successfully', 'The user has been successfully removed from the system.');
        loadUsers(currentRole);
      } else {
        showErrorModal('Delete failed: ' + (data.error || 'Unknown error'));
      }
    });

    // modal
    const addModal = document.getElementById('addModal');
    const btnOpenAdd = document.getElementById('btnOpenAdd');
    const btnCloseAdd = document.getElementById('btnCloseAdd');
    const btnCancelAdd = document.getElementById('btnCancelAdd');
    const addForm = document.getElementById('addForm');
    const addError = document.getElementById('addError');

    btnOpenAdd.addEventListener('click', () => { addModal.classList.remove('hidden'); addModal.classList.add('flex'); addError.classList.add('hidden'); addForm.reset(); });
    btnCloseAdd.addEventListener('click', () => addModal.classList.add('hidden'));
    btnCancelAdd.addEventListener('click', () => addModal.classList.add('hidden'));

    addForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      addError.classList.add('hidden');

      // Validate password strength
      const password = document.getElementById('password').value;
      if (!isPasswordStrong(password)) {
        showErrorModal('Password does not meet security requirements. Please ensure it has at least 8 characters, including uppercase, lowercase, number, and special character.');
        return;
      }

      const form = new FormData(addForm);
      const payload = Object.fromEntries(form.entries());
      payload.status = 'approved'; // Set default status to approved

      const postUrl = `${BMS_USERS_API}?${API_QS}`;
      const res = await fetch(postUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.ok) {
        showSuccessModal('User Added Successfully', 'The new user has been successfully added to the system.');
        addModal.classList.add('hidden');
        loadUsers(currentRole);
      } else {
        if (data.error && data.error.toLowerCase().includes('duplicate')) {
          showErrorModal('A user with the same name, email, or password already exists. Please use different credentials.');
        } else {
          showErrorModal(data.error || 'Failed to add user');
        }
      }
    });

    function escapeHtml(s) {
      return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    // password toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      eyeIcon.classList.toggle('fa-eye');
      eyeIcon.classList.toggle('fa-eye-slash');
    });

    // password strength checker
    passwordInput.addEventListener('input', () => {
      const password = passwordInput.value;
      updatePasswordStrength(password);
    });

    function updatePasswordStrength(password) {
      const reqLength = document.getElementById('req-length');
      const reqUppercase = document.getElementById('req-uppercase');
      const reqLowercase = document.getElementById('req-lowercase');
      const reqNumber = document.getElementById('req-number');
      const reqSpecial = document.getElementById('req-special');

      const hasLength = password.length >= 8;
      const hasUppercase = /[A-Z]/.test(password);
      const hasLowercase = /[a-z]/.test(password);
      const hasNumber = /\d/.test(password);
      const hasSpecial = /[!@#$%]/.test(password);

      updateRequirement(reqLength, hasLength);
      updateRequirement(reqUppercase, hasUppercase);
      updateRequirement(reqLowercase, hasLowercase);
      updateRequirement(reqNumber, hasNumber);
      updateRequirement(reqSpecial, hasSpecial);
    }

    function updateRequirement(element, isValid) {
      const icon = element.querySelector('i');
      if (isValid) {
        element.classList.remove('text-red-500');
        element.classList.add('text-green-500');
        icon.classList.remove('fa-times-circle');
        icon.classList.add('fa-check-circle');
      } else {
        element.classList.remove('text-green-500');
        element.classList.add('text-red-500');
        icon.classList.remove('fa-check-circle');
        icon.classList.add('fa-times-circle');
      }
    }

    function isPasswordStrong(password) {
      return password.length >= 8 &&
             /[A-Z]/.test(password) &&
             /[a-z]/.test(password) &&
             /\d/.test(password) &&
             /[!@#$%]/.test(password);
    }

    // modal functions
    function showSuccessModal(title, message) {
      document.getElementById('successTitle').textContent = title;
      document.getElementById('successMessage').textContent = message;
      document.getElementById('successModal').classList.remove('hidden');
      document.getElementById('successModal').classList.add('flex');
    }

    function showErrorModal(message) {
      document.getElementById('errorModalMessage').textContent = message;
      document.getElementById('errorModal').classList.remove('hidden');
      document.getElementById('errorModal').classList.add('flex');
    }

    // close modals
    document.getElementById('closeSuccessModalBtn').addEventListener('click', () => {
      document.getElementById('successModal').classList.add('hidden');
    });

    document.getElementById('closeErrorModalBtn').addEventListener('click', () => {
      document.getElementById('errorModal').classList.add('hidden');
    });

    // reset form on modal open
    btnOpenAdd.addEventListener('click', () => {
      addModal.classList.remove('hidden');
      addModal.classList.add('flex');
      addError.classList.add('hidden');
      addForm.reset();
      updatePasswordStrength(''); // Reset password strength indicators
    });

    // initial
    loadUsers('');
  </script>
</body>
</html>
