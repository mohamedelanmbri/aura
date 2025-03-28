<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: ../login.php'); // Adjust the path if needed
    exit();
}

$userRole = $_SESSION['user']['role'];

// Define the current page
$currentPage = basename($_SERVER['PHP_SELF']);

// Redirect based on role and current page
if ($userRole === 'user' && ($currentPage === 'admin_profile.php' || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false)) {
    header('Location: ../profile.php'); // Adjust the path if needed
    exit();
} elseif ($userRole === 'admin' && $currentPage === 'profile.php') {
    header('Location: ../admin_profile.php'); // Adjust the path if needed
    exit();
}
?>
