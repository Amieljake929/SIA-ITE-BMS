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
      <button data-role="Resident" class="role-tab px-4 py-2 rounded-full bg-white border border-green-700 text-green-800 hover:bg-green-50">Resident</button>
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
  <div id="addModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-green-800">Add User (BMS)</h3>
        <button id="btnCloseAdd" class="text-gray-500 hover:text-gray-700">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <form id="addForm" class="space-y-3">
        <div>
          <label class="block text-sm font-medium">Full name</label>
          <input name="full_name" class="mt-1 w-full border rounded-md px-3 py-2" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Email</label>
          <input type="email" name="email" class="mt-1 w-full border rounded-md px-3 py-2" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Password</label>
          <input type="password" name="password" class="mt-1 w-full border rounded-md px-3 py-2" required />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium">Role</label>
            <select name="role" class="mt-1 w-full border rounded-md px-3 py-2" required>
              <option value="">Select…</option>
              <option>Resident</option>
              <option>Official</option>
              <option>Staff</option>
              <option>BPSO</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">Status</label>
            <select name="status" class="mt-1 w-full border rounded-md px-3 py-2">
              <option>pending</option>
              <option>approved</option>
              <option>Active</option>
            </select>
          </div>
        </div>

        <div id="addError" class="text-sm text-red-600 hidden"></div>

        <div class="pt-2 flex justify-end gap-2">
          <button type="button" id="btnCancelAdd" class="px-4 py-2 rounded-md border">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. | Empowering Communities Digitally.
  </footer>

  <script>
    // API config
    const BMS_USERS_API = '/ITE-SIA/BMS/api/users.php';
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
      const users = data.items || [];

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
              <button class="px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700" data-del="${u.id}">
                <i class="fa fa-trash mr-1"></i>Delete
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
      if (!confirm(`Delete user #${id}?`)) return;

      const delUrl = `${BMS_USERS_API}?${API_QS}&id=${encodeURIComponent(id)}`;
      const res = await fetch(delUrl, { method: 'DELETE' });
      const data = await res.json();
      if (data.ok) {
        loadUsers(currentRole);
      } else {
        alert('Delete failed: ' + (data.error || 'Unknown error'));
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
      const form = new FormData(addForm);
      const payload = Object.fromEntries(form.entries());

      const postUrl = `${BMS_USERS_API}?${API_QS}`;
      const res = await fetch(postUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.ok) {
        addModal.classList.add('hidden');
        loadUsers(currentRole);
      } else {
        addError.textContent = data.error || 'Failed to add user';
        addError.classList.remove('hidden');
      }
    });

    function escapeHtml(s) {
      return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    // initial
    loadUsers('');
  </script>
</body>
</html>
