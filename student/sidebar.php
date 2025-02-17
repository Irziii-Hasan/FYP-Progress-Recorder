<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Document</title>

</head>
<body>
<div class="sidebar" style="z-index: 1;" id="sidebar">
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="viewAnnoucement.php"><i class="fas fa-bullhorn"></i> Announcement</a>
  <a href="viewMeetings.php"><i class="fas fa-calendar-alt"></i> View Meeting</a>
  <a href="assignments.php"><i class="fas fa-tasks"></i> Assignments</a>
  <a href="viewTemplates.php"><i class="fas fa-file-alt"></i> Templates</a>
  
  <!-- <a href="#GalleryySubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Gallery</a>
  <ul class="collapse list-unstyled" id="GallerySubmenu">
    <li><a href="gallery.php">View Gallery</a></li>
    <li><a href="upload.php">Upload Video</a></li>
  </ul> -->
  
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
