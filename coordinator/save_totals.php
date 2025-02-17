<?php
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['totals'])) {
    // Fetch or insert result_id from result_detail table only once
    $title = $conn->real_escape_string($data['totals'][0]['title']); // Get title from the first item
    $grand_total_marks = $conn->real_escape_string($data['totals'][0]['grand_total_marks']); // Get grand total marks
    $result_id = null;

    // Check if result_id for this title already exists
    $sql_check_result = "SELECT result_id FROM result_detail WHERE title = '$title' LIMIT 1";
    $result_check = $conn->query($sql_check_result);

    if ($result_check->num_rows > 0) {
        $result_row = $result_check->fetch_assoc();
        $result_id = $result_row['result_id'];
    } else {
        // Insert a new record into the result_detail table
        $sql_insert_result = "INSERT INTO result_detail (title, created_at) VALUES ('$title', NOW())";
        if ($conn->query($sql_insert_result) === TRUE) {
            $result_id = $conn->insert_id; // Get the newly inserted result_id
        }
    }

    if ($result_id) {
        // Loop through each student's totals and insert/update the student_grand_totals table
        foreach ($data['totals'] as $total) {
            $student_id = $conn->real_escape_string($total['student_id']);
            $project_id = $conn->real_escape_string($total['project_id']);
            $total_marks = $conn->real_escape_string($total['total_marks']); // Save grand total marks
            $gpa =  $conn->real_escape_string($total['gpa']); 
            $grade = $conn->real_escape_string($total['grade']);
            
            // Fetch the primary key 'id' using 'project_id'
            $sql_get_id = "SELECT id FROM projects WHERE project_id = '$project_id' LIMIT 1";
            $result_get_id = $conn->query($sql_get_id);

            if ($result_get_id->num_rows > 0) {
                $row = $result_get_id->fetch_assoc();
                $primary_id = $row['id'];

                // Insert or update the student_grand_totals table with result_id, total_marks, gpa, and grade
                $sql = "INSERT INTO student_grand_totals (student_id, project_id, total_marks, result_id, gpa, grade, total) 
                        VALUES ('$student_id', '$primary_id', '$total_marks', '$result_id', '$gpa', '$grade', '$grand_total_marks') 
                        ON DUPLICATE KEY UPDATE total_marks = '$total_marks', result_id = '$result_id', gpa = '$gpa', grade = '$grade'";

                $conn->query($sql);
            }
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving result_id.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No data received']);
}
?>
