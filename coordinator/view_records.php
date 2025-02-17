<?php
include 'config.php'; // Database connection

// Get distinct titles from the student_grand_totals table
$sql_titles = "SELECT DISTINCT title FROM student_grand_totals";
$result_titles = $conn->query($sql_titles);

// Check if a title is selected
$selected_title = isset($_GET['title']) ? $_GET['title'] : '';

// Fetch records based on the selected title
$records = [];
if ($selected_title) {
    $sql_records = "SELECT s.username, sgt.total_marks
                    FROM student_grand_totals sgt
                    JOIN student s ON sgt.student_id = s.student_id
                    WHERE sgt.title = '$selected_title'";
    $result_records = $conn->query($sql_records);

    // Fetch records into an array
    if ($result_records->num_rows > 0) {
        while ($row = $result_records->fetch_assoc()) {
            $records[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Records</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Select a Title to View Records</h2>
        
        <div class="mb-4">
            <ul class="list-group">
                <?php while ($row = $result_titles->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <a href="?title=<?php echo urlencode($row['title']); ?>"><?php echo htmlspecialchars($row['title']); ?></a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <?php if ($selected_title): ?>
            <h3>Records for Title: <?php echo htmlspecialchars($selected_title); ?></h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Student Name</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php foreach ($records as $index => $record): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($record['username']); ?></td>
                                <td><?php echo htmlspecialchars($record['total_marks']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No records found for this title.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
