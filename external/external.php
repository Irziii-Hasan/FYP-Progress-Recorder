<?php
include 'session_external_evaluator.php';
include 'config.php';

// Initialize variables
$presentation_id = '';
$form_id = '';
$form_title = '';
$form_details = [];
$presentation_details = [];
$students = [];
$passing_marks = '';
$total_marks = '';
$project_id = ''; // New variable to store project_id

// Get the external evaluator ID from the session
$external_id = $_SESSION['external_id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

      // Initialize an array to track total marks per student
      $student_totals = [];

      foreach ($marks_data as $description_id => $student_marks) {
          foreach ($student_marks as $student_id => $marks) {
              // Set external to the external_id from session
              $supervisor = 0;
              $internal = 0;
              $external = $external_id;
  
              // Insert or update individual marks into the database
              $insert_query = "
                  INSERT INTO marks (form_id, description_id, student_id, marks, supervisor, external, internal, project_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE marks = VALUES(marks)
              ";
              $stmt = $conn->prepare($insert_query);
              $stmt->bind_param("iiidiiii", $form_id, $description_id, $student_id, $marks, $supervisor, $external, $internal, $project_id);
              $stmt->execute();
  
              // Add marks to student's total
              if (!isset($student_totals[$student_id])) {
                  $student_totals[$student_id] = 0;
              }
              $student_totals[$student_id] += $marks;
          }
      }
  
      // Insert or update total marks for each student
      // Insert or update total marks for each student
      $form_comment = $_POST['form_comment'] ?? ''; // Get the single comment for the form
    foreach ($student_totals as $student_id => $total_marks) {
        $total_insert_query = "
            INSERT INTO external_total_student_marks (student_id, project_id, form_id, total_marks, external_id,comment) 
            VALUES (?, ?, ?, ?,?, ?) 
            ON DUPLICATE KEY UPDATE total_marks = VALUES(total_marks)
        ";
        $stmt = $conn->prepare($total_insert_query);
        $stmt->bind_param("iiiiis", $student_id, $project_id, $form_id, $total_marks, $external_id,$form_comment);
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
        WHERE p.presentation_id = ? AND p.external_evaluator_id = ?
    ");
    $sql->bind_param("ii", $presentation_id, $external_id);
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
        const totalMarks = parseInt(document.getElementById('totalMarks').textContent);
        const studentMarksInputs = document.querySelectorAll('.student-marks');
        
        // Function to calculate and update total marks for each student
        function updateStudentTotals() {
            // Create a dictionary to hold the sum of marks for each student
            const studentTotals = {};

            studentMarksInputs.forEach(function(input) {
                const studentId = input.getAttribute('data-student-id');
                const marks = parseInt(input.value) || 0;

                // If the student already has a total, add to it; otherwise, set it
                studentTotals[studentId] = (studentTotals[studentId] || 0) + marks;
            });

            // Update the total marks in the footer
            Object.keys(studentTotals).forEach(function(studentId) {
                document.getElementById('total_' + studentId).textContent = studentTotals[studentId];
            });
        }

        // Attach event listeners to each marks input field to trigger recalculation on change
        studentMarksInputs.forEach(function(input) {
            input.addEventListener('input', updateStudentTotals);
        });

        // Add form validation for total entered marks
        form.addEventListener('submit', function(event) {
            let totalEnteredMarks = 0;

            // Iterate over all the marks inputs and sum up their values
            studentMarksInputs.forEach(function(input) {
                totalEnteredMarks += parseInt(input.value) || 0;
            });

            // Check if the total entered marks exceed the total marks
            
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
                                                                class="form-control student-marks" min="0" max="<?php echo htmlspecialchars($detail['max_marks']); ?>"
                                                                data-student-id="<?php echo intval($student['id']); ?>">
                                                        </td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2">Total Marks</th>
                                                <th></th>
                                                <?php foreach ($students as $student): ?>
                                                    <th id="total_<?php echo intval($student['id']); ?>">0</th>
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
