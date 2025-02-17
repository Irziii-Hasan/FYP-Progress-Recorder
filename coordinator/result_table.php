<?php
include 'session_coordinator.php';
include 'config.php';
// Retrieve batch parameter from URL or define it
$batch = isset($_GET['batch']) ? $_GET['batch'] : ''; // Adjust as needed

// Get current date
$current_date = date('Y-m-d');

// Get duration ID associated with the batch
$duration_id_sql = "SELECT cd.id 
                    FROM course_durations cd 
                    JOIN batches b ON b.course_duration_id = cd.id 
                    WHERE b.batchid = '$batch' 
                    LIMIT 1";

$duration_result = $conn->query($duration_id_sql);
$duration_id = $duration_result->fetch_assoc()['id'] ?? null;

$selected_forms = isset($_GET['forms']) ? $_GET['forms'] : [];

if (!empty($selected_forms)) {
    $form_ids_str = implode(',', array_map('intval', $selected_forms)); // sanitize form IDs
    $sql = "SELECT 
        p.id,
        p.project_id, 
        p.title, 
        f.username AS supervisor_name,
        s1.username AS student1_name, s1.student_id AS student1_id,
        s2.username AS student2_name, s2.student_id AS student2_id,
        s3.username AS student3_name, s3.student_id AS student3_id,
        s4.username AS student4_name, s4.student_id AS student4_id,
        cf.title AS form_title,
        cf.id AS form_id,                   
        cf.total_marks AS form_total_marks,  
        tsm.role,                         
        tsm.total_marks,                  
        tsm.student_id,                  
        etm.total_marks AS external_marks  
        FROM 
        projects p
        LEFT JOIN faculty f ON p.supervisor = f.faculty_id
        LEFT JOIN student s1 ON p.student1 = s1.student_id
        LEFT JOIN student s2 ON p.student2 = s2.student_id
        LEFT JOIN student s3 ON p.student3 = s3.student_id
        LEFT JOIN student s4 ON p.student4 = s4.student_id
        LEFT JOIN customized_form cf ON cf.id IN ($form_ids_str) 
        LEFT JOIN total_student_marks tsm ON tsm.project_id = p.id
        AND tsm.form_id = cf.id                                              
        LEFT JOIN external_total_student_marks etm ON etm.project_id = p.id 
        AND etm.form_id = cf.id AND etm.student_id = tsm.student_id           
        WHERE p.batch = '$batch'
        ORDER BY p.project_id, cf.title ASC";
    $result = $conn->query($sql);
}


// Fetch available forms for the selected duration
$available_forms_sql = "SELECT id, title FROM customized_form WHERE duration_id = '$duration_id'";
$available_forms_result = $conn->query($available_forms_sql);

$available_forms = [];
if ($available_forms_result->num_rows > 0) {
    while ($form_row = $available_forms_result->fetch_assoc()) {
        $available_forms[] = [
            'id' => $form_row['id'],
            'title' => $form_row['title']
        ];
    }
}

// Prepare an array to store project forms
$projects = [];
$forms = [];
$grand_total_marks = 0; // Initialize a variable to store the grand total for all forms
$counted_forms = []; // Array to track which forms have already been counted

