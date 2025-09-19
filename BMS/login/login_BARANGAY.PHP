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
  <title>Barangay Bagbag Login</title>
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
    @media (max-width: 1024px) {
      .lg\:flex {
        flex-direction: column;
      }
      .lg\:w-1\/2 {
        width: 100%;
      }
    }

    /* Modal Styles */
    #successModal {
      display: none;
      position: fixed;
      z-index: 50;
      left: 0; top: 0;
      width: 100%; height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
      backdrop-filter: blur(4px);
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
      <div class="flex-shrink-0 flex items-center justify-center" style="color: white;">
        <!-- Logo -->
         <img src="../images/Bagbag.png" alt="Barangay Logo" class="w-24 h-24 md:w-32 md:h-32 transform translate-y-20 mb-20">
        <!-- Text -->
        <span class="ml-2 text-white font-medium">BagbagCare - Officials Portal</span>
      </div>
    </div>
  </div>
</nav>

  <!-- Login Card -->
  <div class="card w-full max-w-4xl mx-auto overflow-hidden bg-white">
    <div class="flex flex-col lg:flex-row">

      <!-- Left Side: Branding -->
      <div class="lg:w-1/2 p-8 md:p-10 bg-gradient-to-br from-green-50 to-yellow-50 flex flex-col justify-center">
        <div class="icon-circle mb-5">
          <img src="../images/Bagbag.png" alt="Barangay Logo" class="w-12 h-12">
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 text-center">
          Welcome to Barangay Bagbag Services
        </h2>
        <p class="text-gray-600 text-center leading-relaxed text-sm sm:text-base">
          Access community programs, submit requests, and stay connected with your barangay — all in one place.
        </p>
        <div class="mt-8 text-center opacity-10">
          <i class="fas fa-shield-alt text-6xl text-green-600"></i>
        </div>
      </div>

      <!-- Right Side: Login Form -->
      <div class="lg:w-1/2 p-8 md:p-10 flex flex-col justify-center">
        <div class="text-center mb-6">
          <div class="icon-circle mx-auto mb-4" style="width: 50px; height: 50px;">
            <img src="../images/Bagbag.png" alt="Barangay Logo" class="w-8 h-8">
          </div>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Sign In to Your Account</h1>
          <p class="text-gray-600 text-xs sm:text-sm mt-2">Please enter your credentials to continue</p>
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
          <div class="flex justify-end mb-6">
            <a href="forgot_password.php" class="text-sm link hover:underline flex items-center">
              <i class="fas fa-question-circle mr-1"></i> Forgot password?
            </a>
          </div>

          <!-- Sign In Button -->
          <button type="submit" class="btn-primary w-full py-3 text-base sm:text-lg transition duration-300 flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
          </button>
        </form>

        <!-- Create Account Link -->
        

        <!-- Footer Note -->
        <div class="text-center mt-8 text-xs text-gray-500">
          <p>Secure login • Encrypted connection</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" aria-hidden="true" role="dialog" aria-modal="true" tabindex="-1">
    <div class="flex items-center justify-center min-h-screen">
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

      fetch('login_process_BARANGAY.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          successModal.style.display = 'block';
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