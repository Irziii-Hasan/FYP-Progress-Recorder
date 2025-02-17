<?php
include 'session_admin.php'; // Include session handling

// Database connection
include 'config.php';

$search_query = "";

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    // Sanitize the input
    $search_query = $conn->real_escape_string($search_query);

    // Modify the query to filter results based on the search query
    $sql = "SELECT * FROM external WHERE 
            juw_id LIKE '%$search_query%' OR
            name LIKE '%$search_query%' OR
            contact LIKE '%$search_query%' OR
            organization LIKE '%$search_query%' OR
            designation LIKE '%$search_query%' OR
            postal_address LIKE '%$search_query%' OR
            email LIKE '%$search_query%'";
} else {
    $sql = "SELECT * FROM external";
}

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>External List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <style>
      .heading {
            color: #0a4a91;
            font-weight: 700;
        }
        .table-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
            margin-top: 20px;
        }
        .btn-view, .btn-add {
            margin-right: 10px;
        }
        table th, table td {
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
            color: #0a4a91;
        }
        .table-bordered {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
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
            <li class="breadcrumb-item active" aria-current="page">External</li>
          </ol>
        </nav>

        <div class="container mt-5">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="heading">External List</h1>
            <a href="addExternal.php" class="btn btn-primary">Add External</a>
          </div>
        <!-- Search and Filter -->

          <div class="row mb-3 justify-content-center">
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-prepend">
                  <div class="input-group-text"><i class="bi bi-search"></i></div>
                </span>
                <input class="form-control me-2" type="search" id="myInput" placeholder="Search" aria-label="Search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
              </div>
            </div>
          </div>


          <div class="table-container">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S. No</th>
                  <th>User ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Organization</th>
                  <th>Postal Address</th> <!-- New column added -->
                  <th>Actions</th>
                  <th>Send Credentials</th> <!-- New column for actions -->
                </tr>
              </thead>
              <tbody id="myTable">
                <?php
                  if ($result->num_rows > 0) {
                      $count = 1;
                      while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>" . $count++ . "</td>";
                          echo "<td>" . $row["juw_id"] . "</td>";
                          echo "<td>" . $row["name"] . "</td>";
                          echo "<td>" . $row["email"] . "</td>";
                          echo "<td>" . $row["organization"] . "</td>";
                          echo "<td>" . $row["postal_address"] . "</td>"; // New column added
                          echo "<td>";
                          echo "<a href='editExternal.php?id=" . $row["juw_id"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a>";
                          echo "<button class='btn btn-danger btn-sm' onclick='confirmDelete(\"" . $row["juw_id"] . "\")'><i class='bi bi-trash'></i></button>";
                          echo "</td>";
                          echo "<td>";
                          echo "<a href='sendEmail.php?id=" . $row["juw_id"] . "' class='btn btn-info btn-sm'><i class='bi bi-envelope'></i></a>";
                          echo "</td>";
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='6'>No external users found</td></tr>";
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  function confirmDelete(juw_id) {
    if (confirm("Are you sure you want to delete this record?")) {
      window.location.href = "deleteExternal.php?id=" + juw_id;
    }
  }

  document.getElementById("myInput").addEventListener("keyup", function() {
    var filter, table,rows;
    filter = document.getElementById("myInput").value.toUpperCase();
table = document.getElementById("myTable");
rows = table.getElementsByTagName("tr");

// Loop through all table rows, and hide those who don't match the search query
for (var i = 0; i < rows.length; i++) {
  var cells = rows[i].getElementsByTagName("td");
  var found = false;
  for (var j = 0; j < cells.length && !found; j++) {
    var cell = cells[j];
    if (cell) {
      if (cell.innerHTML.toUpperCase().indexOf(filter) > -1) {
        found = true;
      }
    }
  }
  if (found) {
    rows[i].style.display = "";
  } else {
    rows[i].style.display = "none";
  }
}
});
</script>
</body>
</html>
