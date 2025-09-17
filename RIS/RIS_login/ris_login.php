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
  <title>RIS Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      color: #212529;
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
      @apply bg-white rounded-xl shadow-lg overflow-hidden;
    }
    .link {
      @apply text-green-600 hover:text-green-800 font-medium;
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
  <a href="#" class="absolute top-4 left-4 text-green-700 hover:text-green-900 text-sm flex items-center z-10">
    <i class="fas fa-arrow-left mr-1"></i> Back to Website
  </a>

  <!-- Login Card -->
  <div class="card w-full max-w-md mx-auto">
    <div class="p-8 sm:p-10">

      <!-- Logo & Title -->
      <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 border-2 border-green-600 rounded-full mb-4">
          <img src="../images/Bagbag.png" alt="Barangay Logo" class="w-8 h-8">
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Admin Sign In</h1>
        <p class="text-gray-600 text-sm mt-1">Enter your credentials to access the dashboard</p>
      </div>

      <!-- Error Container (AJAX) -->
      <div id="errorContainer" class="hidden mb-6 p-3 bg-red-500 text-white text-sm rounded-lg text-center">
      </div>

      <!-- Login Form -->
      <form id="loginForm" class="space-y-5" novalidate>
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            <i class="fas fa-envelope text-gray-500 mr-1"></i> Email Address
          </label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="admin@barangay.gov.ph"
            required
          />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            <i class="fas fa-lock text-gray-500 mr-1"></i> Password
          </label>
          <div class="relative">
            <input
              type="password"
              id="password"
              name="password"
              class="form-input pr-10"
              placeholder="••••••••"
              required
            />
            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <!-- Forgot Password -->
        <div class="flex justify-end">
          <a href="forgot_password.php" class="text-sm link flex items-center hover:underline">
            <i class="fas fa-question-circle mr-1"></i> Forgot password?
          </a>
        </div>

        <!-- Sign In Button -->
        <button type="submit" class="btn-primary w-full py-3 text-base sm:text-lg transition duration-300 flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
          </button>
      </form>

      <!-- Footer -->
      <div class="text-center mt-6 text-xs text-gray-500">
        <p>Secure login • Encrypted connection</p>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center mx-4">
      <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
      </svg>
      <h2 class="text-xl font-semibold mb-2">Login Successful!</h2>
      <p class="text-gray-700 mb-4">Welcome back! Redirecting...</p>
      <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-10 w-10 mx-auto"></div>
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

      fetch('ris_login_process.php', {
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