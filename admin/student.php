<?php include 'session_admin.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
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
        }</style>
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
            <li class="breadcrumb-item active" aria-current="page">Student</li>
          </ol>
        </nav>
        <div class="container mt-5">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="heading">Student List</h1>
            <a href="addstudent.php" class="btn btn-primary">Add Student</a>
          </div>

         

          <!-- Search -->
          <!-- Filter and Search -->
          <div class="row mb-3 justify-content-between">
            <div class="col-md-2">
              <!-- Filter by Batch -->
              <form method="GET" action="">
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-filter"></i></span>
                  <select name="batch" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Batch</option>
                    <?php
                    // Database connection
                    include 'config.php';

                    // Fetch batches from the batches table
                    $batchQuery = "SELECT BatchID, BatchName FROM batches ORDER BY BatchName ASC";
                    $batchResult = $conn->query($batchQuery);

                    if ($batchResult->num_rows > 0) {
                        while ($batch = $batchResult->fetch_assoc()) {
                            $selected = isset($_GET['batch']) && $_GET['batch'] == $batch['BatchName'] ? 'selected' : '';
                            echo "<option value='{$batch['BatchName']}' $selected>{$batch['BatchName']}</option>";
                        }
                    }
                    ?>
                  </select>
                </div>
              </form>
            </div>

            <div class="row mb-3 justify-content-center">
  <div class="col-md-6">
    <!-- Search -->
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-search"></i></span>
      <input class="form-control" type="search" id="myInput" placeholder="Search" aria-label="Search">
    </div>
  </div>
</div>

          <!-- Student Table -->
          <div class="table-container">
          <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S. No</th>
                  <th>User ID</th>
                  <th>Name</th>
                  <th>Enrollment No</th>
                  <th>Email</th>
                  <th>Degree Program</th>
                  <th>Batch</th> <!-- New column for Batch -->
                  <th>Actions</th> <!-- New column for actions -->
                  <th>Send Credential</th> <!-- New column for actions -->
                </tr>
              </thead>
              <tbody id="myTable">
                <?php

                // Batch filtering logic
                $batchFilter = '';
                if (isset($_GET['batch']) && !empty($_GET['batch'])) {
                    $batchID = mysqli_real_escape_string($conn, $_GET['batch']);
                    $batchFilter = "WHERE batch = '$batchID'";
                }
 if (isset($_GET['id']) && !empty($_GET['id'])) {
                      // Sanitize the input to prevent SQL injection
                      $juw_id = mysqli_real_escape_string($conn, $_GET['id']);

                      // Retrieve email associated with the JUW ID
                      $sql_select_email = "SELECT email FROM student WHERE juw_id = '$juw_id'";
                      $result = $conn->query($sql_select_email);

                      if ($result->num_rows > 0) {
                          $row = $result->fetch_assoc();
                          $email = $row['email'];

                          // SQL to delete the user from the user table based on email
                          $sql_delete_user = "DELETE FROM user WHERE email = '$email'";
                          if ($conn->query($sql_delete_user) === TRUE) {
                              // Delete corresponding student record based on JUW ID
                              $sql_delete_student = "DELETE FROM student WHERE juw_id = '$juw_id'";
                              if ($conn->query($sql_delete_student) === TRUE) {
                                  //echo "<div class='alert alert-success'>User and associated student records deleted successfully.</div>";
                              } else {
                                  echo "<div class='alert alert-danger'>Error deleting associated student records: " . $conn->error . "</div>";
                              }
                          } else {
                              echo "<div class='alert alert-danger'>Error deleting user: " . $conn->error . "</div>";
                          }
                      } else {
                          echo "<div class='alert alert-warning'>No matching record found in the student table.</div>";
                      }
                  }
                // Fetch students based on the selected batch
                $sql = "SELECT juw_id,enrollment, username, email, degree_program , batch FROM student $batchFilter ORDER BY username ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td>" . $row["juw_id"] . "</td>";
                        echo "<td>" . $row["username"] . "</td>";
                        echo "<td>" . $row["enrollment"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["degree_program"] . "</td>";
                        echo "<td>" . $row["batch"] . "</td>"; // Display Batch Name
                        echo "<td>";
                        echo "<a href='editstudent.php?id=" . $row["juw_id"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a>";
                        echo "<button class='btn btn-danger btn-sm' onclick='confirmDelete(\"" . $row["juw_id"] . "\")'><i class='bi bi-trash'></i></button>";
                        echo "</td>";
                        echo "<td>";
                        echo "<button class='btn btn-info btn-sm' onclick='confirmSendEmail(\"" . $row["juw_id"] . "\", \"student\")'><i class='bi bi-envelope'></i></button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No students found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- JavaScript -->
        <script>
          function confirmDelete(juw_id) {
            if (confirm("Are you sure you want to delete this record?")) {
              window.location.href = "?id=" + juw_id;
            }
          }

          function confirmSendEmail(juw_id, userType) {
            if (confirm("Are you sure you want to send an email to this user?")) {
              var form = document.createElement('form');
              form.method = 'POST';
              form.action = 'sendEmail.php';

              var inputId = document.createElement('input');
              inputId.type = 'hidden';
              inputId.name = 'juw_id';
              inputId.value = juw_id;

              var inputType = document.createElement('input');
              inputType.type = 'hidden';
              inputType.name = 'user_type';
              inputType.value = userType;

              form.appendChild(inputId);
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
