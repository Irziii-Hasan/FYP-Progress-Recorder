<?php
session_start(); // Start PHP session

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user is a supervisor, otherwise redirect to unauthorized access page
if ($_SESSION['role'] !== 'supervisor') {
    header("Location: unauthorized.php");
    exit;
}

// Database connection
include 'config.php';

// Fetch the username from the faculty table based on faculty_id
$faculty_id = $_SESSION['faculty_id'];
$sql = "SELECT username FROM faculty WHERE faculty_id = '$faculty_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['username'] = $row['username']; // Save username in session
}

$conn->close();
?>
