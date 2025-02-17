<?php
// Include necessary files
include 'session_admin.php'; 
include 'config.php'; 

// Fetch distinct titles from the result_detail table for selection
$sql_titles = "SELECT DISTINCT title FROM result_detail"; 
$result_titles = $conn->query($sql_titles);

// Handle title selection
$selected_title = isset($_POST['selected_title']) ? $_POST['selected_title'] : '';

// Fetch results from the student_grand_totals table based on the selected result_id
$sql = "SELECT sgt.student_id, s.username, sgt.project_id, sgt.total_marks, sgt.total, rd.title, 
               p.project_id AS project_id_ref, p.title AS project_title, sgt.gpa, sgt.grade
        FROM student_grand_totals sgt
        JOIN result_detail rd ON sgt.result_id = rd.result_id
        JOIN projects p ON sgt.project_id = p.id
        JOIN student s ON sgt.student_id = s.student_id
        WHERE rd.title = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selected_title);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are results
if ($result->num_rows > 0) {
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $title = htmlspecialchars($selected_title);
} else {
    $results = [];
    $title = "No Title Available"; 
}

// Handle the "Publish" action
if (isset($_POST['publish_title'])) {
    // Get the selected title and audience
    $selected_title = $_POST['selected_title'];
    $selected_audience = isset($_POST['audience']) ? $_POST['audience'] : [];

    // Get the result_id for the selected title
    $sql_result_id = "SELECT result_id FROM result_detail WHERE title = ?";
    $stmt_result = $conn->prepare($sql_result_id);
    $stmt_result->bind_param("s", $selected_title);
    $stmt_result->execute();
    $result_id = $stmt_result->get_result()->fetch_assoc()['result_id'];

    // Combine selected audience types into a comma-separated string
    $audience_type = implode(', ', $selected_audience);

    // Update the Audience_Type column in the student_grand_totals table
    $update_audience_sql = "UPDATE student_grand_totals SET Audience_Type = ?, publish = 1 WHERE result_id = ?";
    $update_stmt = $conn->prepare($update_audience_sql);
    $update_stmt->bind_param("si", $audience_type, $result_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Records published successfully with audience type: " . htmlspecialchars($audience_type) . "');</script>";
    } else {
        echo "<script>alert('Error updating audience type: " . $conn->error . "');</script>";
    }
}

// Ensure this is before any HTML output
if (isset($result_id)) {
    // Fetch the existing audience type for the result_id
    $sql_existing_audience = "SELECT Audience_Type FROM student_grand_totals WHERE result_id = ?";
    $stmt_existing = $conn->prepare($sql_existing_audience);
    $stmt_existing->bind_param("i", $result_id);
    $stmt_existing->execute();
    $existing_audience = $stmt_existing->get_result()->fetch_assoc()['Audience_Type'];
    $existing_audience_array = explode(', ', $existing_audience); // Convert to array
} else {
    $existing_audience_array = []; // No result_id available
}

