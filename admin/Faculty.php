<?php include 'session_admin.php'; ?>
<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <style>.heading {
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

        .breadc{
          display:inline-block;
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
        <nav aria-label="breadcrumb"  class="breadc">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item active" aria-current="page">Faculty</li>
          </ol>
        </nav>
        <div class="container mt-5">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="heading">Faculty List</h1>
            <a href="addfaculty.php" class="btn btn-primary">Add Faculty</a>
          </div>
          <!-- Search and Filter -->
          <div class="row mb-3 justify-content-center">
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-prepend">
                  <div class="input-group-text"><i class="bi bi-search"></i></div>
                </span>
                <input class="form-control me-2" type="search" id="myInput" placeholder="Search" aria-label="Search">
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
                  <th>Professional Email</th>
                  <th>Designation</th>
                  <th>Actions</th>
                  <th>Send Credentials</th> <!-- New column for actions -->
                </tr>
              </thead>
              <tbody id="myTable">
                <?php
                

                  if (isset($_GET['id']) && !empty($_GET['id'])) {
                      // Sanitize the input to prevent SQL injection
                      $juw_id = mysqli_real_escape_string($conn, $_GET['id']);

                      // Retrieve email associated with the JUW ID
                      $sql_select_email = "SELECT email FROM faculty WHERE juw_id = '$juw_id'";
                      $result = $conn->query($sql_select_email);

                      if ($result->num_rows > 0) {
                          $row = $result->fetch_assoc();
                          $email = $row['email'];

                          // SQL to delete the user from the user table based on email
                          $sql_delete_user = "DELETE FROM user WHERE email = '$email'";
                          if ($conn->query($sql_delete_user) === TRUE) {
                              // Delete corresponding faculty record based on JUW ID
                              $sql_delete_faculty = "DELETE FROM faculty WHERE juw_id = '$juw_id'";
                              if ($conn->query($sql_delete_faculty) === TRUE) {
                                  //echo "<div class='alert alert-success'>User and associated faculty records deleted successfully.</div>";
                              } else {
                                  echo "<div class='alert alert-danger'>Error deleting associated faculty records: " . $conn->error . "</div>";
                              }
                          } else {
                              echo "<div class='alert alert-danger'>Error deleting user: " . $conn->error . "</div>";
                          }
                      } else {
                          echo "<div class='alert alert-warning'>No matching record found in the faculty table.</div>";
                      }
                  }

                  $sql = "SELECT juw_id, username, email, professional_email, designation FROM faculty ORDER BY juw_id ASC";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                      $count = 1;
                      // Output data of each row
                      while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>" . $count++ . "</td>";
                          echo "<td>" . $row["juw_id"] . "</td>";
                          echo "<td>" . $row["username"] . "</td>";
                          echo "<td>" . $row["email"] . "</td>";
                          echo "<td>" . $row["professional_email"] . "</td>";
                          echo "<td>" . $row["designation"] . "</td>";
                          // Actions column with edit, delete and send email buttons
                          echo "<td>";
                          echo "<a href='editfaculty.php?id=" . $row["juw_id"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a>";
                          echo "<button class='btn btn-danger btn-sm' onclick='confirmDelete(\"" . $row["juw_id"] . "\")'><i class='bi bi-trash'></i></button>";
                          echo "</td>";
                          echo "<td>";
                          echo "<button class='btn btn-info btn-sm' onclick='confirmSendEmail(\"" . $row["juw_id"] . "\", \"" . $row["email"] . "\", \"faculty\")'><i class='bi bi-envelope'></i></button>";
                          echo "</td>";
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='7'>No faculty members found</td></tr>"; // Updated colspan to 7
                  }
                  $conn->close();
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

          function confirmDelete(juw_id) {
            if (confirm("Are you sure you want to delete this record?")) {
              window.location.href = "?id=" + juw_id;
            }
          }

          function confirmSendEmail(juw_id, email, userType) {
            if (confirm("Are you sure you want to send an email to this user?")) {
              var form = document.createElement('form');
              form.method = 'POST';
              form.action = 'sendEmail.php';

              var inputJuwId = document.createElement('input');
              inputJuwId.type = 'hidden';
              inputJuwId.name = 'juw_id';
              inputJuwId.value = juw_id.charAt(0).toLowerCase() + juw_id.slice(1); // Convert first letter to lowercase

              var inputEmail = document.createElement('input');
              inputEmail.type = 'hidden';
              inputEmail.name = 'email';
              inputEmail.value = email;

              var inputType = document.createElement('input');
              inputType.type = 'hidden';
              inputType.name = 'user_type';
              inputType.value = userType;

              form.appendChild(inputJuwId);
              form.appendChild(inputEmail);
              form.appendChild(inputType);
              document.body.appendChild(form);
              form.submit();
            }
          }

          document.getElementById("myInput").addEventListener("keyup", function() {
            var filter, table, rows;
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
      </div>
    </div>
  </div>
</div>
<?php if (isset($message)) echo $message; ?>

</body>
</html>
