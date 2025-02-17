

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
  .sidebar {
    height: 100vh; /* Full viewport height */
    overflow-y: auto; /* Add vertical scroll */
  }
</style>

</head>
<body>
<div class="sidebar" id="sidebar">
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcement</a>

  <a href="#studentSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-calendar-alt"></i> Meetings</a>
  <ul class="collapse list-unstyled" id="studentSubmenu">
    <li><a href="meetings.php"><i class="fas fa-calendar-check"></i> View Meetings</a></li>
    <li><a href="scheduleMeeting.php"><i class="fas fa-calendar-plus"></i> Set Meetings</a></li>
  </ul>

  <a href="project.php"><i class="fas fa-project-diagram"></i> View Projects</a>
  <a href="templates.php"><i class="fas fa-file-alt"></i> Templates</a>
  <a href="Remarks.php"><i class="fas fa-clipboard-check"></i> Internal Project Evaluation</a>
  <a href="visibleforms.php"><i class="fas fa-edit"></i> My Project Evaluation</a>
  <a href="viewresult.php"><i class="fas fa-chart-line"></i> Result</a>
</div>

</body>
</html>


<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Initialize Bootstrap dropdown
  $(document).ready(function(){
    $('.dropdown-toggle').dropdown();
  });
</script>

<script>
  function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var content = document.getElementById('content');
    sidebar.classList.toggle('show');
    if (sidebar.classList.contains('show')) {
      content.style.marginLeft = '250px';
    } else {
      content.style.marginLeft = '0';
    }
  }
</script>
