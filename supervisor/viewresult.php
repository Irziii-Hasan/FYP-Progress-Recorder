<?php
include 'session_supervisor.php'; // Make sure to include the correct session file for the supervisor

$supervisor_id = $_SESSION['faculty_id']; // Get the logged-in supervisor's ID

include 'config.php';

// Handle search input
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Default sorting column and order
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'total_marks';
$sort_order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'asc' : 'desc';

// Toggle sort order for next click
$toggle_order = $sort_order == 'asc' ? 'desc' : 'asc';

// Allowed columns for sorting
$allowed_columns = ['username', 'Proj_id', 'project_title', 'title', 'total', 'total_marks', 'gpa', 'grade'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'total_marks'; // Fallback to default column
}

// Modify query with sorting
$sql_result = "
    SELECT s.student_id, st.username, s.project_id, s.total_marks, s.gpa, s.grade, r.title, s.total, p.title AS project_title, p.project_id AS Proj_id
    FROM student_grand_totals s
    JOIN result_detail r ON s.result_id = r.result_id 
    JOIN student st ON s.student_id = st.student_id
    JOIN projects p ON s.project_id = p.id
    WHERE p.supervisor = '$supervisor_id' AND s.publish = 1
    AND (st.username LIKE '%$search%' OR p.title LIKE '%$search%')
    ORDER BY $sort_column $sort_order";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
                    <h1 class="heading">View Results</h1>
                    <!-- Search Bar (Centered) -->
                    <form method="GET" action="viewresult.php" class="mb-4">
                        <div class="d-flex justify-content-center">
                            <div class="col-4">
                                <input type="text" name="search" class="form-control form-control-md" placeholder="Search by Student Name or Project Title" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                            <div class="ms-2">
                                <button class="btn btn-dark" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        echo '<table class="table table-striped table-container">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>S No.</th>';
                        echo '<th><a href="?search=' . $search . '&sort=username&order=' . $toggle_order . '">Student Name ' . ($sort_column == 'username' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '<th><a href="?search=' . $search . '&sort=Proj_id&order=' . $toggle_order . '">Project ID ' . ($sort_column == 'Proj_id' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '<th><a href="?search=' . $search . '&sort=project_title&order=' . $toggle_order . '">Project Title ' . ($sort_column == 'project_title' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '<th><a href="?search=' . $search . '&sort=title&order=' . $toggle_order . '">Title ' . ($sort_column == 'title' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '<th><a href="?search=' . $search . '&sort=total&order=' . $toggle_order . '">Total Marks ' . ($sort_column == 'total' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '<th><a href="?search=' . $search . '&sort=total_marks&order=' . $toggle_order . '">Obtained Marks ' . ($sort_column == 'total_marks' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '<th><a href="?search=' . $search . '&sort=gpa&order=' . $toggle_order . '">GPA ' . ($sort_column == 'gpa' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '<th><a href="?search=' . $search . '&sort=grade&order=' . $toggle_order . '">Grade ' . ($sort_column == 'grade' ? ($sort_order == 'asc' ? '↑' : '↓') : '') . '</a></th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        $count = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo "<td>" . $count++ . "</td>";
                            echo '<td>' . $row['username'] . '</td>';
                            echo '<td>' . $row['Proj_id'] . '</td>';
                            echo '<td>' . $row['project_title'] . '</td>';
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
