<?php
include 'session_coordinator.php';
include 'config.php';

// Fetch all student marks
$marks_sql = "
    SELECT pr.project_id, pr.title AS project_title, sm.marks_title, sm.student_id, sm.student_marks, s.username AS student_name
    FROM student_marks sm
    JOIN student s ON sm.student_id = s.student_id
    JOIN projects pr ON (
        pr.student1 = sm.student_id
        OR pr.student2 = sm.student_id
        OR pr.student3 = sm.student_id
        OR pr.student4 = sm.student_id
    )
";

$stmt = $conn->prepare($marks_sql);
$stmt->execute();
$result = $stmt->get_result();

$marks_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $marks_data[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | View Marks</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .table-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
        }
        .sidebar {
            height: 100vh; /* Full viewport height */
            overflow-y: auto; /* Add vertical scroll */
        }
        .actions-icon {
            font-size: 1.2em;
            cursor: pointer;
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
                        <li class="breadcrumb-item active" aria-current="page">View Marks</li>
                    </ol>
                </nav>
                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">Student Marks</h1>
                    </div>
                    <div class="container mt-5">
                        <div class="table-container">
                            <table class="table table-striped table-hover mt-3">
                                <thead>
                                    <tr>
                                        <th>Project ID</th>
                                        <th>Project Title</th>
                                        <th>Marks Title</th>
                                        <th>Student Name</th>
                                        <th>Marks</th>
                                        <th>Actions</th> <!-- New Actions column -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($marks_data)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No marks found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($marks_data as $mark): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($mark['project_id']); ?></td>
                                                <td><?php echo htmlspecialchars($mark['project_title']); ?></td>
                                                <td><?php echo htmlspecialchars($mark['marks_title']); ?></td>
                                                <td><?php echo htmlspecialchars($mark['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($mark['student_marks']); ?></td>
                                                <td>
                                                    <a href="#" 
                                                       class="actions-icon" 
                                                       data-project_id="<?php echo htmlspecialchars($mark['project_id']); ?>"
                                                       data-marks_title="<?php echo htmlspecialchars($mark['marks_title']); ?>"
                                                       data-student_id="<?php echo htmlspecialchars($mark['student_id']); ?>"
                                                       data-student_marks="<?php echo htmlspecialchars($mark['student_marks']); ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Marks Modal -->
<div class="modal fade" id="editMarksModal" tabindex="-1" aria-labelledby="editMarksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMarksModalLabel">Edit Marks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMarksForm" action="update_marks.php" method="post">
                    <input type="hidden" name="student_id" id="student_id">
                    <input type="hidden" name="project_id" id="project_id">
                    <div class="mb-3">
                        <label for="marks_title" class="form-label">Marks Title</label>
                        <input type="text" class="form-control" id="marks_title" name="marks_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="student_marks" class="form-label">Marks</label>
                        <input type="number" class="form-control" id="student_marks" name="student_marks" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle the edit button click
    document.querySelectorAll('.actions-icon').forEach(button => {
        button.addEventListener('click', function() {
            const project_id = this.getAttribute('data-project_id');
            const marks_title = this.getAttribute('data-marks_title');
            const student_id = this.getAttribute('data-student_id');
            const student_marks = this.getAttribute('data-student_marks');

            // Set values in the modal
            document.getElementById('project_id').value = project_id;
            document.getElementById('marks_title').value = marks_title;
            document.getElementById('student_id').value = student_id;
            document.getElementById('student_marks').value = student_marks;

            // Show the modal
            new bootstrap.Modal(document.getElementById('editMarksModal')).show();
        });
    });
});
</script>
</body>
</html>
