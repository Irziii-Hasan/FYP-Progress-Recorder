<?php
include 'session_supervisor.php';
include 'config.php';

// Initialize variables
$presentation_id = '';
$form_id = '';
$project_id = '';

// Get the faculty ID from the session (internal evaluator)
$faculty_id = $_SESSION['faculty_id'] ?? '';

// Check if the form is submitted for updating marks
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $presentation_id = intval($_POST['presentation_id']);
    $form_id = intval($_POST['form_id']);
    $marks_data = $_POST['marks'] ?? [];

    // Array to hold the total marks for each student
    $student_total_marks = [];

    // Update marks data
    foreach ($marks_data as $description_id => $student_marks) {
        foreach ($student_marks as $student_id => $marks) {
            // Update individual marks in the marks table (ensuring only integers are saved)
            $update_query = "
                UPDATE marks 
                SET marks = ? 
                WHERE form_id = ? AND description_id = ? AND student_id = ? AND internal = ? AND project_id = ?
            ";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("diiiii", $marks, $form_id, $description_id, $student_id, $faculty_id, $project_id);
            $stmt->execute();
        }
    }

    echo "<script>alert('Marks have been updated successfully!');</script>";
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
            s4.student_id AS student4_id, s4.username AS student4_username,
            p.form_id, p.project_id
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
        
        // Extract the form_id and project_id from the presentation
        $form_id = $presentation_details['form_id'];
        $project_id = $presentation_details['project_id'];
    } else {
        echo "No presentation details found.";
    }

    // Fetch form details if form_id is passed
    if (!empty($form_id)) {
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

        // Fetch existing marks for the logged-in internal evaluator, matching form_id and project_id
        $marks_query = "
            SELECT description_id, student_id, marks 
            FROM marks 
            WHERE form_id = ? AND internal = ? AND project_id = ?
        ";
        $stmt = $conn->prepare($marks_query);
        $stmt->bind_param("iii", $form_id, $faculty_id, $project_id);
        $stmt->execute();
        $existing_marks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Organize marks by description_id and student_id for easier access
        $marks_data = [];
        foreach ($existing_marks as $mark) {
            $marks_data[$mark['description_id']][$mark['student_id']] = (int)$mark['marks']; // Cast to integer
        }

        // Fetch form details
        $details_query = "SELECT * FROM form_detail WHERE form_id = ?";
        $stmt = $conn->prepare($details_query);
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $form_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "No form ID available.";
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
    <title>Edit Internal Marks</title>
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
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-5">
        <!-- Presentation Details -->
        <div class="card-container">
            <h1 class="heading">Edit Internal Marks</h1>

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
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($detail['description']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['max_marks']); ?></td>
                                    <?php foreach ($students as $student): ?>
                                        <td>
                                            <input type="number" name="marks[<?php echo intval($detail['id']); ?>][<?php echo intval($student['id']); ?>]" 
                                                value="<?php echo isset($marks_data[$detail['id']][$student['id']]) ? intval($marks_data[$detail['id']][$student['id']]) : ''; ?>" 
                                                min="0" max="<?php echo intval($detail['max_marks']); ?>">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="presentation_id" value="<?php echo htmlspecialchars($presentation_id); ?>">
                    <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
                    <button type="submit" class="btn btn-primary">Save Marks</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
