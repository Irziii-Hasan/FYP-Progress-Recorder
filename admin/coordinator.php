<?php include 'session_admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>.heading {
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
        }</style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid" id="content">
        <div class="row">
            <div class="col-md-12">

                <!-- BREADCRUMBS -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Coordinators</li>
                    </ol>
                </nav>

                <?php
                include 'config.php';

                // Initialize search query
                $search_query = "";
                if (isset($_GET['search'])) {
                    $search_query = $_GET['search'];
                    // Sanitize the input
                    $search_query = $conn->real_escape_string($search_query);
                }

                // SQL to select coordinator records
                $sql = "SELECT c.coordinator_id, f.username AS CoordinatorName, b.BatchName, c.Year 
                        FROM coordinator c 
                        JOIN faculty f ON c.faculty_id = f.faculty_id 
                        JOIN batches b ON c.batch_id = b.BatchID";

                // Modify SQL query based on search query
                if (!empty($search_query)) {
                    $sql .= " WHERE f.username LIKE '%$search_query%' OR b.BatchName LIKE '%$search_query%' OR c.Year LIKE '%$search_query%'";
                }

                $result = $conn->query($sql);
                ?>

                <!-- COORDINATOR LIST -->
                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">Coordinator List</h1>
                        <a href="addCoordinator.php" class="btn btn-primary">Add Coordinator</a>
                    </div>
                    <!-- SEARCH FORM -->
                    <div class="row mb-3 justify-content-center">
                        <div class="col-md-6">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="input-group">
                                <span class="input-group-prepend">
                                    <div class="input-group-text"><i class="bi bi-search"></i></div>
                                </span>
                                <input class="form-control me-2" type="search" id="myInput" placeholder="Search" aria-label="Search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            </form>
                        </div>
                    </div>
                    <div class="table-container">
          <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>Coordinator Name</th>
                                <th>Batch</th>
                                <th>Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="myTable">
                            <?php
                            if ($result->num_rows > 0) {
                                // Counter for S No.
                                $s_no = 1;
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr id='row_" . $row["coordinator_id"] . "'>";
                                    echo "<td>" . $s_no . "</td>"; // Display S No.
                                    echo "<td>" . $row["CoordinatorName"] . "</td>";
                                    echo "<td>" . $row["BatchName"] . "</td>";
                                    echo "<td>" . $row["Year"] . "</td>";
                                    echo "<td>";
                                    // Edit icon
                                    echo "<a href='editCoordinator.php?id=" . $row["coordinator_id"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a> ";
                                    // Delete icon with AJAX call
                                    echo "<a href='javascript:void(0);' class='btn btn-danger btn-sm' onclick='deleteCoordinator(" . $row["coordinator_id"] . ")'><i class='bi bi-trash'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $s_no++; // Increment S No.
                                }
                            } else {
                                echo "<tr><td colspan='5'>No coordinator records found</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                        </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.getElementById("myInput").addEventListener("keyup", function() {
        var filter, table, rows;
        filter = document.getElementById("myInput").value.toUpperCase();
        table = document.getElementById("myTable");
        rows = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (var i = 0; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName("td");
            var found = false;
            for (var j = 0; j < cells.length && !found; j++) {
                var cell = cells[j];
                if (cell) {
                    if (cell.innerHTML.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                    }
                }
            }
            if (found) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    });

    function deleteCoordinator(coordinatorId) {
        if (confirm("Are you sure you want to delete this coordinator?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "deleteCoordinator.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Remove the row from the table
                            var row = document.getElementById("row_" + coordinatorId);
                            row.parentNode.removeChild(row);
                        } else {
                            alert("Error: " + response.message);
                        }
                    } else {
                        alert("An error occurred while processing the request.");
                    }
                }
            };
            xhr.send("id=" + coordinatorId);
        }
    }
</script>

</body>
</html>
