<?php
include 'session_supervisor.php';
include 'config.php';
// Initialize variables
$presentation_id = '';
$form_id = '';
$project_id = '';

// Get the faculty ID from the session for internal evaluator
$faculty_id = $_SESSION['faculty_id'] ?? '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $presentation_id = intval($_POST['presentation_id']);
    $form_id = intval($_POST['form_id']);
    $marks_data = $_POST['marks'] ?? [];

    // Get the project_id from the presentations table
    $project_query = "SELECT project_id FROM presentations WHERE presentation_id = ?";
    $stmt = $conn->prepare($project_query);
    $stmt->bind_param("i", $presentation_id);
    $stmt->execute();
    $stmt->bind_result($project_id);
    $stmt->fetch();
    $stmt->close();

    // Array to hold the total marks for each student
    $student_total_marks = [];

    // Process marks data
    foreach ($marks_data as $description_id => $student_marks) {
        foreach ($student_marks as $student_id => $marks) {
            // If the student is not in the total marks array, initialize it
            if (!isset($student_total_marks[$student_id])) {
                $student_total_marks[$student_id] = 0;
            }

            // Add the current description's marks to the total for this student
            $student_total_marks[$student_id] += $marks;

            // Insert or update individual marks into the marks table (existing logic)
            $insert_query = "
                INSERT INTO marks (form_id, description_id, student_id, marks, supervisor, external, internal, project_id) 
                VALUES (?, ?, ?, ?, 0, 0, ?, ?)
            ";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiidii", $form_id, $description_id, $student_id, $marks, $faculty_id, $project_id);
            $stmt->execute();
        }
    }
    $form_comment = $_POST['form_comment'] ?? ''; // Get the single comment for the form

    // Now insert total marks for each student into the total_student_marks table
    foreach ($student_total_marks as $student_id => $total_marks) {
        $total_marks_query = "
            INSERT INTO total_student_marks (student_id, faculty_id, form_id, project_id, total_marks,comment, role) 
            VALUES (?, ?,?, ?, ?, ?, 'internal_evaluator')
        ";
        $stmt = $conn->prepare($total_marks_query);
        $stmt->bind_param("iiiids", $student_id, $faculty_id, $form_id, $project_id, $total_marks,$form_comment);
        $stmt->execute();
    }

    echo "<script>alert('Marks have been submitted successfully!');</script>";
    echo "<script>window.location.href='remarks.php';</script>";
    exit();
}
// Check if presentation_id is passed
if (isset($_GET['presentation_id'])) {
    $presentation_id = intval($_GET['presentation_id']);
    
    // Fetch presentation and project details
    $sql = $conn->prepare("
        SELECT p.presentation_id, p.batch, p.date, p.time, r.room_number, pr.title AS project_title, pr.description AS project_description,
            s1.student_id AS student1_id, s1.username AS student1_username,
            s2.student_id AS student2_id, s2.username AS student2_username,
            s3.student_id AS student3_id, s3.username AS student3_username,
            s4.student_id AS student4_id, s4.username AS student4_username
        FROM presentations p
        JOIN projects pr ON p.project_id = pr.id
        JOIN rooms r ON p.room_id = r.room_id
        LEFT JOIN student s1 ON pr.student1 = s1.student_id
        LEFT JOIN student s2 ON pr.student2 = s2.student_id
        LEFT JOIN student s3 ON pr.student3 = s3.student_id
        LEFT JOIN student s4 ON pr.student4 = s4.student_id
        WHERE p.presentation_id = ? AND p.internal_evaluator_id = ?
    ");
    $sql->bind_param("ii", $presentation_id, $faculty_id);
    $sql->execute();
    
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $presentation_details = $result->fetch_assoc();
        // Collect student data
        $students = array_filter([
            ['id' => $presentation_details['student1_id'], 'username' => $presentation_details['student1_username']],
            ['id' => $presentation_details['student2_id'], 'username' => $presentation_details['student2_username']],
            ['id' => $presentation_details['student3_id'], 'username' => $presentation_details['student3_username']],
            ['id' => $presentation_details['student4_id'], 'username' => $presentation_details['student4_username']]
        ], fn($student) => !empty($student['id']));
    } else {
        echo "No presentation details found.";
    }

    // Fetch form details if form_id is passed
    if (isset($_GET['form_id'])) {
        $form_id = intval($_GET['form_id']);
        
        $form_query = "SELECT * FROM customized_form WHERE id = ?";
        $stmt = $conn->prepare($form_query);
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $form_result = $stmt->get_result();
        if ($form_result->num_rows > 0) {
            $form_row = $form_result->fetch_assoc();
            $form_title = htmlspecialchars($form_row['title']);
            $passing_marks = htmlspecialchars($form_row['passing_marks']);
            $total_marks = htmlspecialchars($form_row['total_marks']);
        } else {
            echo "No form details found for form_id: " . htmlspecialchars($form_id);
        }

        // Fetch form details
        $details_query = "SELECT * FROM form_detail WHERE form_id = ?";
        $stmt = $conn->prepare($details_query);
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $form_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "No form ID provided.";
    }
} else {
    echo "No presentation ID provided.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .card-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .btn-submit {
            margin-top: 20px;
        }
        .marks-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector('form');
        const totalMarksLimit = parseInt(document.getElementById('totalMarks').textContent);

        form.addEventListener('submit', function(event) {
            let isValid = true;

            // Clear previous error messages
            form.querySelectorAll('.error-message').forEach(function(errorSpan) {
                errorSpan.textContent = '';
            });

            // Validate total entered marks for each student
            <?php foreach ($students as $student): ?>
                let totalEnteredMarks_<?php echo $student['id']; ?> = 0;

                // Iterate over each input field for this student
                form.querySelectorAll('input[name^="marks"][name$="[<?php echo $student['id']; ?>]"]').forEach(function(input) {
                    const value = parseInt(input.value) || 0;
                    totalEnteredMarks_<?php echo $student['id']; ?> += value;
                    const errorSpan = input.nextElementSibling;

                    // Check if the input field is empty or less than 0
                    if (input.value.trim() === "" || value < 0) {
                        isValid = false;
                        errorSpan.textContent = "Please enter valid marks for <?php echo htmlspecialchars($student['username']); ?>.";
                    }

                    // Check if the total entered marks for this student exceed the total marks
                    if (totalEnteredMarks_<?php echo $student['id']; ?> > totalMarksLimit) {
                        isValid = false;
                        errorSpan.textContent = "The total entered marks exceed the maximum marks.";
                    }
                });

                // Update total marks display for the student
                document.getElementById('totalMarks_<?php echo $student['id']; ?>').textContent = totalEnteredMarks_<?php echo $student['id']; ?>;
            <?php endforeach; ?>

            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });

        // Update total marks dynamically as marks are entered
        document.querySelectorAll('.student-marks').forEach(function(input) {
            input.addEventListener('input', function() {
                const studentId = this.getAttribute('data-student-id');
                let totalMarks = 0;

                // Calculate total marks for the student
                document.querySelectorAll(`input[data-student-id="${studentId}"]`).forEach(function(markInput) {
                    totalMarks += parseInt(markInput.value) || 0;
                });

                // Update the footer total marks for this student
                document.getElementById('totalMarks_' + studentId).textContent = totalMarks;
            });
        });
    });
    </script>
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
                        <li class="breadcrumb-item"><a href="remarks.php">Presentation Marks</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Assign Marks</li>
                        </ol>
                    </nav>

                    <div class="container mt-5">
                        <!-- Presentation Details -->
                        <div class="card-container">
                            <h1 class="heading">Assign Marks</h1>

                            <?php if (!empty($presentation_details)): ?>
                                <h2 class="mt-3">Presentation Details</h2>
                                <p><strong>Title:</strong> <?php echo htmlspecialchars($presentation_details['project_title']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($presentation_details['date']); ?></p>
                                <p><strong>Time:</strong> <?php echo htmlspecialchars($presentation_details['time']); ?></p>
                                <p><strong>Room:</strong> <?php echo htmlspecialchars($presentation_details['room_number']); ?></p>
                                <p><strong>Students:</strong> <?php echo implode(', ', array_column($students, 'username')); ?></p>
                            <?php else: ?>
                                <p>No presentation details available.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Form Details -->
                        <?php if (!empty($form_title)): ?>
                            <div class="card-container mt-4">
                                <h2><?php echo htmlspecialchars($form_title); ?></h2>
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                                    <table class="table table-striped table-hover mt-3 align-middle">
                                        <thead>
                                            <tr>
                                                <th scope="col">S.No</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Max Marks</th>
                                                <?php foreach ($students as $student): ?>
                                                    <th scope="col"><?php echo htmlspecialchars($student['username']); ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($form_details as $index => $detail): ?>
                                                <tr>
                                                    <th scope="row"><?php echo $index + 1; ?></th>
                                                    <td><?php echo htmlspecialchars($detail['description']); ?></td>
                                                    <td><?php echo htmlspecialchars($detail['max_marks']); ?></td>
                                                    <?php foreach ($students as $student): ?>
                                                        <td>
                                                            <input type="number" name="marks[<?php echo intval($detail['id']); ?>][<?php echo intval($student['id']); ?>]" 
                                                                class="form-control student-marks" data-student-id="<?php echo intval($student['id']); ?>" 
                                                                min="0" max="<?php echo htmlspecialchars($detail['max_marks']); ?>">
                                                            <!-- Span to show error message -->
                                                            <span class="error-message" style="color: red; font-size: 12px;"></span>
                                                        </td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"><strong>Total Marks</strong></td>
                                                <td><?php echo htmlspecialchars($total_marks); ?></td>
                                                <?php foreach ($students as $student): ?>
                                                    <td>
                                                        <strong><span id="totalMarks_<?php echo intval($student['id']); ?>">0</span></strong>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="mb-3 mt-4">
    <label for="form_comment" class="form-label"><strong>Comment for the Form</strong></label>
    <textarea name="form_comment" id="form_comment" class="form-control" rows="3" placeholder="Write your comment for the entire form..."></textarea>
</div>

                                    <p><strong>Total Marks:</strong> <span id="totalMarks"><?php echo htmlspecialchars($total_marks); ?></span></p>
                                    <p><strong>Passing Marks:</strong> <?php echo htmlspecialchars($passing_marks); ?></p>
                                    <input type="hidden" name="presentation_id" value="<?php echo intval($presentation_id); ?>">
                                    <input type="hidden" name="form_id" value="<?php echo intval($form_id); ?>">
                                    <button type="submit" class="btn btn-primary btn-submit">Submit Marks</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <p>No form details available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>