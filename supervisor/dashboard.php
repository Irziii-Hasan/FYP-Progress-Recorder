<?php include 'session_supervisor.php'; ?> 
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
        <h1 class="heading" style="text-align: center;">Dashboard</h1>
        <div class="container-fluid mt-4 ">
          <div class="row">
            <div class="col-md-12">
              <div><h3>Activities</h3></div>
              <div class="row dashbtnparent row-cols-2 row-cols-sm-3 row-cols-md-4">
                <button class="dashbtn" onclick="window.location.href='announcement.php'"><div class="col"><img src="Icons/announcement.png" alt="Icon 1"><br>Announcement</div></button>
                <button class="dashbtn" onclick="window.location.href='meetings.php'"><div class="col"><img src="Icons/meeting.png" alt="Icon 1"><br>Meetings</div></button>
                <button class="dashbtn" onclick="window.location.href='project.php'"><div class="col"><img src="Icons/project.png" alt="Icon 2"><br>View Projects</div></button>
                <button class="dashbtn" onclick="window.location.href='templates.php'"><div class="col"><img src="Icons/template.png" alt="Icon 2"><br>Templates</div></button>
                <button class="dashbtn" onclick="window.location.href='remarks.php'"><div class="col"><img src="Icons/remarks.png" alt="Icon 2"><br>Internal Project Evaluation</div></button>
                <button class="dashbtn" onclick="window.location.href='visibleforms.php'"><div class="col"><img src="Icons/forms.png" alt="Icon 2"><br>My Project Evaluation</div></button>
                <button class="dashbtn" onclick="window.location.href='viewresult.php'"><div class="col"><img src="Icons/results.png" alt="Icon 2"><br>Result</div></button>
              </div>
            </div>
<!--             -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
