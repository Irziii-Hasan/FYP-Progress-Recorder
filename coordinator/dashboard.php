<?php include 'session_coordinator.php'; ?>
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

    .activity{
      background-color: #f8f9fa;
      border-radius: 10px;
      border: 1px solid #dee2e6;
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
          <li class="breadcrumb-item"><a href="#">JUW - FYP Progress Recorder</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
      </nav>

      <!-- Pages Heading -->
      <h2 class="heading" style="text-align: center;">Dashboard</h2>
      <div class="container-fluid mt-4">
        <div class="row">
          <div class="col-md-12  mt-2 mb-2 pt-2 pb-2 activity">

            <div><h3>Activities</h3></div>

            <div class="row dashbtnparent row-cols-2 row-cols-sm-3 row-cols-md-4">
              <button class="dashbtn" onclick="window.location.href='viewAnnouncement.php'"><div class="col"><img src="Icons/announcement.png" alt="Icon 1"><br>Announcement</div></button>
              <button class="dashbtn" onclick="window.location.href='forms.php'"><div class="col"><img src="Icons/forms.png" alt="Icon 1"><br>Forms</div></button>
              <button class="dashbtn" onclick="window.location.href='progress.php'"><div class="col"><img src="Icons/progress.png" alt="Icon 2"><br>Project Progress</div></button>
              <button class="dashbtn" onclick="window.location.href='view_schedule.php'"><div class="col"><img src="Icons/pp.png" alt="Icon 3"><br>Presentations</div></button>
              <button class="dashbtn" onclick="window.location.href='project.php'"><div class="col"><img src="Icons/status board.png" alt="Icon 3"><br>Projects</div></button>
              <button class="dashbtn" onclick="window.location.href='Templates.php'"><div class="col"><img src="Icons/upload-file.png" alt="Icon 3"><br>Templates</div></button>
              <button class="dashbtn" onclick="window.location.href='viewportal.php'"><div class="col"><img src="Icons/portal.png" alt="Icon 1"><br>Assignments</div></button>
              <button class="dashbtn" onclick="window.location.href='gallery.php'"><div class="col"><img src="Icons/gallery.png" alt="Icon 2"><br>Gallery</div></button>
              <button class="dashbtn" onclick="window.location.href='event.php'"><div class="col"><img src="Icons/events.png" alt="Icon 2"><br>Event</div></button>
              <button class="dashbtn" onclick="window.location.href='viewResult.php'"><div class="col"><img src="Icons/evaluation.png" alt="Icon 2"><br>Result</div></button>
              <button class="dashbtn" onclick="window.location.href='viewduration.php'"><div class="col"><img src="Icons/duration.png" alt="Icon 2"><br>Duration</div></button>
            </div>
          </div>
          
          
        </div>
      </div>
    </div>
  </div>
</div>
</div>

</body>
</html>
