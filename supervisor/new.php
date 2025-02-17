<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fyp_progress_recorder";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all distinct form IDs from the marks table
$sql_forms = "SELECT DISTINCT form_id FROM marks";
$result_forms = $conn->query($sql_forms);

if ($result_forms->num_rows > 0) {
    // Loop through each form_id
    while ($row_form = $result_forms->fetch_assoc()) {
        $form_id = $row_form['form_id'];

        // Get all student IDs for the current form_id
        $sql_students = "SELECT DISTINCT student_id FROM marks WHERE form_id = ?";
        $stmt_students = $conn->prepare($sql_students);
        $stmt_students->bind_param("i", $form_id);
        $stmt_students->execute();
        $result_students = $stmt_students->get_result();

        while ($row_student = $result_students->fetch_assoc()) {
            $student_id = $row_student['student_id'];

            // Calculate total marks for each evaluator type (supervisor, external, internal)
            $sql_totals = "
                SELECT 
                    SUM(CASE WHEN supervisor IS NOT NULL THEN marks ELSE 0 END) AS supervisor_total,
                    SUM(CASE WHEN external IS NOT NULL THEN marks ELSE 0 END) AS external_total,
                    SUM(CASE WHEN internal IS NOT NULL THEN marks ELSE 0 END) AS internal_total
                FROM marks 
                WHERE form_id = ? AND student_id = ?
            ";
            $stmt_totals = $conn->prepare($sql_totals);
            $stmt_totals->bind_param("ii", $form_id, $student_id);
            $stmt_totals->execute();
            $result_totals = $stmt_totals->get_result();
            $row_totals = $result_totals->fetch_assoc();

            $supervisor_total = $row_totals['supervisor_total'];
            $external_total = $row_totals['external_total'];
            $internal_total = $row_totals['internal_total'];

            // Insert or update total marks in the student_marks_total table
            $sql_insert = "
                INSERT INTO student_marks_total (student_id, form_id, supervisor_total, external_total, internal_total)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    supervisor_total = VALUES(supervisor_total), 
                    external_total = VALUES(external_total), 
                    internal_total = VALUES(internal_total)
            ";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iisss", $student_id, $form_id, $supervisor_total, $external_total, $internal_total);
            $stmt_insert->execute();
        }

        $stmt_students->close();
    }

    echo "Marks total for all forms calculated and saved successfully.";
} else {
    echo "No forms found in the marks table.";
}

$conn->close();
?>
