<?php
session_start();
include 'config.php'; // Database connection file

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student's project created in 2025 or later
$query = "SELECT id FROM projects WHERE (student1 = ? OR student2 = ? OR student3 = ? OR student4 = ?) AND created_at >= '2025-01-01'";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $student_id, $student_id, $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row['id'];
}
$stmt->close();

$recommended_projects = [];
if (!empty($projects)) {
    $placeholders = implode(',', array_fill(0, count($projects), '?'));
    $query = "SELECT pr.recommended_project_id, p.title, p.description, (pr.similarity_score * 100) AS similarity_score, f.faculty_id, f.username AS supervisor, f.professional_email, v.file_path 
              FROM project_recommendations pr
              JOIN projects p ON pr.recommended_project_id = p.id
              LEFT JOIN faculty f ON p.supervisor = f.faculty_id
              LEFT JOIN videos v ON p.title = v.title
              WHERE pr.project_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat("i", count($projects)), ...$projects);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recommended_projects[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommended Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .card { margin-bottom: 20px; }
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
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
        .sidebar {
            height: 100vh;
            overflow-y: auto;
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
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recommended Projects</li>
          </ol>
        </nav>
        
        <div class="container mt-5">
            <h2>Recommended Projects</h2>
            <div class="row">
                <?php if (!empty($recommended_projects)): ?>
                    <?php foreach ($recommended_projects as $project): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($project['description']); ?></p>
                                    <p><strong>Similarity Score:</strong> <?php echo round($project['similarity_score'], 2); ?>%</p>
                                    <p><strong>Supervisor:</strong> <?php echo htmlspecialchars($project['supervisor']); ?></p>
                                    
                                    <?php if (!empty($project['file_path'])): ?>
                                        <div class="video-container">
                                            <video controls>
                                                <source src="<?php echo htmlspecialchars($project['file_path']); ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Request for Consultation Button -->
                                    <?php if (!empty($project['professional_email'])): ?>
                                        <a href="mailto:<?php echo htmlspecialchars($project['professional_email']); ?>?subject=Consultation Request&body=Dear <?php echo htmlspecialchars($project['supervisor']); ?>, I would like to discuss the project '<?php echo htmlspecialchars($project['title']); ?>'." class="btn btn-primary mt-2">
                                            Request for Consultation
                                        </a>
                                    <?php endif; ?>
                                    
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recommended projects found.</p>
                <?php endif; ?>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  document.getElementById("myInput").addEventListener("keyup", function() {
    var filter, table, rows;
    filter = this.value.toUpperCase();
    table = document.getElementById("myTable");
    rows = table.getElementsByTagName("tr");
s
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
