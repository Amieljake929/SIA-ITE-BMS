<!-- success.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Success</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-md text-center max-w-md">
    <h2 class="text-2xl font-bold text-green-700 mb-4">Success!</h2>
    <p class="text-gray-700">Your Barangay Clearance request has been submitted.</p>
    <a href="dashboard.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Back to Dashboard</a>
  </div>
</body>
</html>