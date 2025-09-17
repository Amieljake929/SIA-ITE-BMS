<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';
include '../../phpmailer_config.php'; // Include PHPMailer config

// Get report ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid report ID.");
}
$id = (int)$_GET['id'];

// Fetch report details (including complainant_contact)
$stmt = $conn->prepare("SELECT id, status, complainant_contact FROM blotter_and_reports WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Report not found.");
}
$report = $result->fetch_assoc();
$stmt->close();

// Fetch all BPSOs (with user_id and full_name)
$stmt = $conn->prepare("SELECT id, full_name FROM users WHERE role = 'BPSO' ORDER BY full_name ASC");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->execute();
$bpso_result = $stmt->get_result();

if ($bpso_result->num_rows === 0) {
    $bpsos = [];
} else {
    $bpsos = $bpso_result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['bpso_id']) || !is_numeric($_POST['bpso_id'])) {
        $error = "Please select a valid BPSO.";
    } else {
        $bpso_id = (int)$_POST['bpso_id'];

        // Update report: assign to BPSO and change status
        $stmt = $conn->prepare("UPDATE blotter_and_reports SET status = 'Assigned', assigned_to_bpso_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $bpso_id, $id);

        if ($stmt->execute()) {
            // Fetch complainant's email/contact and BPSO name
            $stmt_fetch = $conn->prepare("
                SELECT br.complainant_contact, u.full_name AS bpso_name 
                FROM blotter_and_reports br 
                JOIN users u ON u.id = ? 
                WHERE br.id = ?
            ");
            $stmt_fetch->bind_param("ii", $bpso_id, $id);
            $stmt_fetch->execute();
            $result_fetch = $stmt_fetch->get_result();
            $updated_report = $result_fetch->fetch_assoc();
            $stmt_fetch->close();

            // Send email notification if contact is available
            if ($updated_report && !empty($updated_report['complainant_contact'])) {
                $toEmail = $updated_report['complainant_contact'];
                $subject = "Your Blotter Report Has Been Assigned!";
                
                $bpsoName = htmlspecialchars($updated_report['bpso_name'] ?? 'a Barangay Officer');
                $body = "
                <h2 style='color: green;'>Your Blotter Report Has Been Assigned!</h2>
                <p>Hello,</p>
                <p>We are pleased to inform you that your blotter report has been successfully assigned to <strong>{$bpsoName}</strong>.</p>
                <p><strong>Report ID:</strong> #{$id}</p>
                <p>Please expect further communication from the assigned officer regarding your case.</p>
                <p>Thank you for choosing BagbagCare.</p>
                ";

                // Send email using PHPMailer
                sendNotificationEmail($toEmail, $subject, $body);
            }

            $stmt->close();
            $conn->close();
            
            // Redirect with success
            header("Location: S.blotter_reports.php?assigned=1");
            exit();
        } else {
            $error = "Failed to assign. Please try again.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Assign BPSO - BagbagCare</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <!-- Top Bar -->
    <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
        <div class="flex-1">
            <span id="datetime" class="font-medium tracking-wide">LOADING DATE...</span>
        </div>
        <div class="flex-shrink-0">
            <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4">
        <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
        <!-- Home Icon Button and Title -->
        <div class="flex items-center space-x-4">
            <!-- Home Icon Button -->
            <button 
                class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../staff_dashboard.php'"
                title="Home"
            >
                <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
            </button>

            <h1 class="text-xl font-bold text-green-800">Assign BPSO to Report #<?= $id ?></h1>

        </div>

            <!-- User Info -->
            <div class="text-sm text-gray-600">
                Logged in as: <span class="font-medium text-blue-700"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow px-6 py-8">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">

            <h2 class="text-2xl font-bold text-green-800 mb-6">Assign to BPSO</h2>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                    <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <!-- Assignment Form -->
            <form method="POST">
                <div class="mb-6">
                    <label for="bpso_id" class="block text-sm font-medium text-gray-700 mb-2">Select BPSO</label>
                    <?php if (empty($bpsos)): ?>
                        <p class="text-red-600 text-sm">No BPSO found in the system.</p>
                    <?php else: ?>
                        <select name="bpso_id" id="bpso_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">-- Select BPSO --</option>
                            <?php foreach ($bpsos as $b): ?>
                                <option value="<?= htmlspecialchars($b['id']) ?>">
                                    <?= htmlspecialchars($b['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
                        <i class="fas fa-check mr-2"></i> Assign Report
                    </button>
                    <a href="S.blotter_reports.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </form>

            <!-- Report Info (Optional) -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <p><strong>Status:</strong> <span class="font-medium"><?= htmlspecialchars($report['status']) ?></span></p>
                <p><strong>Report ID:</strong> <?= $id ?></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
        &copy; <?= date('Y') ?> BagbagCare. All rights reserved. | Empowering Communities Digitally.
    </footer>

    <!-- JavaScript -->
    <script>
        // Update time
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
    </script>
</body>
</html>