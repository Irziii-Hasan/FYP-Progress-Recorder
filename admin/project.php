<?php

include 'session_admin.php';
include 'config.php';


if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $project_id = intval($_GET['id']); // Ensure ID is an integer to prevent SQL injection
    
    // Delete query
    $delete_sql = "DELETE FROM projects WHERE id = ?";
    
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $project_id);

    if ($stmt->execute()) {
        // Redirect after successful deletion
        header("Location: project.php?msg=Project deleted successfully");
        exit();
    } else {
        echo "Error deleting project: " . $conn->error;
    }

    $stmt->close();
}


$search_query = "";

// Fetch projects data
$sql = "SELECT projects.id, projects.project_id, projects.title, 
        faculty.username AS supervisor_name,
        batches.batchName AS batch_name
        FROM projects
        LEFT JOIN faculty ON projects.supervisor = faculty.faculty_id
        LEFT JOIN batches ON projects.batch = batches.batchID";

$result = $conn->query($sql);
$projects = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP | View Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Projects</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="child heading">
                        <h1 class="heading">Projects List</h1>
                        </div>
                        <div class="child">
                        <a href="detail.php?" class="btn btn-secondary">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <a href="addproject.php" class="btn btn-primary">Assign Project</a>
                        </div>
                        
                    </div>

                    <div class="row mb-3 justify-content-center">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-prepend">
                <div class="input-group-text"><i class="bi bi-search"></i></div>
            </span>
            <input class="form-control" type="search" placeholder="Search..." id="searchInput">
        </div>
    </div>
</div>


                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Project ID</th>
                                    <th>Title</th>
                                    <th>Supervisor</th>
                                    <th>Batch</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($projects)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No projects found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($projects as $project): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($project['project_id']); ?></td>
                                            <td><?php echo htmlspecialchars($project['title']); ?></td>
                                            <td><?php echo htmlspecialchars($project['supervisor_name']); ?></td>
                                            <td><?php echo htmlspecialchars($project['batch_name']); ?></td>
                                            <td>
                                                <a href="editproject.php?id=<?php echo $project['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                                <a href="project.php?action=delete&id=<?php echo $project['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this project?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#projectModal" onclick="loadProjectModal(<?php echo $project['id']; ?>)"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Project Modal -->
                <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="projectModalLabel">Project Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="modal-body-content">
                                <!-- Modal content will be loaded here -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function loadProjectModal(projectId) {
        fetch('project_modal.php?id=' + projectId)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById('projectModalLabel').innerText = data.title;
                document.getElementById('modal-body-content').innerHTML = `
                    <p><strong>Project ID:</strong> ${data.project_id}</p>
                    <p><strong>Title:</strong> ${data.title}</p>
                    <p><strong>Description:</strong></p>
                    <p>${data.description.replace(/\n/g, '<br>')}</p>
                    <p><strong>Supervisor:</strong> ${data.supervisor_name}</p>
                    <p><strong>Co-Supervisor:</strong> ${data.co_supervisor_name}</p>
                    <p><strong>External Supervisor:</strong> ${data.external_supervisor_name}</p>
                    <p><strong>Students:</strong> ${data.students}</p>
                    <p><strong>Batch:</strong> ${data.batch_name}</p>
                    <p><strong>Created At:</strong> ${data.created_at}</p>
                `;
            }
        })
        .catch(error => console.error('Error fetching modal content:', error));
    }
</script>

<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    let searchValue = this.value.toLowerCase();
    let tableRows = document.querySelectorAll(".table tbody tr");

    tableRows.forEach(row => {
        let projectID = row.cells[0].textContent.toLowerCase();
        let title = row.cells[1].textContent.toLowerCase();
        let supervisor = row.cells[2].textContent.toLowerCase();
        let batch = row.cells[3].textContent.toLowerCase();

        if (projectID.includes(searchValue) || title.includes(searchValue) || supervisor.includes(searchValue) || batch.includes(searchValue)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>


<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
