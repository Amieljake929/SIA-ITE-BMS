<?php
// Optional: fallback error message (para sa non-JS users)
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
  <title>Resident Login | Barangay Bagbag</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      padding-top: 4rem;
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
    }
    .form-input {
      border: 1px solid #ced4da;
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 1rem;
      width: 100%;
      transition: border 0.3s ease;
    }
    .form-input:focus {
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
    }
    .link {
      color: #3a9d6a;
      font-weight: 500;
    }
    .link:hover {
      text-decoration: underline;
      color: #2d7c4a;
    }
    .badge-resident {
      background: linear-gradient(to right, #d1f0e1, #e8f5e8);
      border: 1px solid #3a9d6a;
      color: #1f7042;
      padding: 4px 12px;
      border-radius: 50px;
      font-size: 0.875rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
    }
    .illustration {
      max-width: 200px;
      margin: 0 auto;
      opacity: 0.9;
    }
    @media (max-width: 1024px) {
      .lg\:flex {
        flex-direction: column;
      }
      .lg\:w-1\/2 {
        width: 100%;
      }
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
      animation: fadeIn 0.6s ease-out;
    }
    .loader {
      border-top-color: #3a9d6a;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center px-4 py-10 sm:py-12">

  <!-- Back to Home Link -->
  <!-- Top Navbar -->
<nav class="fixed top-0 left-0 right-0 bg-[#3a9d6a] shadow-md z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">
      <!-- Logo (on the left) -->
      <div class="flex-shrink-0 flex items-center justify-center">
        <img src="../images/Bagbag.png" alt="Barangay Logo" class="w-24 h-24 md:w-32 md:h-32 transform translate-y-20 mb-20">
                <span class="ml-2 text-white font-medium">BagbagCare - Resident Portal</span>

      </div>

      <!-- Back to Website Link (on the right) -->
      <div class="flex-shrink-0 ml-auto">
        <a href="website2.php" class="text-white hover:text-green-100 text-sm font-medium flex items-center">
          <i class="fas fa-arrow-left mr-2"></i> Back to Website
        </a>
      </div>
    </div>
  </div>
</nav>

  <!-- Login Card -->
  <div class="card w-full max-w-4xl mx-auto overflow-hidden bg-white fade-in">
    <div class="flex flex-col lg:flex-row">

      <!-- Left Side: Branding with Resident Badge -->
      <div class="lg:w-1/2 p-8 md:p-10 bg-gradient-to-br from-green-50 to-emerald-50 flex flex-col justify-center">
        <div class="icon-circle mb-5">
          <img src="../images/Bagbag.png" alt="Barangay Logo" class="w-12 h-12">
        </div>
        <span class="badge-resident mb-4 justify-center">
          <i class="fas fa-user-shield mr-1.5"></i> For Residents Only
        </span>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 text-center">
          Welcome to Barangay Bagbag Services
        </h2>
        <p class="text-gray-600 text-center leading-relaxed text-sm sm:text-base">
          Access community programs, submit requests, and stay connected with your barangay — all in one place.
        </p>

        <!-- Resident Illustration -->
        <div class="illustration mt-8 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3a9d6a" stroke-width="1.5" class="w-full h-auto">
            <circle cx="12" cy="8" r="5"/>
            <path d="M12 13a8 8 0 0 0 8 8h-16a8 8 0 0 0 8-8z"/>
            <path d="M8 21v-3a4 4 0 0 1 8 0v3"/>
          </svg>
        </div>
      </div>

      <!-- Right Side: Login Form -->
      <div class="lg:w-1/2 p-8 md:p-10 flex flex-col justify-center">
        <div class="text-center mb-6">
          <div class="icon-circle mx-auto mb-4" style="width: 50px; height: 50px;">
            <i class="fas fa-sign-in-alt text-green-700"></i>
          </div>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Resident Sign In</h1>
          <p class="text-gray-600 text-xs sm:text-sm mt-2">Secure access to your personal account</p>
        </div>

        <!-- Error Container (AJAX) -->
        <div id="errorContainer" class="hidden mb-6 p-4 bg-red-600 text-white rounded-lg shadow text-sm text-center">
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="space-y-5" novalidate>
          <!-- Email -->
          <div class="mb-5">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-envelope text-gray-500 mr-1"></i> Email Address
            </label>
            <input
              type="email"
              id="email"
              name="email"
              class="form-input text-base"
              placeholder="you@example.com"
              required
            />
          </div>

          <!-- Password -->
          <div class="mb-5">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-lock text-gray-500 mr-1"></i> Password
            </label>
            <div class="relative">
              <input
                type="password"
                id="password"
                name="password"
                class="form-input text-base pr-10"
                placeholder="••••••••"
                required
              />
              <button type="button" id="togglePassword" class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                <i class="fas fa-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>

          <!-- Forgot Password -->
          

          <!-- Sign In Button -->
          <button type="submit" class="btn-primary w-full py-3 text-base sm:text-lg transition duration-300 flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
          </button>
        </form>

        <!-- Create Account Link -->
        <div class="text-center mt-6">
          <p class="text-gray-600 text-sm">
            Don’t have an account?
            <a href="register.php" class="link font-medium hover:underline">
              <i class="fas fa-user-plus mr-1"></i> Register Now
            </a>
          </p>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-8 text-xs text-gray-500">
          <p>🔒 Secure login • Encrypted connection</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" aria-hidden="true" role="dialog" aria-modal="true" tabindex="-1" class="hidden">
    <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 animate-fade-in">
      <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center mx-4">
        <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <h2 class="text-2xl font-semibold mb-2">Login Successful!</h2>
        <p class="text-gray-700 mb-4">Welcome back! Redirecting to your dashboard...</p>
        <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12 mx-auto"></div>
      </div>
    </div>
  </div>

  <script>
    const loginForm = document.getElementById('loginForm');
    const errorContainer = document.getElementById('errorContainer');
    const successModal = document.getElementById('successModal');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    // Toggle Password Visibility
    togglePassword.addEventListener('click', () => {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      eyeIcon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    // Handle Form Submission
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      errorContainer.classList.add('hidden');
      errorContainer.textContent = '';

      const formData = new FormData(loginForm);

      fetch('login_process.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          successModal.classList.remove('hidden');
          setTimeout(() => {
            window.location.href = data.redirect_url;
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