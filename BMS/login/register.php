<?php
// Optional: fallback error message
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
    body {
      background-color: #f8f9fa;
      color: #212529;
      font-size: 16px;
      line-height: 1.7;
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233a9d6a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .btn-primary {
      background-color: #3a9d6a;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1rem;
    }
    .btn-primary:hover {
      background-color: #2d7c4a;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(58, 157, 106, 0.2);
    }
    .form-input, .form-select {
      border: 1px solid #ced4da;
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 1rem;
      width: 100%;
      transition: all 0.3s ease;
    }
    .form-input:focus, .form-select:focus {
      border-color: #3a9d6a;
      box-shadow: 0 0 0 3px rgba(58, 157, 106, 0.2);
      outline: none;
    }
    .card {
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: white;
    }
    .icon-circle {
      background: linear-gradient(135deg, #e8f5e8, #f0f9f0);
      border: 2px solid #3a9d6a;
      border-radius: 50%;
      width: 70px;
      height: 70px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px auto;
      animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
    .link {
      color: #3a9d6a;
      font-weight: 500;
    }
    .link:hover {
      text-decoration: underline;
      color: #2d7c4a;
    }
    .input-group {
      position: relative;
    }
    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }
    .form-input.has-icon {
      padding-left: 40px;
    }
    .form-section {
      border-top: 1px solid #e5e7eb;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
    }
    .text-muted {
      font-size: 0.875rem;
      color: #6b7280;
    }
    @media (max-width: 1024px) {
      .lg\:flex {
        flex-direction: column;
      }
      .lg\:w-1\/2 {
        width: 100%;
      }
    }
  </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center px-4 py-10 sm:py-12">

  <!-- Back to Home Link -->
  <a href="#" class="absolute top-4 left-4 text-green-700 hover:text-green-900 text-sm flex items-center z-10">
    <i class="fas fa-arrow-left mr-1"></i> Back to Website
  </a>

  <!-- Register Card -->
  <div class="card w-full max-w-5xl mx-auto overflow-hidden bg-white">
    <div class="flex flex-col lg:flex-row">

      <!-- Left Side: Branding & Welcome -->
      <div class="lg:w-1/2 p-8 md:p-10 bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 flex flex-col justify-center">
        <div class="icon-circle mb-5 animate-pulse">
          <i class="fas fa-home text-3xl text-green-700"></i>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 text-center">
          Welcome to Barangay Bagbag!
        </h2>
        <p class="text-gray-600 text-center leading-relaxed text-sm sm:text-base mb-6">
          Join our community and gain access to services, announcements, and support — all in one secure place.
        </p>
        <div class="bg-white/70 rounded-xl p-5 shadow-sm border border-green-100">
          <ul class="space-y-3 text-sm text-gray-700">
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Free access to services</li>
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Secure personal account</li>
            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Real-time updates</li>
          </ul>
        </div>
      </div>

      <!-- Right Side: Enhanced Registration Form -->
      <div class="lg:w-1/2 p-8 md:p-10 flex flex-col justify-center">
        <div class="text-center mb-6">
          <div class="icon-circle mx-auto mb-4" style="width: 50px; height: 50px;">
            <i class="fas fa-user-plus text-lg text-green-700"></i>
          </div>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Create Your Resident Account</h1>
          <p class="text-gray-600 text-xs sm:text-sm mt-2">Please fill in your complete information</p>
        </div>

        <!-- Error Container -->
        <div id="errorContainer" class="hidden mb-6 p-4 bg-red-600 text-white rounded-lg shadow text-sm text-center">
        </div>

        <!-- Registration Form -->
        <form id="registerForm" class="space-y-5" novalidate>
          <!-- Full Name -->
          <div class="mb-5">
            <label for="fullname" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-user text-gray-500 mr-1"></i> Full Name
            </label>
            <div class="input-group">
              <i class="fas fa-user input-icon"></i>
              <input
                type="text"
                id="fullname"
                name="fullname"
                class="form-input has-icon text-base"
                placeholder="Juan Dela Cruz"
                required
              />
            </div>
          </div>

          <!-- Email -->
          <div class="mb-5">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-envelope text-gray-500 mr-1"></i> Email Address
            </label>
            <div class="input-group">
              <i class="fas fa-envelope input-icon"></i>
              <input
                type="email"
                id="email"
                name="email"
                class="form-input has-icon text-base"
                placeholder="you@example.com"
                required
              />
            </div>
          </div>

          <!-- Phone Number -->
          <div class="mb-5">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-phone text-gray-500 mr-1"></i> Mobile Number
            </label>
            <div class="input-group">
              <i class="fas fa-phone input-icon"></i>
              <input
                type="tel"
                id="phone"
                name="phone"
                class="form-input has-icon text-base"
                placeholder="+63 912 345 6789"
                required
              />
            </div>
          </div>

          <!-- Date of Birth & Age -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
              <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar-alt text-gray-500 mr-1"></i> Date of Birth
              </label>
              <input
                type="date"
                id="dob"
                name="dob"
                class="form-input text-base"
                required
              />
            </div>
            <div>
              <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age</label>
              <input
                type="number"
                id="age"
                name="age"
                class="form-input text-base"
                placeholder="Auto-calculated"
                readonly
              />
            </div>
          </div>

          <!-- Place of Birth & Gender -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
              <label for="pob" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-map-marker-alt text-gray-500 mr-1"></i> Place of Birth
              </label>
              <input
                type="text"
                id="pob"
                name="pob"
                class="form-input text-base"
                placeholder="Manila, Philippines"
                required
              />
            </div>
            <div>
              <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-venus-mars text-gray-500 mr-1"></i> Gender
              </label>
              <select id="gender" name="gender" class="form-select" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>

          <!-- Civil Status & Employment Status -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
              <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-ring text-gray-500 mr-1"></i> Civil Status
              </label>
              <select id="civil_status" name="civil_status" class="form-select" required>
                <option value="" disabled selected>Select Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
                <option value="Divorced">Divorced</option>
              </select>
            </div>
            <div>
              <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-briefcase text-gray-500 mr-1"></i> Employment Status
              </label>
              <select id="employment_status" name="employment_status" class="form-select" required>
                <option value="" disabled selected>Select Status</option>
                <option value="Employed">Employed</option>
                <option value="Unemployed">Unemployed</option>
                <option value="Self-Employed">Self-Employed</option>
                <option value="Student">Student</option>
                <option value="Retired">Retired</option>
                <option value="OFW">OFW</option>
              </select>
            </div>
          </div>

          <!-- Nationality & Religion -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
              <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-flag text-gray-500 mr-1"></i> Nationality
              </label>
              <input
                type="text"
                id="nationality"
                name="nationality"
                class="form-input text-base"
                placeholder="Filipino"
                required
              />
            </div>
            <div>
              <label for="religion" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-place-of-worship text-gray-500 mr-1"></i> Religion
              </label>
              <input
                type="text"
                id="religion"
                name="religion"
                class="form-input text-base"
                placeholder="Catholic"
              />
            </div>
          </div>

          <!-- Present Address -->
          <div class="mb-5">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-home text-gray-500 mr-1"></i> Present Address
            </label>
            <textarea
              id="address"
              name="address"
              class="form-input text-base"
              rows="3"
              placeholder="House No., Street, Barangay, City"
              required
            ></textarea>
          </div>

          <!-- Resident Type & Length of Stay -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
              <label for="resident_type" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-id-card text-gray-500 mr-1"></i> Resident Type
              </label>
              <select id="resident_type" name="resident_type" class="form-select" required>
                <option value="" disabled selected>Select Type</option>
                <option value="Permanent">Permanent</option>
                <option value="Temporary">Temporary</option>
                <option value="Newly Registered">Newly Registered</option>
              </select>
            </div>
            <div>
              <label for="length_of_stay" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-clock text-gray-500 mr-1"></i> Length of Stay
              </label>
              <select id="length_of_stay" name="length_of_stay" class="form-select" required>
                <option value="" disabled selected>How long?</option>
                <option value="Less than 1 year">Less than 1 year</option>
                <option value="1-5 years">1-5 years</option>
                <option value="6-10 years">6-10 years</option>
                <option value="More than 10 years">More than 10 years</option>
              </select>
            </div>
          </div>

          <!-- Password Fields -->
          <div class="form-section">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Set Your Account Password</h3>
            <div class="mb-5">
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock text-gray-500 mr-1"></i> Password
              </label>
              <div class="input-group relative">
                <i class="fas fa-lock input-icon"></i>
                <input
                  type="password"
                  id="password"
                  name="password"
                  class="form-input has-icon text-base pr-10"
                  placeholder="••••••••"
                  required
                />
                <button type="button" id="togglePassword" class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                  <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
              </div>
            </div>

            <div class="mb-5">
              <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock text-gray-500 mr-1"></i> Confirm Password
              </label>
              <div class="input-group relative">
                <i class="fas fa-lock input-icon"></i>
                <input
                  type="password"
                  id="confirm_password"
                  name="confirm_password"
                  class="form-input has-icon text-base pr-10"
                  placeholder="••••••••"
                  required
                />
                <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                  <i class="fas fa-eye" id="confirmEyeIcon"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Register Button -->
          <button type="submit" class="btn-primary w-full py-3 text-base sm:text-lg transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg">
            <i class="fas fa-user-plus mr-2"></i> Create Account
          </button>
        </form>

        <!-- Already have an account? -->
        <div class="text-center mt-6">
          <p class="text-gray-600 text-sm">
            Already have an account?
            <a href="login.php" class="link font-medium hover:underline">
              <i class="fas fa-sign-in-alt mr-1"></i> Sign in here
            </a>
          </p>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-8 text-xs text-gray-500">
          <p>Your data is secure • We respect your privacy</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" aria-hidden="true" role="dialog" aria-modal="true" tabindex="-1" class="hidden">
    <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center mx-4 animate-fade-in">
        <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <h2 class="text-2xl font-semibold mb-2">Account Created!</h2>
        <p class="text-gray-700 mb-4">Welcome to Barangay Bagbag! Redirecting to login...</p>
        <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12 mx-auto"></div>
      </div>
    </div>
  </div>

  <script>
    // Auto-calculate Age from DOB
    document.getElementById('dob').addEventListener('change', function () {
      const dob = new Date(this.value);
      const today = new Date();
      let age = today.getFullYear() - dob.getFullYear();
      const monthDiff = today.getMonth() - dob.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
      }
      document.getElementById('age').value = age >= 0 ? age : '';
    });

    // Toggle Password Visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const confirmEyeIcon = document.getElementById('confirmEyeIcon');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      eyeIcon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    toggleConfirmPassword.addEventListener('click', () => {
      const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
      confirmPasswordInput.type = type;
      confirmEyeIcon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    // Handle Form Submission
    const registerForm = document.getElementById('registerForm');
    const errorContainer = document.getElementById('errorContainer');
    const successModal = document.getElementById('successModal');

    registerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      errorContainer.classList.add('hidden');
      errorContainer.textContent = '';

      const password = passwordInput.value;
      const confirm = confirmPasswordInput.value;

      if (password !== confirm) {
        errorContainer.textContent = "Passwords do not match.";
        errorContainer.classList.remove('hidden');
        return;
      }

      const formData = new FormData(registerForm);

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
          errorContainer.textContent = data.message;
          errorContainer.classList.remove('hidden');
        }
      })
      .catch(() => {
        errorContainer.textContent = "Something went wrong. Please try again.";
        errorContainer.classList.remove('hidden');
      });
    });
  </script>
</body>
</html>