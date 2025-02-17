<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database connection
    $conn = new mysqli("localhost", "root", "", "fyp_progress_recorder");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $project_id = $_GET['project_id'] ?? '';

    if ($project_id) {
        // Query to fetch project progress
        $sql = "SELECT 
                    pf.*, 
                    pres.batch, 
                    pres.date AS presentation_date, 
                    pres.time AS presentation_time, 
                    s1.username AS student1, 
                    s2.username AS student2, 
                    s3.username AS student3, 
                    s4.username AS student4, 
                    f.username AS supervisor, 
                    e.name AS external_evaluator,
                    m.title AS meeting_title,
                    m.description AS meeting_description,
                    m.date AS meeting_date,
                    m.time AS meeting_time,
                    m.marks AS meeting_marks,
                    m.feedback AS meeting_feedback,
                    m.attendance_status AS meeting_attendance
                FROM 
                    presentation_feedback pf
                JOIN 
                    presentations pres ON pf.presentation_id = pres.presentation_id
                JOIN 
                    projects p ON pres.project_id = p.id
                LEFT JOIN 
                    student s1 ON p.student1 = s1.student_id
                LEFT JOIN 
                    student s2 ON p.student2 = s2.student_id
                LEFT JOIN 
                    student s3 ON p.student3 = s3.student_id
                LEFT JOIN 
                    student s4 ON p.student4 = s4.student_id
                LEFT JOIN 
                    faculty f ON p.supervisor = f.faculty_id
                LEFT JOIN 
                    external e ON pres.external_evaluator_id = e.external_id
                LEFT JOIN 
                    meetings m ON p.id = m.project_id
                WHERE 
                    p.id = '$project_id'";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $progress_data = [];
            while ($row = $result->fetch_assoc()) {
                $progress_data[] = $row;
            }
            echo json_encode($progress_data);
        } else {
            echo json_encode([]);  // No data found
        }
    } else {
        echo json_encode([]);  // No project ID provided
    }

    $conn->close();
}
