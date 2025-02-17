<?php
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

// Ensure the training_data table exists
$sql_create_table = "CREATE TABLE IF NOT EXISTS training_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    project_id VARCHAR(255),
    degree_program VARCHAR(255),
    academic_year VARCHAR(255),
    assignment_name VARCHAR(255),
    assignment_deadline DATE,
    assignment_status VARCHAR(255),
    meetings_attended INT,
    avg_meeting_feedback TEXT,
    presentations_attended INT,
    avg_presentation_feedback TEXT,
    current_progress INT
)";
if ($conn->query($sql_create_table) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Aggregate Data
$sql = "SELECT 
            s.student_id,
            p.project_id,
            s.degree_program,
            s.academic_year,
            a.assignment_name,
            a.deadline as assignment_deadline,
            sub.status as assignment_status,
            COUNT(DISTINCT m.id) as meetings_attended,
            GROUP_CONCAT(m.feedback SEPARATOR '; ') as avg_meeting_feedback,
            COUNT(DISTINCT pr.presentation_id) as presentations_attended,
            GROUP_CONCAT(pf.feedback SEPARATOR '; ') as avg_presentation_feedback,
            MAX(pr.presentation_id) as current_progress
        FROM student s
        LEFT JOIN projects p ON s.student_id = p.student1 OR s.student_id = p.student2 OR s.student_id = p.student3 OR s.student_id = p.student4
        LEFT JOIN submission sub ON p.id = sub.project_id
        LEFT JOIN assignments a ON sub.assignment_id = a.id
        LEFT JOIN meetings m ON p.id = m.project_id
        LEFT JOIN presentations pr ON p.id = pr.project_id
        LEFT JOIN presentation_feedback pf ON pr.presentation_id = pf.presentation_id
        GROUP BY s.student_id, p.project_id, s.degree_program, s.academic_year, a.assignment_name, a.deadline, sub.status";

// Check for SQL errors
if (!$result = $conn->query($sql)) {
    die("SQL error: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Store aggregated data in training_data table
    while($row = $result->fetch_assoc()) {
        $sql_insert = "INSERT INTO training_data (student_id, project_id, degree_program, academic_year, assignment_name, assignment_deadline, assignment_status, meetings_attended, avg_meeting_feedback, presentations_attended, avg_presentation_feedback, current_progress)
                        VALUES ('".$row["student_id"]."', '".$row["project_id"]."', '".$row["degree_program"]."', '".$row["academic_year"]."', '".$row["assignment_name"]."', '".$row["assignment_deadline"]."', '".$row["assignment_status"]."', '".$row["meetings_attended"]."', '".$row["avg_meeting_feedback"]."', '".$row["presentations_attended"]."', '".$row["avg_presentation_feedback"]."', '".$row["current_progress"]."')";
        if ($conn->query($sql_insert) === TRUE) {
            echo "";
        } else {
            echo "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    }
} else {
    echo "0 results";
}

// Retrieve data for HTML table
$sql_display = "SELECT * FROM training_data";
$result_display = $conn->query($sql_display);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Data View</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Training Data View</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student ID</th>
                <th>Project ID</th>
                <th>Degree Program</th>
                <th>Academic Year</th>
                <th>Assignment Name</th>
                <th>Assignment Deadline</th>
                <th>Assignment Status</th>
                <th>Meetings Attended</th>
                <th>Avg Meeting Feedback</th>
                <th>Presentations Attended</th>
                <th>Avg Presentation Feedback</th>
                <th>Current Progress</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_display->num_rows > 0) {
                while($row = $result_display->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["student_id"] . "</td>
                        <td>" . $row["project_id"] . "</td>
                        <td>" . $row["degree_program"] . "</td>
                        <td>" . $row["academic_year"] . "</td>
                        <td>" . $row["assignment_name"] . "</td>
                        <td>" . $row["assignment_deadline"] . "</td>
                        <td>" . $row["assignment_status"] . "</td>
                        <td>" . $row["meetings_attended"] . "</td>
                        <td>" . $row["avg_meeting_feedback"] . "</td>
                        <td>" . $row["presentations_attended"] . "</td>
                        <td>" . $row["avg_presentation_feedback"] . "</td>
                        <td>" . $row["current_progress"] . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='13'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
