<?php
include 'session_student.php'; // Include session management
include 'config.php'; // Include database configuration

$student_id = $_SESSION['student_id']; // Get the logged-in student's ID

// Fetch ongoing course durations
$current_date = date('Y-m-d');
$query = "SELECT title, start_date, end_date FROM course_durations WHERE '$current_date' BETWEEN start_date AND end_date ORDER BY start_date DESC";
$result = mysqli_query($conn, $query);

// Fetch student's published result along with the corresponding title from result_detail
$sql_result = "
    SELECT s.total_marks, s.gpa, s.grade, r.title 
    FROM student_grand_totals s
    JOIN result_detail r ON s.result_id = r.result_id 
    WHERE s.student_id = '$student_id' AND s.Audience_Type = 'Student'
";
$result_data = mysqli_query($conn, $sql_result);
$has_result = mysqli_num_rows($result_data) > 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JUW - FYP Progress Recorder | Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <!-- Custom styles -->
  <link rel="stylesheet" href="style.css">
  <style>
    /* Hover effect for dashboard buttons */
    .dashbtn {
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .dashbtn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-custom {
      width: 100%;
      padding: 10px;
      text-align: center;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    /* Styling for the course duration section */
    .course-duration {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      margin-bottom: 10px;
      padding: 15px;
      border-radius: 5px;
    }

    .course-duration h5 {
      font-size: 1.2em;
      font-weight: bold;
    }

    .course-duration p {
      margin: 0;
      font-size: 0.9em;
    }

    .heading {
      color: #0a4a91;
      font-weight: 700;
    }

    /* Result card styling */
    .result-card {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      margin-bottom: 10px;
      padding: 15px;
      border-radius: 5px;

    }

    .result-card h5 {
      font-size: 1.2em;
      font-weight: bold;
    }

    .result-card p {
      margin: 0;
      font-size: 0.9em;    }

    .grade {
      font-size: 1.5em;
      font-weight: bold;
      color: #28a745;
    }

    .no-result {
      text-align: center;
      color: #dc3545;
      font-weight: bold;
      margin-top: 20px;
    }

    .activity{
      background-color: #f8f9fa;
      border-radius: 10px;
      border: 1px solid #dee2e6;
    }
  </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="wrapper">
  <?php include 'sidebar.php'; ?>
  <div class="container-fluid" id="content">
    <div class="row">
      <div class="col-md-12">
        <!-- BREADCRUMBS -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb"> 
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
        </nav>

        <!-- Pages Heading -->
        <h2 class="heading" style="text-align: center;">Dashboard</h2>
        <div class="container-fluid mt-4">
          <div class="row">
          <div class="col-md-12  mt-2 mb-2 pt-2 pb-2 activity">
              <div><h3>More Activities</h3></div>
              <div class="row dashbtnparent row-cols-2 row-cols-sm-3 row-cols-md-4">
                <button class="dashbtn" onclick="window.location.href='viewAnnoucement.php'"><div class="col"><img src="Icons/announcement.png" alt="Icon 1"><br>View Announcement</div></button>
                <button class="dashbtn" onclick="window.location.href='viewmeetings.php'"><div class="col"><img src="Icons/meeting.png" alt="Icon 1"><br>View Meetings</div></button>
                <button class="dashbtn" onclick="window.location.href='Assignments.php'"><div class="col"><img src="Icons/Submit.png" alt="submission icon"><br>Submit Assignments</div></button>              
                <button class="dashbtn" onclick="window.location.href='viewTemplates.php'"><div class="col"><img src="Icons/template.png" alt="Icon 2"><br>View Templates</div></button>
                <button class="dashbtn" onclick="window.location.href='gallery.php'"><div class="col"><img src="Icons/gallery.png" alt="Icon 2"><br>Gallery</div></button>
                <button class="dashbtn" onclick="window.location.href='viewresult.php'"><div class="col"><img src="Icons/evaluation.png" alt="Icon 2"><br>View Result</div></button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
