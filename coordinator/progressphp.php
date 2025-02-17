<?php
// Assuming you have a connection to the database
$servername = "localhost"; // Update with your server name
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "fyp_progress_recorder"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch project data
$sql = "SELECT id, title,description FROM projects";
$result = $conn->query($sql);

// Initialize variables for overall progress
$overall_progress = 0;
$meetings_progress = 0;
$assignments_progress = 0;
$presentation_progress = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $selected_project_id = $_POST['id'];

    // Get total number of assignments for the selected project
    $assignments_sql = "SELECT COUNT(*) AS total_assignments FROM assignments";
    $assignments_result = $conn->query($assignments_sql);
    $assignments_data = $assignments_result->fetch_assoc();
    $total_assignments = $assignments_data['total_assignments'];

    // Get number of completed submissions for the selected project
    $completed_sql = "SELECT COUNT(*) AS completed_assignments FROM submission WHERE project_id = '$selected_project_id'";
    $completed_result = $conn->query($completed_sql);
    $completed_data = $completed_result->fetch_assoc();
    $completed_assignments = $completed_data['completed_assignments'];

    // Calculate pending assignments
    $pending_assignments = $total_assignments - $completed_assignments;

    // Get project details along with student and supervisor usernames
    $project_sql = "
    SELECT 
    p.project_id, 
    p.title, 
    p.description,
    CONCAT(s1.username, ' (', s1.seat_number, ')') AS student1, 
    CONCAT(s2.username, ' (', s2.seat_number, ')') AS student2, 
    CONCAT(s3.username, ' (', s3.seat_number, ')') AS student3, 
    CONCAT(s4.username, ' (', s4.seat_number, ')') AS student4, 
    fs.username AS supervisor, 
    fcs.username AS co_supervisor, 
    p.external_supervisor
FROM projects p
LEFT JOIN student s1 ON p.student1 = s1.student_id
LEFT JOIN student s2 ON p.student2 = s2.student_id
LEFT JOIN student s3 ON p.student3 = s3.student_id
LEFT JOIN student s4 ON p.student4 = s4.student_id
LEFT JOIN faculty fs ON p.supervisor = fs.faculty_id
LEFT JOIN faculty fcs ON p.co_supervisor = fcs.faculty_id
WHERE p.id = '$selected_project_id';
";

    $project_result = $conn->query($project_sql);
    $project_details = $project_result->fetch_assoc();

    // Get project details along with meetings
    $meetings_sql = "
    SELECT m.id, m.title, m.feedback, m.attendance_status
    FROM meetings m
    WHERE m.project_id = '$selected_project_id'
    ORDER BY m.date";

    $meetings_result = $conn->query($meetings_sql);

    $total_meetings = 0;
    $attended_meetings = 0;
    $not_attended_meetings = 0;

    $meeting_rows = '';

    // Generate the rows for meetings and calculate counts
    while ($row = $meetings_result->fetch_assoc()) {
        $total_meetings++;
        $feedback = $row['feedback'] ? $row['feedback'] : 'Not Attended';

        if ($row['attendance_status'] == 'Present') {
            $attended_meetings++;
        } else {
            $not_attended_meetings++;
        }

        $meeting_rows .= "
        <tr>
            <td>{$row['title']}</td>
            <td>{$feedback}</td>
            <td>{$row['attendance_status']}</td>
        </tr>";
    }
    $presentation_progress_sql = "
    SELECT 
        p.form_id, 
        f1.username AS internal_evaluator_name, 
        e1.name AS external_evaluator_name, 
        c.title AS form_title,
        c.total_marks,  -- Fetch the total_marks from the customized_form table
        COALESCE(AVG(tsm.total_marks), 0) AS internal_average_marks,  -- Internal marks
        MAX(tsm.comment) AS internal_comment,  -- Single internal comment
        COALESCE(AVG(etsm.total_marks), 0) AS external_average_marks,  -- External marks
        MAX(etsm.comment) AS external_comment  -- Single external comment
    FROM presentations p
    LEFT JOIN faculty f1 ON p.internal_evaluator_id = f1.faculty_id
    LEFT JOIN external e1 ON p.external_evaluator_id = e1.external_id
    LEFT JOIN customized_form c ON p.form_id = c.id
    LEFT JOIN total_student_marks tsm 
        ON p.form_id = tsm.form_id AND p.project_id = tsm.project_id  -- Internal marks table
    LEFT JOIN external_total_student_marks etsm 
        ON p.form_id = etsm.form_id AND p.project_id = etsm.project_id  -- External marks table
    WHERE p.project_id = '$selected_project_id'
    GROUP BY p.form_id, f1.username, e1.name, c.title, c.total_marks";

    
    $presentation_progress_result = $conn->query($presentation_progress_sql);
