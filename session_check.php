<?php
session_start();

if (!isset($_SESSION['user'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: login.php');
    exit();
}

// Check if 'role' key exists before accessing it
$userRole = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;

// Define the current page
$currentPage = basename($_SERVER['PHP_SELF']);

// Redirect based on role and current page
if ($userRole === 'user' && $currentPage === 'admin_profile.php') {
    header('Location: profile.php');
    exit();
} elseif ($userRole === 'admin' && $currentPage === 'profile.php') {
    header('Location: admin_profile.php');
    exit();
}
?>
