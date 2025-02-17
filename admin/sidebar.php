<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
 .sidebar {
    height: 90vh; /* Full viewport height */
    overflow-y: auto; /* Add ve
    rtical scroll */
  } 
  
  .sidebar a {
    display: flex;
    align-items: center;
    gap: 10px; /* Space between the icon and text */
    font-size: 16px;
}
.sidebar i {
    font-size: 18px;
    color: #000;
}

</style>
</head>
<body>
<div class="sidebar" id="sidebar">
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="#facultySubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-chalkboard-teacher"></i> Faculty
    </a>
    <ul class="collapse list-unstyled" id="facultySubmenu">
        <li><a href="addfaculty.php"><i class="fas fa-user-plus"></i> Add Faculty</a></li>
        <li><a href="faculty.php"><i class="fas fa-list"></i> Faculty List</a></li>
    </ul>

    <a href="#studentSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-user-graduate"></i> Student
    </a>
    <ul class="collapse list-unstyled" id="studentSubmenu">
        <li><a href="addstudent.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
        <li><a href="student.php"><i class="fas fa-list"></i> Student List</a></li>
    </ul>

    <a href="#externalSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-users"></i> External
    </a>
    <ul class="collapse list-unstyled" id="externalSubmenu">
        <li><a href="addexternal.php"><i class="fas fa-user-plus"></i> Add External</a></li>
        <li><a href="external.php"><i class="fas fa-list"></i> External List</a></li>
    </ul>

    <a href="#projectSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-project-diagram"></i> Project
    </a>
    <ul class="collapse list-unstyled" id="projectSubmenu">
        <li><a href="addproject.php"><i class="fas fa-plus-circle"></i> Assign Project</a></li>
        <li><a href="project.php"><i class="fas fa-list"></i> Project List</a></li>
    </ul>

    <a href="#coordinatorSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-user-tie"></i> Coordinator
    </a>
    <ul class="collapse list-unstyled" id="coordinatorSubmenu">
        <li><a href="addcoordinator.php"><i class="fas fa-plus-circle"></i> Assign Coordinator</a></li>
        <li><a href="Coordinator.php"><i class="fas fa-list"></i> Coordinator List</a></li>
    </ul>

    <a href="#batchSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-layer-group"></i> Batch
    </a>
    <ul class="collapse list-unstyled" id="batchSubmenu">
        <li><a href="addBatch.php"><i class="fas fa-plus-circle"></i> Add Batch</a></li>
        <li><a href="batch.php"><i class="fas fa-list"></i> Batch List</a></li>
    </ul>

    <a href="#roomSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-door-open"></i> Room
    </a>
    <ul class="collapse list-unstyled" id="roomSubmenu">
        <li><a href="addroom.php"><i class="fas fa-plus-circle"></i> Add Room</a></li>
        <li><a href="room.php"><i class="fas fa-list"></i> Room List</a></li>
    </ul>

    <a href="#durationSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="fas fa-clock"></i> Duration
    </a>
    <ul class="collapse list-unstyled" id="durationSubmenu">
        <li><a href="viewduration.php"><i class="fas fa-eye"></i> View Duration</a></li>
        <li><a href="course_Duration.php"><i class="fas fa-plus-circle"></i> Add Duration</a></li>
    </ul>
</div>
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

</body>
</html>