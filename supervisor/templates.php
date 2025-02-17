<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
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
            <li class="breadcrumb-item active" aria-current="page">Templates</li>
          </ol>
        </nav>

        <div class="container mt-5">
          <h1 class="heading">Templates</h1>
          <div class="table-container">
          <table class="table table-bordered table-striped"> 
              <thead>
                <tr>
                  <th>S. No</th>
                  <th>Document Name</th>
                  <th>Upload Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="myTable">
                <?php
                // Database connection
                include 'config.php';

                $sql = "SELECT Template_id, document_name, file_path, upload_date FROM templates WHERE send_to IN ('all', 'faculty')";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $count = 1;
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $filePath = '../coordinator/uploads/' . basename($row["file_path"]);
                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td><a href='" . $filePath . "' target='_blank'>" . htmlspecialchars($row["document_name"]) . "</a></td>";
                        echo "<td>" . date("d-m-Y", strtotime($row["upload_date"])) . "</td>";
                        echo "<td><a href='" . $filePath . "' class='btn btn-info btn-sm' download>Download</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No templates found</td></tr>";
                }
                $conn->close();
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
  document.getElementById("myInput").addEventListener("keyup", function () {
    var filter, table, rows;
    filter = this.value.toUpperCase();
    table = document.getElementById("myTable");
    rows = table.getElementsByTagName("tr");

    for (var i = 0; i < rows.length; i++) {
      var cells = rows[i].getElementsByTagName("td");
      var found = false;
      for (var j = 0; j < cells.length && !found; j++) {
        var cell = cells[j];
        if (cell && cell.innerHTML.toUpperCase().indexOf(filter) > -1) {
          found = true;
        }
      }
      rows[i].style.display = found ? "" : "none";
    }
  });
</script>
</body>
</html>