// Fetch the result and group forms by project
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $project_id = $row['project_id'];
        $form_id = $row['form_id'];

        // Only add the total_marks for forms that haven't been counted yet
        if ($row['form_total_marks'] && !in_array($form_id, $counted_forms)) {
            $grand_total_marks += $row['form_total_marks'];
            $counted_forms[] = $form_id; // Mark this form as counted
        }
        // If the project doesn't exist in the array, initialize it
        if (!isset($projects[$project_id])) {
            $students = array_filter([$row['student1_name'], $row['student2_name'], $row['student3_name'], $row['student4_name']]);
            $student_ids = [
                'student1' => $row['student1_id'],
                'student2' => $row['student2_id'],
                'student3' => $row['student3_id'],
                'student4' => $row['student4_id']
            ];

            $projects[$project_id] = [
                'project_id' => $row['project_id'],
                'title' => $row['title'],
                'supervisor_name' => $row['supervisor_name'],
                'students' => $students,
                'student_ids' => $student_ids,
                'forms' => []
            ];
        }

        // Assign form title and marks based on role
        if ($row['form_title']) {
            // Determine which student this `student_id` matches in the project
            $matched_student = null;
            foreach ($projects[$project_id]['student_ids'] as $student_key => $student_id) {
                if ($student_id == $row['student_id']) {
                    $matched_student = $student_key;
                    break;
                }
            }

            if ($matched_student) {
                // Initialize marks array for each student if it doesn't exist
                if (!isset($projects[$project_id]['forms'][$row['form_title']][$matched_student])) {
                    $projects[$project_id]['forms'][$row['form_title']][$matched_student] = [
                        'supervisor_marks' => '-',
                        'internal_marks' => [],
                        'external_marks' => '-'
                    ];
                }

                // Assign marks based on role
                if ($row['role'] === 'supervisor') {
                    $projects[$project_id]['forms'][$row['form_title']][$matched_student]['supervisor_marks'] = $row['total_marks'];
                } elseif ($row['role'] === 'internal_evaluator') {
                    // Add the internal evaluator marks to an array
                    $projects[$project_id]['forms'][$row['form_title']][$matched_student]['internal_marks'][] = $row['total_marks'];
                }

                // External marks from `external_total_student_marks`
                if ($row['external_marks']) {
                    $projects[$project_id]['forms'][$row['form_title']][$matched_student]['external_marks'] = $row['external_marks'];
                }

                // Track unique form titles for table headers
                if (!in_array($row['form_title'], $forms)) {
                    $forms[] = $row['form_title'];
                }
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="resultstyle.css">
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
        .grade-fail {
            color: red;
            font-weight: bold;
        }

        .table-responsive {
            max-height: 800px; 
            overflow-y: auto; 
            overflow-x: hidden;
        }

        table {
            width: 100%; /* Make the table take up the full width */
            table-layout: auto; /* Let columns auto-adjust based on content */
        }

        th, td {
            word-wrap: break-word; /* Allow text to wrap to avoid overflow */
            white-space: normal; /* Ensure text wraps within the table cell */
        }
        

        .table td, .table th {
            padding: .45rem !important;
        }
        thead{
            position: sticky;
            top: 0px;
        }

        tbody {
            text-align: center;
            font-size: 14px;
        }
        thead {
            font-size: 14px;
        }

        .button{
            position: fixed;
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
                            <li class="breadcrumb-item"><a href="viewresult.php">View Results</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Projects Table</li>
                        </ol>
                    </nav>

                    <div class="container-fluid mt-5">
                        <h2 class="heading text-center mb-4">Projects Table</h2>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8">
                                    <form id="formSelectionForm" method="GET" action="">
                                        <input type="hidden" name="batch" value="<?php echo $batch; ?>" />
                                        <div class="form-group">
                                            <label for="formSelection">Select Forms:</label><br>
                                            <?php foreach ($available_forms as $form) { ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="forms[]" value="<?php echo $form['id']; ?>"
                                                        id="form-<?php echo $form['id']; ?>" <?php echo isset($_GET['forms']) && in_array($form['id'], $_GET['forms']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="form-<?php echo $form['id']; ?>"><?php echo $form['title']; ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Generate Table</button>
                                    </form>
                                </div>
                                <div class="col-md-4 d-flex justify-content-end align-items-end button">
                                    <div class="d-flex flex-row">
                                        <a href="view_student_results.php" class="btn btn-secondary">View Student Results</a>
                                        <button id="saveTotalsBtn" class="btn btn-primary ms-2">Save Totals</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive table-container" >
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Project ID</th>
                                        <th rowspan="2">Project Title</th>
                                        <th rowspan="2">Supervisor Name</th>
                                        <th rowspan="2">Student Names</th>
                                        <?php foreach ($forms as $form) { ?>
                                            <th colspan="5" class="text-center"><?php echo $form; ?></th>
                                        <?php } ?>
                                        <th rowspan="2" class="grand-total">Grand Total (<?php echo $grand_total_marks; ?>)</th>
                                        <th rowspan="2">GPA</th>
                                        <th rowspan="2">Grade</th>
                                    </tr>
                                    <tr>
                                        <?php foreach ($forms as $form) { ?>
                                            <th>Supervisor <i class="bi bi-person-fill"></i></th>
                                            <th>Internal <i class="bi bi-people-fill"></i></th>
                                            <th>External <i class="bi bi-globe"></i></th>
                                            <th>Average</th>
                                            <th>Total</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($projects)) {
                                        foreach ($projects as $project) {
                                            $first_row = true;
                                            
                                            foreach ($project['students'] as $index => $student_name) {
                                                echo "<tr data-student-id='{$project['student_ids']['student' . ($index + 1)]}' data-project-id='{$project['project_id']}'>";

                                                // Merge project info columns only for the first student row
                                                if ($first_row) {
                                                    echo "<td rowspan='" . count($project['students']) . "'>{$project['project_id']}</td>";
                                                    echo "<td rowspan='" . count($project['students']) . "'>{$project['title']}</td>";
                                                    echo "<td rowspan='" . count($project['students']) . "'>{$project['supervisor_name']}</td>";
                                                    $first_row = false;
                                                }
                                                echo "<td>{$student_name}</td>";

                                                // Initialize the total final marks for the student
                                                $total_final = 0;

                                                // Output form columns for each student with marks
                                                foreach ($forms as $form) {
                                                    $student_key = 'student' . ($index + 1);
                                                    $marks_data = isset($projects[$project['project_id']]['forms'][$form][$student_key]) 
                                                                    ? $projects[$project['project_id']]['forms'][$form][$student_key] 
                                                                    : null;

                                                    $supervisor_marks = $marks_data ? $marks_data['supervisor_marks'] : '-';

                                                    // Check if internal marks exist and average them
                                                    if ($marks_data && !empty($marks_data['internal_marks'])) {
                                                        $internal_marks = array_sum($marks_data['internal_marks']) / count($marks_data['internal_marks']);
                                                    } else {
                                                        $internal_marks = '-';
                                                    }

                                                    $external_marks = $marks_data ? $marks_data['external_marks'] : '-';

                                                    $marks_sum = 0;
                                                    $marks_count = 0;

                                                    if (is_numeric($supervisor_marks)) {
                                                        $marks_sum += $supervisor_marks;
                                                        $marks_count++;
                                                    }

                                                    if (is_numeric($internal_marks)) {
                                                        $marks_sum += $internal_marks;
                                                        $marks_count++;
                                                    }

                                                    if (is_numeric($external_marks)) {
                                                        $marks_sum += $external_marks;
                                                        $marks_count++;
                                                    }

                                                    // Calculate average if at least one mark is available
                                                    if ($marks_count > 0) {
                                                        $average_marks = round($marks_sum / $marks_count, 2);
                                                    } else {
                                                        $average_marks = '-';  // No marks given by any evaluator
                                                    }

                                                    // Use the ceiling value of the average for the total marks
                                                    $total_marks = is_numeric($average_marks) ? ceil($average_marks) : '-';

                                                    echo "<td>{$supervisor_marks}</td>";
                                                    echo "<td>{$internal_marks}</td>";
                                                    echo "<td>{$external_marks}</td>";
                                                    echo "<td>{$average_marks}</td>";
                                                    echo "<td>{$total_marks}</td>";

                                                    // Add this form's total marks to the student's grand total
                                                    if (is_numeric($total_marks)) {
                                                        $total_final += $total_marks;
                                                    }
                                                }

                                                // Output grand total columns
                                                echo "<td class='grand-total' style='width: 100px;'>{$total_final}</td>";

                                                // Calculate GPA based on the calculated total_final marks
                                                $percentage = ($total_final / $grand_total_marks) * 100;
                                                $gpa = '-';
                                                $grade = 'F';

                                                if ($percentage >= 61 && $percentage <= 63) {
                                                    $gpa = 2.0;
                                                    $grade = 'C';
                                                } elseif ($percentage >= 64 && $percentage <= 67) {
                                                    $gpa = 2.33;
                                                    $grade = 'C+';
                                                } elseif ($percentage >= 68 && $percentage <= 70) {
                                                    $gpa = 2.66;
                                                    $grade = 'B-';
                                                } elseif ($percentage >= 71 && $percentage <= 74) {
                                                    $gpa = 3.0;
                                                    $grade = 'B';
                                                } elseif ($percentage >= 75 && $percentage <= 79) {
                                                    $gpa = 3.33;
                                                    $grade = 'B+';
                                                } elseif ($percentage >= 80 && $percentage <= 84) {
                                                    $gpa = 3.66;
                                                    $grade = 'A-';
                                                } elseif ($percentage >= 85) {
                                                    $gpa = 4.0;
                                                    $grade = 'A';
                                                }
                                                if ($grade === 'F') {
                                                    $grade = '<span style="color: red; font-weight: bold;">F</span>';
                                                }

                                                // Output GPA and Grade columns
                                                echo "<td class='gpa'>{$gpa}</td>";
                                                echo "<td class='grade'>{$grade}</td>";
                                                echo "</tr>";
                                            }
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No projects found for the selected batch.</td></tr>";
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
    <script>
    document.getElementById('saveTotalsBtn').addEventListener('click', function() {
    // Get all input fields with the grand totals
    const grandTotalCells = document.querySelectorAll('.grand-total');
    const data = [];

    const title = prompt('Please enter the title for the results:'); // Prompt for title

    // Check if the user canceled the prompt
    if (title === null) {
        alert('Result saving canceled.');
        return; // Stop the execution
    }

    // Get grand total marks from PHP
    const grandTotalMarks = <?php echo $grand_total_marks; ?>;

    grandTotalCells.forEach(cell => {
        const studentRow = cell.closest('tr');
        const studentId = studentRow.getAttribute('data-student-id');
        const projectId = studentRow.getAttribute('data-project-id');
        const totalMarks = cell.textContent;

        // Calculate percentage, GPA, and grade
        const percentage = (totalMarks / grandTotalMarks) * 100;
        const { gpa, grade } = calculateGPA(percentage);

        data.push({
            student_id: studentId,
            project_id: projectId,
            total_marks: totalMarks,
            title: title, // Include title in the data
            gpa: gpa,
            grade: grade,
            grand_total_marks: grandTotalMarks // Include grand total marks in the data
        });
    });

    // Send AJAX request to PHP to save the results
    fetch('save_totals.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ totals: data })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Results saved successfully!');
        } else {
            alert('Error saving results. Please try again.');
        }
    })
    .catch(error => console.error('Error:', error));
});


// Function to calculate GPA and Grade based on percentage
function calculateGPA(percentage) {
    if (percentage >= 61 && percentage <= 63) {
        return { gpa: 2.0, grade: 'C' };
    } else if (percentage >= 64 && percentage <= 67) {
        return { gpa: 2.33, grade: 'C+' };
    } else if (percentage >= 68 && percentage <= 70) {
        return { gpa: 2.66, grade: 'B-' };
    } else if (percentage >= 71 && percentage <= 74) {
        return { gpa: 3.0, grade: 'B' };
    } else if (percentage >= 75 && percentage <= 79) {
        return { gpa: 3.33, grade: 'B+' };
    } else if (percentage >= 80 && percentage <= 84) {
        return { gpa: 3.66, grade: 'A-' };
    } else if (percentage >= 85) {
        return { gpa: 4.0, grade: 'A' };
    } else {
        return { gpa: '-', grade: 'F' }; // For invalid or lower percentages
    }
}

</script>
</body>
</html>