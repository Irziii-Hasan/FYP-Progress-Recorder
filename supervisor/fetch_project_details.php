<?php
include 'session_supervisor.php';
header('Content-Type: application/json'); // Ensure content type is JSON

// Database connection
include 'config.php';

if (isset($_GET['project_id'])) {
    $project_id = intval($_GET['project_id']); // Convert to integer to prevent SQL injection

    // SQL query to fetch project details along with student and supervisor usernames
    $sql = "SELECT 
                p.title,
                s1.username as student1,
                s2.username as student2,
                s3.username as student3,
                s4.username as student4,
                f1.username as supervisor
            FROM projects p
            LEFT JOIN student s1 ON p.student1 = s1.student_id
            LEFT JOIN student s2 ON p.student2 = s2.student_id
            LEFT JOIN student s3 ON p.student3 = s3.student_id
            LEFT JOIN student s4 ON p.student4 = s4.student_id
            LEFT JOIN faculty f1 ON p.supervisor = f1.faculty_id
            WHERE p.project_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $projectDetails = $result->fetch_assoc();
        echo json_encode($projectDetails);
    } else {
        echo json_encode(['error' => 'No project found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No project ID provided']);
}

$conn->close();
?>
