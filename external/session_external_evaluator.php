<?php
session_start(); // Start PHP session

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['external_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user is an external member, otherwise redirect to unauthorized access page
if ($_SESSION['role'] !== 'external') {
    header("Location: unauthorized.php");
    exit;
}

// Database connection
include 'config.php';

// Fetch the username from the faculty table based on external_id
$external_id = $_SESSION['external_id'];
$sql = "SELECT name FROM external WHERE external_id = '$external_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['username'] = $row['name']; // Save username in session
}

$conn->close();
?>
