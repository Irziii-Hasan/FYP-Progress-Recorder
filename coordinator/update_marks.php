<?php
include 'config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $marks_title = $_POST['marks_title'];
    $student_marks = $_POST['student_marks'];

    // Ensure the combination of student_id and marks_title is unique before updating
    $check_sql = "
        SELECT COUNT(*) as count
        FROM student_marks
        WHERE student_id = ? AND marks_title = ?
    ";

    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('ss', $student_id, $marks_title);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Update student marks in the database
        $update_sql = "
            UPDATE student_marks
            SET student_marks = ?
            WHERE student_id = ? AND marks_title = ?
        ";

        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('iss', $student_marks, $student_id, $marks_title);

        if ($stmt->execute()) {
            header('Location: viewMarks.php'); // Redirect to the view marks page
            exit();
        } else {
            echo "Error updating marks: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Record not found or unique constraint violated.";
    }
}

$conn->close();
?>
