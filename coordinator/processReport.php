<?php
include 'session_coordinator.php';
include 'config.php';

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve selected columns, type, and batch
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    $type = isset($_POST['type']) ? $conn->real_escape_string($_POST['type']) : '';
    $batch = isset($_POST['batch']) ? intval($_POST['batch']) : '';

    // Handle the Publish button action
    if (isset($_POST['publish'])) {
        // Get current date and time
        $currentDateTime = date('Y-m-d H:i:s');

        // Construct SQL query to select the relevant presentations
        $sql = "
            SELECT presentations.presentation_id, presentations.send_to
            FROM presentations
            LEFT JOIN batches ON presentations.batch = batches.batchID
            WHERE CONCAT(presentations.date, ' ', presentations.time) > '$currentDateTime'
        ";

        // Add type and batch filtering if selected
        if (!empty($type)) {
            $sql .= " AND presentations.type = '$type'";
        }
        if (!empty($batch)) {
            $sql .= " AND presentations.batch = '$batch'";
        }

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $alreadyPublished = true;
            while ($row = $result->fetch_assoc()) {
                if ($row['send_to'] !== 'all') {
                    $alreadyPublished = false;
                    $presentationId = $row['presentation_id'];
                    $updateSql = "UPDATE presentations SET send_to = 'all' WHERE presentation_id = $presentationId";
                    $conn->query($updateSql);
                }
            }

            if ($alreadyPublished) {
                echo "<script>alert('Records are already published.');</script>";
            } else {
                echo "<script>alert('Records have been published successfully.');</script>";
            }
        } else {
            echo "<script>alert('No records found to publish.');</script>";
        }
    }

    // Create SQL query based on selected columns
    if (!empty($columns)) {
        // Map columns to the database fields and handle special cases
        $columnList = implode(', ', array_map(function($col) {
            if ($col == 'internal_evaluator_id') return 'faculty.username as internal_evaluator';
            if ($col == 'external_evaluator_id') return 'external.name as external_evaluator';
            if ($col == 'project_id') return 'projects.title as project_title, presentations.project_id';
            if ($col == 'room_number') return 'rooms.room_number';
            if ($col == 'batch') return 'batches.batchName as batch_name';
            if ($col == 'date') return 'DATE_FORMAT(presentations.date, "%d-%m-%Y") as formatted_date';
            if ($col == 'time') return 'DATE_FORMAT(presentations.time, "%h:%i %p") as formatted_time';
            return "presentations.`$col`";
        }, $columns));

        // Get current date and time
        $currentDateTime = date('Y-m-d H:i:s');

        // Construct SQL query with type and batch filtering and future presentations only
        $sql = "
            SELECT $columnList
            FROM presentations
            LEFT JOIN faculty ON presentations.internal_evaluator_id = faculty.faculty_id
            LEFT JOIN external ON presentations.external_evaluator_id = external.external_id
            LEFT JOIN projects ON presentations.project_id = projects.id
            LEFT JOIN rooms ON presentations.room_id = rooms.room_id
            LEFT JOIN batches ON presentations.batch = batches.batchID
            WHERE CONCAT(presentations.date, ' ', presentations.time) > '$currentDateTime'
        ";

        // Add type and batch filtering if selected
        if (!empty($type)) {
            $sql .= " AND presentations.type = '$type'";
        }
        if (!empty($batch)) {
            $sql .= " AND presentations.batch = '$batch'";
        }

        $result = $conn->query($sql);

        if (!$result) {
            die("Error in query: " . $conn->error);
        }
    } else {
        // If no columns selected, redirect or show an error
        header('Location: generateReport.php');
        exit();
    }
} else {
    // Redirect to generateReport.php if accessed directly
    header('Location: generateReport.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Processed Report</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom styles -->
  <link rel="stylesheet" href="style.css">
  <style>
    .container {
      max-width: 900px;
      margin-top: 30px;
    }
    .table-container {
      border: 1.5px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      background-color: #f9f9f9;
      margin-bottom: 20px;
    }
    th, td {
      text-align: left;
    }
    th {
      background-color: #e9ecef;
      text-align: center;
    }
    td {
      vertical-align: middle;
    }
    .breadcrumb {
      margin-bottom: 20px;
    }
    h1, h2 {
      margin-bottom: 20px;
    }
    .button-container {
      display: flex;
      justify-content: space-between;
      padding-bottom: 100px;
    }
    .button-container .btn-group {
      display: flex;
      gap: 10px;
    }
    .publish-button {
      position: absolute;
      top: 15px;
      right: 15px;
    }
    @media print {
      .button-container, .breadcrumb, .container {
        display: none;
      }
      #print-content {
        border: 1px solid #ddd;
        padding: 20px;
        width: 100%;
      }
    }
  </style>
  <!-- Include html2canvas -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
            <li class="breadcrumb-item"><a href="view_Schedule.php">Presentation Schedule</a></li>
            <li class="breadcrumb-item active" aria-current="page">Processed Report</li>
          </ol>
        </nav>

        <div class="container">
          <!-- Add Publish button -->
          <form method="POST" style="position: relative; top: -25px;">
            <button type="submit" name="publish" class="btn btn-warning publish-button">Publish</button>
          </form>

          <!-- Add form for heading and subheading -->
          <form id="heading-form">
            <div class="mb-3">
              <label for="heading" class="form-label">Heading</label>
              <input type="text" class="form-control" id="heading" name="heading" required>
            </div>
            <div class="mb-3">
              <label for="subheading" class="form-label">Subheading</label>
              <input type="text" class="form-control" id="subheading" name="subheading" required>
            </div>
          </form>

          <div id="print-content">
            <!-- Placeholders for heading and subheading -->
            <h1 id="report-heading" class="text-center"></h1>
            <h2 id="report-subheading" class="text-center"></h2>

            <?php if (isset($result) && $result->num_rows > 0): ?>
              <div class="table-container">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <?php foreach ($columns as $column): ?>
                        <th>
                          <?php
                          if ($column == 'internal_evaluator_id') echo 'Internal Evaluator';
                          else if ($column == 'external_evaluator_id') echo 'External Evaluator';
                          else if ($column == 'project_id') echo 'Project Title';
                          else if ($column == 'room_number') echo 'Room Number';
                          else if ($column == 'batch') echo 'Batch Name';
                          else if ($column == 'date') echo 'Date';
                          else if ($column == 'time') echo 'Time';
                          else echo ucfirst(str_replace('_', ' ', $column));
                          ?>
                        </th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <?php foreach ($columns as $column): ?>
                          <td>
                            <?php
                            if ($column == 'internal_evaluator_id') echo htmlspecialchars($row['internal_evaluator']);
                            else if ($column == 'external_evaluator_id') echo htmlspecialchars($row['external_evaluator']);
                            else if ($column == 'project_id') echo htmlspecialchars($row['project_title']);
                            else if ($column == 'batch') echo htmlspecialchars($row['batch_name']);
                            else if ($column == 'date') echo htmlspecialchars($row['formatted_date']);
                            else if ($column == 'time') echo htmlspecialchars($row['formatted_time']);
                            else echo htmlspecialchars($row[$column]);
                            ?>
                          </td>
                        <?php endforeach; ?>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="table-container">
                <p>No data found for the selected columns.</p>
              </div>
            <?php endif; ?>
          </div>

          <div class="button-container">
            <form action="generateReport.php" method="get">
              <button type="submit" class="btn btn-secondary">Go Back</button>
            </form>
            <div class="btn-group">
              <?php if (isset($result) && $result->num_rows > 0): ?>
                <button id="download-btn" class="btn btn-primary">Download as JPG</button>
                <button id="print-btn" class="btn btn-success">Print</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('download-btn').addEventListener('click', function() {
    var heading = document.getElementById('heading').value;
    var subheading = document.getElementById('subheading').value;
    document.getElementById('report-heading').innerText = heading;
    document.getElementById('report-subheading').innerText = subheading;

    html2canvas(document.getElementById('print-content')).then(function(canvas) {
        var link = document.createElement('a');
        link.href = canvas.toDataURL('image/jpeg');
        link.download = 'report.jpg';
        link.click();
    });
});

document.getElementById('print-btn').addEventListener('click', function() {
    var heading = document.getElementById('heading').value;
    var subheading = document.getElementById('subheading').value;
    document.getElementById('report-heading').innerText = heading;
    document.getElementById('report-subheading').innerText = subheading;

    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Report</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">');
    printWindow.document.write('<style>h1, h2 { text-align: center; } table { width: 100%; margin-top: 20px; } th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>' + heading + '</h1>');
    printWindow.document.write('<h2>' + subheading + '</h2>');
    printWindow.document.write(document.querySelector('.table-container').outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
});
</script>

</body>
</html>
