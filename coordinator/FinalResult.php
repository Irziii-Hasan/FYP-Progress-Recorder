<?php
// Database connection credentials
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "fyp_progress_recorder";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve batch parameter from URL or define it
$batch = isset($_GET['batch']) ? $_GET['batch'] : ''; // Adjust as needed

// Fetch average marks for all forms within the same duration
// Adjust SQL query to fit your schema
$sql = "
    SELECT 
        s.username AS student_name,
        f.username AS supervisor_name, -- Corrected column name
        p.project_id AS project_id, -- Adjusted column name
        -- Calculate average marks for Mid
        AVG(CASE WHEN cf.title = 'FYP-I MID' THEN m.marks ELSE NULL END) AS avg_mid,
        -- Calculate average marks for Terminal
        AVG(CASE WHEN cf.title = 'FYP-I Terminal' THEN m.marks ELSE NULL END) AS avg_terminal,
        -- Calculate total formula
        (AVG(CASE WHEN cf.title = 'FYP-I MID' THEN m.marks ELSE NULL END) * 0.4 +
         AVG(CASE WHEN cf.title = 'FYP-I Terminal' THEN m.marks ELSE NULL END) * 0.6) AS total_formula
    FROM 
        marks m
    LEFT JOIN 
        student s ON m.student_id = s.student_id
    LEFT JOIN 
        form_detail fd ON m.description_id = fd.id
    LEFT JOIN 
        customized_form cf ON m.form_id = cf.id
    LEFT JOIN 
        course_durations cd ON cf.duration_id = cd.id
    LEFT JOIN 
        projects p ON m.project_id = p.project_id
    LEFT JOIN 
        faculty f ON m.supervisor = f.faculty_id -- Corrected column name
    WHERE 
        cd.title = '$batch'
    GROUP BY 
        s.username, f.username, p.project_id
    ORDER BY 
        p.project_id ASC, s.username ASC;
";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marks Distribution</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4 text-center">Marks Distribution</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Supervisor Name</th>
                    <th>Project ID</th>
                    <th>Average Mid</th>
                    <th>Average Terminal</th>
                    <th>Total Formula</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['project_id']); ?></td>
                            <td><?php echo number_format($row['avg_mid'], 2); ?></td>
                            <td><?php echo number_format($row['avg_terminal'], 2); ?></td>
                            <td><?php echo number_format($row['total_formula'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS (Optional for additional functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
