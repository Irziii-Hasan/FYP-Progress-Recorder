<?php
include 'session_student.php';

$student_id = $_SESSION['student_id']; // Get the logged-in student's ID

include 'config.php';

// Check if results are published for the student and fetch title from result_detail
$sql_result = "
    SELECT s.student_id, st.username, s.project_id, s.total_marks, s.gpa, s.grade, r.title, s.total, p.title AS project_title, p.project_id AS Proj_id
    FROM student_grand_totals s
    JOIN result_detail r ON s.result_id = r.result_id 
    JOIN student st ON s.student_id = st.student_id  -- Joining with the students table to get username
    JOIN projects p ON s.project_id = p.id
    WHERE s.student_id = '$student_id' AND s.publish = 1
    ORDER BY s.total_marks DESC"; // Sort in descending order by total_marks

$result = mysqli_query($conn, $sql_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
          .heading {
            color: #0a4a91;
            font-weight: 700;
        }
        .table-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
            margin-top: 20px;
        }
        .btn-view, .btn-add {
            margin-right: 10px;
        }
        table th, table td {
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
            color: #0a4a91;
        }
        .table-bordered {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        .no-result {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid" id="content">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Results</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <h1 class="heading">View Result</h1>

                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        echo '<table class="table table-striped table-container">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>S No. </th>';
                        echo '<th>Student Name</th>';    // Added Username header
                        echo '<th>Project ID</th>';  // Added Project ID header
                        echo '<th>Project Title</th>';
                        echo '<th>Title</th>';
                        echo '<th>Total Marks</th>';
                        echo '<th>Obtained Marks</th>';
                        echo '<th>GPA</th>';
                        echo '<th>Grade</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        $count = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo "<td>" . $count++ . "</td>";
                            echo '<td>' . $row['username'] . '</td>';  // Display Username
                            echo '<td>' . $row['Proj_id'] . '</td>'; // Display Project ID
                            echo '<td>' . $row['project_title'].'</td>';
                            echo '<td>' . $row['title'] . '</td>';
                            echo '<td>' . $row['total'] . '</td>';
                            echo '<td>' . $row['total_marks'] . '</td>';
                            echo '<td>' . $row['gpa'] . '</td>';
                            echo '<td>' . $row['grade'] . '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p class="no-result">Results are not published yet or you have no results.</p>';
                    }

                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
