<?php
// Include database connection
include 'config.php';

// Function to get username based on user type
function getUsername($userId) {
    global $conn;
    $sql = "SELECT username FROM student WHERE juw_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['username'] ?? 'Unknown User'; // Use null coalescing operator to handle no result
}

// Check if user is logged in and is a student, otherwise redirect to login page
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Retrieve student ID from session
$userId = $_SESSION['student_id'];

// Get username based on student ID
$username = getUsername($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JUW - FYP Progress Recorder | Dashboard</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: rgb(236, 236, 236);
      max-width: 100%;
      overflow-x: hidden;
    }
    .header {
      background-color: #051747;
      height: 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
    }
    .header-title {
      color: #fff;
      font-size: 24px;
      font-weight: bold;
      text-align: center;
    }
    .sidebar {
      position: fixed;
      top: 60px;
      left: -250px;
      height: calc(100% - 60px);
      width: 250px;
      background-color: #fff;
      transition: left 0.3s ease-in-out;
    }
    .sidebar.show {
      left: 0;
    }
.sidebar a {
  padding: 14px 14px;
  display: block;
  color: #051747;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  border-bottom: 1px solid #f9eeee;
}
    .sidebar a:first-child {
      border-top: 0.5px solid #ccc;
    }
    .sidebar a:hover {
      background-color: #f1f1f1;
    }
    .toggle-btn {
      cursor: pointer;
      color: #fff;
    }
    .user-name {
      color: #fff;
      font-weight: bold;
      font-size: 16px;
      position: relative;
      text-decoration: none;

    }
    .user-dropdown-icon {
      position: absolute;
      top: 50%;
      right: -20px;
      transform: translateY(-50%);
      color: #fff;
    }
    .recent-content  {
      color: #081F62;
      font-size: 25px;
      padding-left: 15px;
    }
    .btn-link:hover {
    color: #cdf1f5;
    text-decoration: none;
    }

    /* Transition for sidebar and content */
    .sidebar, #content {
      transition: left 0.3s ease-in-out, margin-left 0.3s ease-in-out;
    }
    .recent{
      border: 1px solid #ccc;
      box-shadow: 2px 3px 7px gray;
      border-radius: 10px;
      padding: 15px;
      margin: 15px 0px;
      background-color: #D9D9D9;
    }
    
    .fa-chart-line{
      color: #081F62;
      font-size: 27px;
    }
    
  .dropdown-item {
    color: #051747; /* Set text color */
    text-decoration: none; /* Remove underline from links */
  }

  .dropdown-item:hover {
    background-color: transparent; /* Remove background color change on hover */
    color: #051747; /* Ensure text color remains consistent */
  }

    @media (max-width: 768px) {
      .header-title {
        font-size: 20px; /* Decrease font size on smaller screens */
      }
      .sidebar {
        width: 200px;
      }
      .container-fluid {
        padding-left: 0; /* Remove left padding */
        padding-right: 0; /* Remove right padding */
      }
    }
    @media (min-width: 768px){
      .row-cols-md-4>* {
          -ms-flex: 0 0 25%;
          flex: 0 0 25%;
          max-width: 23%;
      }
    }

    .home-icon {
    color: white;
    }

  </style>
</head>
<body>

<div class="navbar header sticky-top">
  <div class="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars fa-2x"></i>
  </div>
  <div class="header-title d-flex align-items-center ">
    <img src="Icons/logo.png" alt="Logo" class="header-logo p-2" />
    <span>FYP Progress Recorder</span>
  </div>  <div class="user-info d-flex flex-direction-row align-items-center">
  <a href="dashboard.php" style="text-decoration: none; margin-right: 10px;">
            <i class="fas fa-home fa-lg home-icon"></i>
          </a>
  <?php if (isset($_SESSION['username'])): ?>
      <div class="dropdown">
        <button class="btn btn-link dropdown-toggle user-name" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php 
                echo 'Student: ' . $_SESSION['username'];
          ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <a class="dropdown-item" href="logout.php">Logout</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
