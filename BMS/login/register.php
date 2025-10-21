<?php
// IMPORTANT: Start session at the very top to handle CAPTCHA
session_start();

$error_msg = '';
if (isset($_GET['error'])) {
    $error_msg = htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resident Registration | Barangay Bagbag</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    /* No changes needed in CSS, all layout adjustments are done with Tailwind classes */
    body {
      background-color: #f8f9fa; color: #212529; font-size: 16px; line-height: 1.7;
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233a9d6a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .btn-primary { background-color: #3a9d6a; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 1rem; }
    .btn-primary:hover:not(:disabled) { background-color: #2d7c4a; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(58, 157, 106, 0.2); }
    .btn-primary:disabled { background-color: #9ca3af; cursor: not-allowed; }
    .form-input, .form-select { border: 1px solid #ced4da; border-radius: 8px; padding: 12px 16px; font-size: 1rem; width: 100%; transition: all 0.3s ease; }
    .form-input:focus, .form-select:focus { border-color: #3a9d6a; box-shadow: 0 0 0 3px rgba(58, 157, 106, 0.2); outline: none; }
    .card { border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; background: white; }
    .icon-circle { background: linear-gradient(135deg, #e8f5e8, #f0f9f0); border: 2px solid #3a9d6a; border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; animation: float 3s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .link { color: #3a9d6a; font-weight: 500; }
    .link:hover { text-decoration: underline; color: #2d7c4a; }
    .input-group { position: relative; }
    .input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #6c757d; }
    .form-input.has-icon { padding-left: 40px; }
    .form-section { border-top: 1px solid #e5e7eb; margin-top: 1.5rem; padding-top: 1.5rem; }
    .loading { display: inline-block; width: 16px; height: 16px; border: 2px solid #f3f3f3; border-top: 2px solid #3a9d6a; border-radius: 50%; animation: spin 1s linear infinite; margin-left: 8px; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .badge-resident { background: linear-gradient(to right, #d1f0e1, #e8f5e8); border: 1px solid #3a9d6a; color: #1f7042; padding: 4px 12px; border-radius: 50px; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; }
    .illustration-reg { max-width: 220px; margin: 20px auto 0; opacity: 0.9; }
    #password-strength-requirements { list-style-type: none; padding: 0; margin-top: 0.5rem; font-size: 0.875rem; }
    .strength-req { color: #ef4444; transition: color 0.3s ease; }
    .strength-req.valid { color: #22c55e; text-decoration: line-through; }
    @keyframes fade-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .modal-animate { animation: fade-in 0.3s ease-out forwards; }
    #captcha_image { border: 1px solid #d1d5db; border-radius: 8px; }
    #refreshCaptchaBtn { color: #3a9d6a; cursor: pointer; }
    #refreshCaptchaBtn:hover { color: #2d7c4a; }
  </style>
</head>
<!-- CHANGE: Added pt-24 to body to make space for the fixed navbar -->
<body class="font-sans min-h-screen flex items-center justify-center p-4 pt-28">

<nav class="fixed top-0 left-0 right-0 bg-[#3a9d6a] shadow-md z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">
      <div class="flex-shrink-0 flex items-center">
        <!-- CHANGE: Made logo smaller to fit the navbar height -->
        <img src="../images/Bagbag.png" alt="Barangay Logo" class="h-12 w-12">
        <span class="ml-3 text-white font-semibold text-lg">BagbagCare - Resident Portal</span>
      </div>
      <div class="flex-shrink-0">
        <a href="website2.php" class="text-white hover:text-green-100 text-sm font-medium flex items-center">
          <i class="fas fa-arrow-left mr-2"></i> Back to Website
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- CHANGE: Increased width from max-w-5xl to max-w-7xl -->
<div class="card w-full max-w-7xl mx-auto overflow-hidden bg-white">
    <div class="flex flex-col lg:flex-row">
      <!-- Left side (branding) - No major changes -->
      <div class="lg:w-2/5 p-8 md:p-10 bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 flex flex-col justify-center">
        <div class="icon-circle mb-5 animate-pulse"><i class="fas fa-home text-3xl text-green-700"></i></div>
        <span class="badge-resident mb-4 justify-center"><i class="fas fa-user-shield mr-1.5"></i> Resident Registration Only</span>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 text-center">Join Barangay Bagbag</h2>
        <p class="text-gray-600 text-center leading-relaxed text-sm sm:text-base mb-6">Create your secure account to access services, announcements, and support.</p>
        <div class="illustration-reg">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3a9d6a" stroke-width="1.5">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 11v4M22 17v4M18 15h4M22 19h-4"/>
          </svg>
        </div>
        <div class="bg-white/70 rounded-xl p-5 shadow-sm border border-green-100 mt-6">
          <ul class="space-y-3 text-sm text-gray-700">
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Free access to services</li>
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Secure personal account</li>
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Real-time updates</li>
          </ul>
        </div>
      </div>

      <!-- Right Side: Form (now wider and more compact) -->
      <div class="lg:w-3/5 p-8 md:p-10 flex flex-col justify-center">
        <div class="text-center mb-6">
            <div class="icon-circle mx-auto mb-4" style="width: 50px; height: 50px;"><i class="fas fa-user-plus text-lg text-green-700"></i></div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Create Your Account</h1>
            <p class="text-gray-600 text-xs sm:text-sm mt-2">Fill in your information to get started</p>
        </div>
        
        <!-- CHANGE: Changed space-y-5 to space-y-4 for tighter vertical spacing -->
        <form id="registerForm" class="space-y-4" novalidate>
          
          <!-- CHANGE: Fields are now in a 2-column grid to save vertical space -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-barcode text-gray-500 mr-1"></i> Reference Number</label>
              <div class="input-group relative">
                <i class="fas fa-barcode input-icon"></i>
                <input type="text" id="reference_number" name="reference_number" class="form-input has-icon text-base" placeholder="ABC12-XYZ34-5MN6" required />
                <span id="loadingSpinner" class="loading hidden"></span>
              </div>
            </div>
            <div>
              <label for="fullname" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-user text-gray-500 mr-1"></i> Full Name</label>
              <div class="input-group"><i class="fas fa-user input-icon"></i><input type="text" id="fullname" name="fullname" class="form-input has-icon text-base" placeholder="Juan Dela Cruz" readonly required /></div>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-envelope text-gray-500 mr-1"></i> Email Address</label>
              <div class="input-group"><i class="fas fa-envelope input-icon"></i><input type="email" id="email" name="email" class="form-input has-icon text-base" placeholder="you@example.com" readonly required /></div>
            </div>
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-phone text-gray-500 mr-1"></i> Mobile Number</label>
              <div class="input-group"><i class="fas fa-phone input-icon"></i><input type="tel" id="phone" name="phone" class="form-input has-icon text-base" placeholder="+63 912 345 6789" required /></div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label for="dob" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-calendar-alt text-gray-500 mr-1"></i> Date of Birth</label><input type="date" id="dob" name="dob" class="form-input text-base" readonly required /></div>
            <div><label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age</label><input type="number" id="age" name="age" class="form-input text-base" placeholder="Auto-calculated" readonly /></div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label for="pob" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-map-marker-alt text-gray-500 mr-1"></i> Place of Birth</label><input type="text" id="pob" name="pob" class="form-input text-base" placeholder="Manila, Philippines" required /></div>
            <div>
              <label for="gender" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-venus-mars text-gray-500 mr-1"></i> Gender</label>
              <select id="gender" name="gender" class="form-select" required><option value="" disabled selected>Select Gender</option><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-ring text-gray-500 mr-1"></i> Civil Status</label>
              <select id="civil_status" name="civil_status" class="form-select" required>
                <option value="" disabled selected>Select Status</option><option value="Single">Single</option><option value="Married">Married</option><option value="Widow/Widower">Widow/Widower</option><option value="Separated">Separated</option><option value="Divorced">Divorced</option>
              </select>
            </div>
            <div>
              <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-briefcase text-gray-500 mr-1"></i> Employment Status</label>
              <select id="employment_status" name="employment_status" class="form-select" required>
                <option value="" disabled selected>Select Status</option><option value="Employed">Employed</option><option value="Unemployed">Unemployed</option><option value="Self-employed">Self-employed</option><option value="Student">Student</option><option value="Retired">Retired</option><option value="Homemaker">Homemaker</option><option value="Others">Others</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label for="nationality" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-flag text-gray-500 mr-1"></i> Nationality</label><input type="text" id="nationality" name="nationality" class="form-input text-base" placeholder="Filipino" required /></div>
            <div><label for="religion" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-place-of-worship text-gray-500 mr-1"></i> Religion</label><input type="text" id="religion" name="religion" class="form-input text-base" placeholder="Catholic" /></div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="resident_type" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-id-card text-gray-500 mr-1"></i> Resident Type</label>
                <select id="resident_type" name="resident_type" class="form-select" required>
                  <option value="" disabled selected>Select Type</option><option value="Permanent">Permanent</option><option value="Temporary">Temporary</option><option value="Voter">Voter</option><option value="Non-Voter">Non-Voter</option>
                </select>
              </div>
              <div>
                <label for="length_of_stay" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-clock text-gray-500 mr-1"></i> Length of Stay</label>
                <input type="text" id="length_of_stay" name="length_of_stay" class="form-input text-base" placeholder="e.g., 5 years" required />
              </div>
          </div>

          <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-home text-gray-500 mr-1"></i> Present Address</label>
            <textarea id="address" name="address" class="form-input text-base" rows="2" placeholder="House No., Street, Barangay, City" required></textarea>
          </div>
          
          <div class="form-section grid grid-cols-1 md:grid-cols-2 gap-x-4">
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-lock text-gray-500 mr-1"></i> Password</label>
              <div class="input-group relative">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" id="password" name="password" class="form-input has-icon text-base pr-10" placeholder="••••••••" required />
                <button type="button" id="togglePassword" class="absolute right-3 top-2.5 text-gray-500 focus:outline-none"><i class="fas fa-eye" id="eyeIcon"></i></button>
              </div>
              <ul id="password-strength-requirements">
                <li id="req-length" class="strength-req"><i class="fas fa-times-circle mr-1"></i> At least 8 characters</li>
                <li id="req-uppercase" class="strength-req"><i class="fas fa-times-circle mr-1"></i> At least one uppercase letter (A-Z)</li>
                <li id="req-lowercase" class="strength-req"><i class="fas fa-times-circle mr-1"></i> At least one lowercase letter (a-z)</li>
                <li id="req-number" class="strength-req"><i class="fas fa-times-circle mr-1"></i> At least one number (0-9)</li>
                <li id="req-special" class="strength-req"><i class="fas fa-times-circle mr-1"></i> At least one special character (!@#$%)</li>
              </ul>
            </div>
            <div>
              <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-lock text-gray-500 mr-1"></i> Confirm Password</label>
              <div class="input-group relative">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input has-icon text-base pr-10" placeholder="••••••••" required />
                <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-2.5 text-gray-500 focus:outline-none"><i class="fas fa-eye" id="confirmEyeIcon"></i></button>
              </div>
            </div>
          </div>
          
          <div class="form-section grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
            <div>
                <p class="text-sm text-gray-600 mb-2">Type the characters you see:</p>
                <div class="flex items-center gap-4">
                    <img src="captcha_image.php" alt="CAPTCHA Image" id="captcha_image" class="h-20">
                    <button type="button" id="refreshCaptchaBtn" class="text-xl p-2" title="Refresh CAPTCHA">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div>
                <label for="captcha_code" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-keyboard text-gray-500 mr-1"></i> Enter CAPTCHA Code
                </label>
                <input type="text" id="captcha_code" name="captcha_code" class="form-input tracking-widest text-lg font-mono" placeholder="Enter code" required autocomplete="off">
            </div>
          </div>

          <button type="submit" id="submitBtn" class="btn-primary w-full py-3 text-base sm:text-lg transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg">
            <i class="fas fa-user-plus mr-2"></i> Create Account
          </button>
        </form>

        <div class="text-center mt-6">
          <p class="text-gray-600 text-sm">
            Already have an account?
            <a href="login.php" class="link font-medium hover:underline"><i class="fas fa-sign-in-alt mr-1"></i> Sign in here</a>
          </p>
        </div>
      </div>
    </div>
</div>

<!-- Modals (Success and Error) - No changes needed -->
<div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center mx-4 modal-animate">
      <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
      <h2 class="text-2xl font-semibold mb-2">Account Created!</h2>
      <p class="text-gray-700 mb-4">Welcome to Barangay Bagbag! Redirecting...</p>
      <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12 mx-auto"></div>
    </div>
</div>
<div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
    <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 max-w-sm text-center mx-4 modal-animate">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-red-100">
            <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
        </div>
        <h2 class="text-xl sm:text-2xl font-semibold mb-2 text-gray-800">Oops!</h2>
        <p id="errorModalMessage" class="text-gray-600 mb-6"></p>
        <button id="closeErrorModalBtn" class="bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition-colors w-full">Try Again</button>
    </div>
</div>

<script>
// No changes needed in the JavaScript.
// It will work with the new HTML structure.
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');
    const successModal = document.getElementById('successModal');
    const registerForm = document.getElementById('registerForm');
    const errorModal = document.getElementById('errorModal');
    const errorModalMessage = document.getElementById('errorModalMessage');
    const closeErrorModalBtn = document.getElementById('closeErrorModalBtn');
    
    function showErrorModal(message) {
        errorModalMessage.textContent = message;
        errorModal.classList.remove('hidden');
    }
    closeErrorModalBtn.addEventListener('click', () => {
        errorModal.classList.add('hidden');
    });

    document.getElementById('reference_number').addEventListener('blur', function () {
        const refNumber = this.value.trim().toUpperCase();
        if (!refNumber || !/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/.test(refNumber)) return;
        const loading = document.getElementById('loadingSpinner');
        loading.classList.remove('hidden');
        fetch('http://localhost/SIA-ITE-BMS/RIS/ris_api.php?ref=' + refNumber, {
            headers: { 'X-API-Key': 'my-secret-barangay-api-key-123' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            return fetch('check_registered.php?email=' + encodeURIComponent(data.email))
            .then(res => res.json())
            .then(check => {
                if (check.registered) throw new Error("This account has already been created.");
                document.getElementById('fullname').value = data.full_name || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('dob').value = data.dob || '';
                document.getElementById('pob').value = data.pob || '';
                document.getElementById('gender').value = data.gender || '';
                document.getElementById('civil_status').value = data.civil_status || '';
                document.getElementById('nationality').value = data.nationality || '';
                document.getElementById('religion').value = data.religion || '';
                document.getElementById('address').value = data.address || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('resident_type').value = data.resident_type || '';
                document.getElementById('length_of_stay').value = data.stay_length || '';
                document.getElementById('employment_status').value = data.employment_status || '';
                const dobInput = document.getElementById('dob');
                if (dobInput.value) {
                    const dob = new Date(dobInput.value);
                    const today = new Date();
                    let age = today.getFullYear() - dob.getFullYear();
                    if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) age--;
                    document.getElementById('age').value = age >= 0 ? age : '';
                }
                loading.classList.add('hidden');
            });
        })
        .catch(err => {
            showErrorModal(err.message);
            loading.classList.add('hidden');
        });
    });
    
    document.getElementById('dob').addEventListener('change', function () {
        const dob = new Date(this.value); const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) age--;
        document.getElementById('age').value = age >= 0 ? age : '';
    });
    document.getElementById('togglePassword').addEventListener('click', () => {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        document.getElementById('eyeIcon').className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });
    document.getElementById('toggleConfirmPassword').addEventListener('click', () => {
        const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
        confirmPasswordInput.type = type;
        document.getElementById('confirmEyeIcon').className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    const passwordField = document.getElementById('password');
    const requirements = {
        length: document.getElementById('req-length'),
        uppercase: document.getElementById('req-uppercase'),
        lowercase: document.getElementById('req-lowercase'),
        number: document.getElementById('req-number'),
        special: document.getElementById('req-special')
    };
    const validateRequirement = (el, isValid) => {
        const icon = el.querySelector('i');
        if (isValid) {
            el.classList.add('valid');
            icon.className = 'fas fa-check-circle mr-1';
        } else {
            el.classList.remove('valid');
            icon.className = 'fas fa-times-circle mr-1';
        }
    };
    const isPasswordStrong = (password) => {
        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };
        return Object.values(checks).every(Boolean);
    };
    passwordField.addEventListener('input', () => {
        const password = passwordField.value;
        validateRequirement(requirements.length, password.length >= 8);
        validateRequirement(requirements.uppercase, /[A-Z]/.test(password));
        validateRequirement(requirements.lowercase, /[a-z]/.test(password));
        validateRequirement(requirements.number, /[0-9]/.test(password));
        validateRequirement(requirements.special, /[^A-Za-z0-9]/.test(password));
    });

    const refreshCaptchaBtn = document.getElementById('refreshCaptchaBtn');
    const captchaImage = document.getElementById('captcha_image');
    
    refreshCaptchaBtn.addEventListener('click', () => {
        captchaImage.src = 'captcha_image.php?' + new Date().getTime();
    });

    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = passwordInput.value;
        const confirm = confirmPasswordInput.value;
        const captchaCode = document.getElementById('captcha_code').value;

        if (!isPasswordStrong(password)) {
            showErrorModal("Your password does not meet all the security requirements.");
            return;
        }
        if (password !== confirm) {
            showErrorModal("Passwords do not match. Please re-enter them carefully.");
            return;
        }
        if (captchaCode.trim() === '') {
            showErrorModal("Please enter the CAPTCHA code.");
            return;
        }

        const formData = new FormData(registerForm);
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading" style="border-top-color: white; width: 20px; height: 20px; margin-right: 8px;"></span> Processing...';

        fetch('register_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successModal.classList.remove('hidden');
                setTimeout(() => {
                    window.location.href = 'login.php?registered=1';
                }, 2500);
            } else {
                showErrorModal(data.message);
                if (data.message.toLowerCase().includes('captcha')) {
                    refreshCaptchaBtn.click();
                    document.getElementById('captcha_code').value = '';
                }
            }
        })
        .catch(() => {
            showErrorModal("An unexpected error occurred. Please try again.");
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Create Account';
        });
    });
});
</script>

</body>
</html>

