<div class="sidebar" id="sidebar">
<a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

<a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcement</a>

  <a href="remarks.php" ><i class="fas fa-clipboard-check"></i>Enter Marks</a>
  
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
