<?php
session_start(); // Start PHP session

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user is a student, otherwise redirect to unauthorized access page
if ($_SESSION['role'] !== 'student') {
    header("Location: unauthorized.php");
    exit;
}


// Database connection
include 'config.php';

// Fetch the username from the faculty table based on student_id
$student_id = $_SESSION['student_id'];
$sql = "SELECT username FROM student WHERE student_id = '$student_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['username'] = $row['username']; // Save username in session
}

$conn->close();
?>
