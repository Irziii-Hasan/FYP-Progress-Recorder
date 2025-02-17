<?php include 'session_admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
        </style>
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
                        <li class="breadcrumb-item active" aria-current="page">Rooms</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">Room List</h1>
                        <a href="addRoom.php" class="btn btn-primary">Add Room</a>
                    </div>
                    <div class="table-container">
                    <table class="table table-bordered table-striped">                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>Room Number</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Database connection 
                            include 'config.php';

                            // Check if the delete button is clicked
                            if(isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
                                // Sanitize the input to prevent SQL injection
                                $room_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

                                // SQL to delete the room from the rooms table
                                $sql_delete_room = "DELETE FROM rooms WHERE room_id = '$room_id'";
                                
                                if ($conn->query($sql_delete_room) === TRUE) {
                                    echo "<script>alert('Room deleted successfully.');</script>";
                                } else {
                                    echo "Error deleting room: " . $conn->error;
                                }
                            }

                            // SQL to select room records
                            $sql = "SELECT room_id, room_number FROM rooms";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["room_id"] . "</td>";
                                    echo "<td>" . $row["room_number"] . "</td>";
                                    echo "<td>";
                                    // Edit icon
                                    echo "<a href='editRoom.php?roomId=" . $row["room_id"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a> ";
                                    // Delete icon with confirmation
                                    echo "<a href='?delete_id=" . $row["room_id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this room?\")'><i class='bi bi-trash'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No room records found</td></tr>";
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

<!-- Font Awesome -->

</body>
</html>
