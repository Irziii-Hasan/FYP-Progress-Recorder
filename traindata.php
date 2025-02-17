<?php
$log_file = 'C:/xampp/htdocs/code v3.20/logfile.txt'; // Update this path as needed
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Script started\n", FILE_APPEND);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fyp_progress_recorder";

// Connection banayein
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check karein
if ($conn->connect_error) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
    die("Connection failed: " . $conn->connect_error);
}

// Ensure karein ke training_data table maujood hai
$sql_create_table = "CREATE TABLE IF NOT EXISTS training_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNIQUE,
    assignments TEXT,
    assignment_status VARCHAR(255),
    meetings_attended INT,
    avg_meeting_feedback TEXT,
    presentations_attended INT,
    avg_presentation_feedback TEXT,
    current_progress INT
)";
if ($conn->query($sql_create_table) !== TRUE) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error creating table: " . $conn->error . "\n", FILE_APPEND);
    die("Error creating table: " . $conn->error);
}

// Data aggregate karein
$sql = "SELECT 
            p.id as project_id,  
            GROUP_CONCAT(a.assignment_name SEPARATOR ', ') as assignments,
            GROUP_CONCAT(sub.status SEPARATOR ', ') as assignment_status,
            COUNT(DISTINCT m.id) as meetings_attended,
            GROUP_CONCAT(m.feedback SEPARATOR '; ') as avg_meeting_feedback,
            COUNT(DISTINCT pr.presentation_id) as presentations_attended,
            GROUP_CONCAT(pf.feedback SEPARATOR '; ') as avg_presentation_feedback,
            MAX(pr.presentation_id) as current_progress
        FROM projects p
        LEFT JOIN submission sub ON p.id = sub.project_id
        LEFT JOIN assignments a ON sub.assignment_id = a.id
        LEFT JOIN meetings m ON p.id = m.project_id
        LEFT JOIN presentations pr ON p.id = pr.project_id
        LEFT JOIN presentation_feedback pf ON pr.presentation_id = pf.presentation_id
        GROUP BY p.id";  // Changed to p.id

// SQL errors check karein
if (!$result = $conn->query($sql)) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - SQL error: " . $conn->error . "\n", FILE_APPEND);
    die("SQL error: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Data ko training_data table me insert ya update karein
    while($row = $result->fetch_assoc()) {
        $sql_insert_update = "INSERT INTO training_data (project_id, assignments, assignment_status, meetings_attended, avg_meeting_feedback, presentations_attended, avg_presentation_feedback, current_progress)
                              VALUES ('".$row["project_id"]."', '".$row["assignments"]."', '".$row["assignment_status"]."', '".$row["meetings_attended"]."', '".$row["avg_meeting_feedback"]."', '".$row["presentations_attended"]."', '".$row["avg_presentation_feedback"]."', '".$row["current_progress"]."')
                              ON DUPLICATE KEY UPDATE 
                                  assignments = VALUES(assignments),
                                  assignment_status = VALUES(assignment_status),
                                  meetings_attended = VALUES(meetings_attended),
                                  avg_meeting_feedback = VALUES(avg_meeting_feedback),
                                  presentations_attended = VALUES(presentations_attended),
                                  avg_presentation_feedback = VALUES(avg_presentation_feedback),
                                  current_progress = VALUES(current_progress)";
        if ($conn->query($sql_insert_update) !== TRUE) {
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error: " . $sql_insert_update . " - " . $conn->error . "\n", FILE_APPEND);
            echo "Error: " . $sql_insert_update . "<br>" . $conn->error;
        }
    }
} else {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - 0 results\n", FILE_APPEND);
    echo "0 results";
}

$conn->close();
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Script ended\n", FILE_APPEND);
?>
