<?php
session_start();
session_unset();     // Clear all session variables
session_destroy();   // Destroy the session
header("Location: login_admin_and_officials.php"); // Redirect to login page
exit();
?>
