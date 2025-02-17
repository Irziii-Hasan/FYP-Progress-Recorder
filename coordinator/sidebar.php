

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <title>Document</title>

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

  <a href="#announcementSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-bullhorn"></i> Announcement</a>
  <ul class="collapse list-unstyled" id="announcementSubmenu">
    <li><a href="viewAnnouncement.php"><i class="fas fa-eye"></i> View Announcement</a></li>
    <li><a href="announcement.php"><i class="fas fa-plus"></i> Create Announcement</a></li>
  </ul>

  <a href="#formsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-file-alt"></i> Forms</a>
  <ul class="collapse list-unstyled" id="formsSubmenu">
    <li><a href="forms.php"><i class="fas fa-eye"></i> View Form</a></li>
    <li><a href="customized_form.php"><i class="fas fa-plus"></i> Create Form</a></li>
  </ul>

  <a href="progress.php"><i class="fas fa-tasks"></i> Project Progress</a>

  <a href="#presentationSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-chalkboard-teacher"></i> Presentation</a>
  <ul class="collapse list-unstyled" id="presentationSubmenu">
    <li><a href="view_schedule.php"><i class="fas fa-eye"></i> View Presentation</a></li>
    <li><a href="assignPresentation.php"><i class="fas fa-calendar-plus"></i> Schedule Presentation</a></li>
  </ul>

  <a href="#templatesSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-file"></i> Templates</a>
  <ul class="collapse list-unstyled" id="templatesSubmenu">
    <li><a href="templates.php"><i class="fas fa-eye"></i> View Template</a></li>
    <li><a href="uploadTemplates.php"><i class="fas fa-upload"></i> Upload Templates</a></li>
  </ul>

  <a href="#assignmentsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-clipboard"></i> Assignments</a>
  <ul class="collapse list-unstyled" id="assignmentsSubmenu">
    <li><a href="viewportal.php"><i class="fas fa-eye"></i> View Assignments</a></li>
    <li><a href="createSubmission.php"><i class="fas fa-plus"></i> Create Assignment</a></li>
  </ul>

  <a href="viewResult.php"><i class="fas fa-chart-line"></i> Evaluation Result</a>
  <a href="gallery.php"><i class="fas fa-images"></i> Gallery</a>

  <a href="#eventSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-calendar"></i> Events</a>
  <ul class="collapse list-unstyled" id="eventSubmenu">
    <li><a href="event.php"><i class="fas fa-eye"></i> View Events</a></li>
    <li><a href="createevent.php"><i class="fas fa-plus"></i> Create Event</a></li>
  </ul>

  <a href="#durationSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-clock"></i> Durations</a>
  <ul class="collapse list-unstyled" id="durationSubmenu">
    <li><a href="viewduration.php"><i class="fas fa-eye"></i> View Durations</a></li>
    <li><a href="course_duration.php"><i class="fas fa-plus"></i> Create Duration</a></li>
  </ul>

</div>

</body>
</html>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
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