// Handle update form submission
if (isset($_POST['update_record'])) {
    $student_id = $_POST['student_id'];
    $project_id = $_POST['project_id'];
    $total_marks = $_POST['total_marks']; // Obtained marks
    $total = $_POST['total']; // Total marks
    $gpa = $_POST['gpa'];
    $grade = $_POST['grade'];

    // Server-side validation: Check if obtained marks are less than or equal to total marks
    if ($total_marks > $total) {
        echo "<script>alert('Obtained marks cannot exceed total marks.');</script>";
    } else {
        // Proceed with the update if validation passes
        $update_sql = "UPDATE student_grand_totals 
                       SET total_marks = ?, gpa = ?, grade = ? 
                       WHERE student_id = ? AND project_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("dssii", $total_marks, $gpa, $grade, $student_id, $project_id);

        if ($stmt->execute()) {
            echo "<script>alert('Record updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        #editTable {
            display: none; /* Initially hide the edit table */
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
                                <li class="breadcrumb-item active" aria-current="page">Saved Results</li>
                            </ol>
                        </nav>
                        
                        <div class="container mt-5">
                        <div id="editTable" class="mt-3">
            <h4>Edit Record</h4>
            <form method="post" action="">
                <input type="hidden" id="edit_student_id" name="student_id">
                <input type="hidden" id="edit_project_id" name="project_id">

                <div class="table-container">
                <table class="table table-bordered table-striped">                  <thead>
            <tr>
                <th>S. No.</th>
                <th>Project ID</th>
                <th>Project Title</th>
                <th>Student Name</th>
                <th>Obtained Marks</th>
                <th>Total Marks</th>
                <th>GPA</th>
                <th>Grade</th>
                <th>Action</th>
            </tr>
        </thead>
            

                </table>
    </div>
            </form>
        </div>
                    <h2 class="text-center"><?php echo $title; ?></h2>
                    <h4 class="text-center">Student Results</h4>

                    <div class="d-flex justify-content-between mb-3">
                        <form method="post" action="">
                            <div class="form-group me-2">
                                <select name="selected_title" class="form-select" required>
                                    <option value="" disabled selected>Select Title</option>
                                    <?php while ($row = $result_titles->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($row['title']); ?>"
                                            <?php echo ($row['title'] == $selected_title) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Fetch Results</button>
                        </form>
                    </div>

                    <!-- <form method="post" action="">
                        <input type="hidden" name="selected_title" value="<?php echo htmlspecialchars($selected_title); ?>">
                        <button type="button" class="btn btn-danger mb-3" data-bs-toggle="modal" data-bs-target="#audienceModal">
                            Select Audience & Publish
                        </button>
                    </form> -->

                    <table class="table table-bordered mt-4">
                    <thead>
    <tr>
        <th>S. No.</th>
        <th>Project ID</th>
        <th>Project Title</th>
        <th>Student ID</th>
        <th>Obtained Marks</th>
        <th>Total Marks</th>
        <th>GPA</th>
        <th>Grade</th>
    </tr>
</thead>
<tbody>
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $index => $record): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($record['project_id_ref']); ?></td>
                <td><?php echo htmlspecialchars($record['project_title']); ?></td>
                <td><?php echo htmlspecialchars($record['username']); ?></td>
                <td><?php echo htmlspecialchars($record['total_marks']); ?></td>
                <td><?php echo htmlspecialchars($record['total']); ?></td> <!-- Display total here -->
                <td><?php echo htmlspecialchars($record['gpa']); ?></td>
                <td><?php echo htmlspecialchars($record['grade']); ?></td>

            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="8" class="text-center">No Records Found</td>
        </tr>
    <?php endif; ?>
</tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audience Selection Modal -->
<div class="modal fade" id="audienceModal" tabindex="-1" aria-labelledby="audienceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="">
                <input type="hidden" name="selected_title" value="<?php echo htmlspecialchars($selected_title); ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="audienceModalLabel">Select Audience Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="form-check">
                        <input type="checkbox" id="all" name="audience[]" value="All" class="form-check-input" onclick="toggleAudience(this)">
                        <label for="all" class="form-check-label">All</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="coordinator" name="audience[]" value="Coordinator" class="form-check-input">
                        <label for="coordinator" class="form-check-label">Coordinator</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="admin" name="audience[]" value="Admin" class="form-check-input">
                        <label for="admin" class="form-check-label">Admin</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="faculty" name="audience[]" value="Faculty" class="form-check-input">
                        <label for="faculty" class="form-check-label">Faculty</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="external" name="audience[]" value="External" class="form-check-input">
                        <label for="external" class="form-check-label">External</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="student" name="audience[]" value="Student" class="form-check-input">
                        <label for="student" class="form-check-label">Student</label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="publish_title" class="btn btn-danger">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const existingAudience = <?php echo json_encode($existing_audience_array); ?>;
    existingAudience.forEach(audience => {
        const checkbox = document.querySelector(`input[name="audience[]"][value="${audience}"]`);
        if (checkbox) {
            checkbox.checked = true;
            if (audience === "All") {
                toggleAudience(checkbox);
            }
        }
    });
});
</script>


<script>
   function showEditTable(student_id, project_id, total_marks, gpa, grade, project_title, student_name, total) {
    document.getElementById('edit_student_id').value = student_id;
    document.getElementById('edit_project_id').value = project_id;
    document.getElementById('edit_obtained_marks').value = total_marks;
    document.getElementById('edit_gpa').value = gpa;
    document.getElementById('edit_grade').value = grade;
    document.getElementById('edit_project_id_display').innerText = project_id;
    document.getElementById('edit_project_title_display').innerText = project_title;
    document.getElementById('edit_student_name_display').innerText = student_name;
    document.getElementById('edit_total_marks').value = total; // Set total marks
    document.getElementById('editTable').style.display = 'block';


    // Add event listener for dynamic GPA and Grade update
    document.getElementById('edit_obtained_marks').addEventListener('input', function() {
    const totalMarks = parseFloat(this.value);
    const grandTotal = parseFloat(document.getElementById('edit_total_marks').value); // Get the total marks

    // Check if the obtained marks are greater than total marks
    if (totalMarks > grandTotal) {
        alert("Obtained marks cannot exceed total marks.");
        this.value = ''; // Clear the input
        return;
    }

    // Calculate GPA and Grade
    let percentage = (totalMarks / grandTotal) * 100;
    let gpa = '-';
    let grade = 'F';

    if (percentage >= 61 && percentage <= 63) {
        gpa = 2.0;
        grade = 'C';
    } else if (percentage >= 64 && percentage <= 67) {
        gpa = 2.33;
        grade = 'C+';
    } else if (percentage >= 68 && percentage <= 70) {
        gpa = 2.66;
        grade = 'B-';
    } else if (percentage >= 71 && percentage <= 74) {
        gpa = 3.0;
        grade = 'B';
    } else if (percentage >= 75 && percentage <= 79) {
        gpa = 3.33;
        grade = 'B+';
    } else if (percentage >= 80 && percentage <= 84) {
        gpa = 3.66;
        grade = 'A-';
    } else if (percentage >= 85) {
        gpa = 4.0;
        grade = 'A';
    }

    // Update GPA and Grade fields
    document.getElementById('edit_gpa').value = gpa;
    document.getElementById('edit_grade').value = grade;

    // Display 'F' grade in bold red
    if (grade === 'F') {
        document.getElementById('edit_grade').style.color = 'red';
        document.getElementById('edit_grade').style.fontWeight = 'bold';
    } else {
        document.getElementById('edit_grade').style.color = '';
        document.getElementById('edit_grade').style.fontWeight = '';
    }
});

}

</script>

<script>
function toggleAudience(allCheckbox) {
    const checkboxes = document.querySelectorAll('input[name="audience[]"]');
    checkboxes.forEach(cb => {
        if (cb !== allCheckbox) {
            cb.disabled = allCheckbox.checked;
            cb.checked = false;
        }
    });
}
</script>

</body>
</html>