$presentation_progress_rows = '';


// Variables for calculating presentation progress
$total_internal_progress = 0;
$total_external_progress = 0;
$presentations_count = 0;

while ($row = $presentation_progress_result->fetch_assoc()) {
    // Calculate progress percentages
    $internal_progress_percentage = ($row['total_marks'] > 0) ? (100 * $row['internal_average_marks'] / $row['total_marks']) : 0;
    $external_progress_percentage = ($row['total_marks'] > 0) ? (100 * $row['external_average_marks'] / $row['total_marks']) : 0;

    // Internal Evaluator Row
    $presentation_progress_rows .= "
    <tr>
        <td>{$row['form_title']}</td>  <!-- Display the form title -->
        <td>{$row['internal_evaluator_name']} (Internal)</td>  <!-- Internal evaluator name with role -->
        <td>{$row['total_marks']}</td>  <!-- Display total marks -->
        <td>" . number_format($row['internal_average_marks'], 2) . "</td>  <!-- Internal average marks -->
                <td>{$row['internal_comment']}</td>  <!-- Internal comment -->

        <td>
            <div class='progress'>
                <div class='progress-bar bg-primary' role='progressbar' style='width: {$internal_progress_percentage}%;' aria-valuenow='{$internal_progress_percentage}' aria-valuemin='0' aria-valuemax='100'>{$internal_progress_percentage}%</div>
            </div>
        </td>  <!-- Internal progress bar -->
    </tr>";

    // External Evaluator Row (Only if external evaluator exists)
    if (!empty($row['external_evaluator_name'])) {
        $presentation_progress_rows .= "
        <tr>
            <td>{$row['form_title']}</td>  <!-- Display the form title -->
            <td>{$row['external_evaluator_name']} (External)</td>  <!-- External evaluator name with role -->
            <td>{$row['total_marks']}</td>  <!-- Display total marks -->
            <td>" . number_format($row['external_average_marks'], 2) . "</td>  <!-- External average marks -->
                        <td>{$row['external_comment']}</td>  <!-- External comment -->
            <td>
                <div class='progress'>
                    <div class='progress-bar bg-success' role='progressbar' style='width: {$external_progress_percentage}%;' aria-valuenow='{$external_progress_percentage}' aria-valuemin='0' aria-valuemax='100'>{$external_progress_percentage}%</div>
                </div>
            </td>  <!-- External progress bar -->
        </tr>";
    }

    // Accumulate for overall progress
    if ($row['total_marks'] > 0) {
        $total_internal_progress += $internal_progress_percentage;
        if (!empty($row['external_evaluator_name'])) {
            $total_external_progress += $external_progress_percentage;
        }
        $presentations_count++;
    }
}


// Calculate overall averages if needed
$average_internal_progress = ($presentations_count > 0) ? ($total_internal_progress / $presentations_count) : 0;
$average_external_progress = ($presentations_count > 0) ? ($total_external_progress / $presentations_count) : 0;

    // Calculate Presentation Progress
$presentation_progress = ($average_internal_progress + $average_external_progress) / 2;

// Calculate Overall Progress

    // Calculate average presentation progress

    // Calculate Meetings Progress
    $meetings_progress = ($total_meetings > 0) ? ($attended_meetings / $total_meetings) * 100 : 0;

    // Calculate Assignments Progress
    $assignments_progress = ($total_assignments > 0) ? ($completed_assignments / $total_assignments) * 100 : 0;

    // Combine all progress metrics equally
    $overall_progress = ($meetings_progress + $assignments_progress + $presentation_progress) / 3;
}
?>