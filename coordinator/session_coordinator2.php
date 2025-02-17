<?php
session_start(); // Start PHP session

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['juw_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user is a coordinator, otherwise redirect to unauthorized access page
if ($_SESSION['role'] !== 'coordinator') {
    header("Location: unauthorized.php");
    exit;
}

?>