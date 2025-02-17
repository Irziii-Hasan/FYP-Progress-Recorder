<?php
include 'session_admin.php'; // Include session handling
include 'config.php';

// Initialize variables for error handling and form prefilling
$coordinatorError = "";
$batchError = "";
$coordinatorValue = "";
$batchValue = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $coordinator_id = $_POST['coordinator_id'];
    $batch_id = $_POST['batch_id'];
    $year = date("Y");

    if (empty($coordinator_id)) {
        $coordinatorError = "Coordinator is required";
    }

    if (empty($batch_id)) {
        $batchError = "Batch is required";
    }

    if (empty($coordinatorError) && empty($batchError)) {
        // Check if the coordinator is already enrolled in the same batch for the current year
        $stmt = $conn->prepare("SELECT COUNT(*) FROM coordinator WHERE faculty_id = ? AND year = ?");
        $stmt->bind_param("is", $coordinator_id, $year);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // If the coordinator is already enrolled in the current year
        if ($count > 0) {
            // Check if the coordinator is already enrolled in the selected batch or all batches
            if ($batch_id == "all") {
                $stmt = $conn->prepare("SELECT batch_id FROM coordinator WHERE faculty_id = ? AND year = ?");
                $stmt->bind_param("is", $coordinator_id, $year);
                $stmt->execute();
                $stmt->bind_result($existing_batch_id);
                $existing_batches = [];
                while ($stmt->fetch()) {
                    $existing_batches[] = $existing_batch_id;
                }
                $stmt->close();

                if (!empty($existing_batches)) {
                    $batchError = "Coordinator is already enrolled in some batches for this year.";
                } else {
                    // If the coordinator is not enrolled in any batches, proceed with enrollment
                    $sql = "INSERT INTO coordinator (faculty_id, batch_id, year) SELECT ?, BatchID, ? FROM batches";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $coordinator_id, $year);
                    $stmt->execute();
                    $stmt->close();
                    echo '<script>alert("Coordinator assigned successfully."); window.location.href = "coordinator.php";</script>';
                }
            } else {
                // Check if the coordinator is already enrolled in the specific batch
                $stmt = $conn->prepare("SELECT COUNT(*) FROM coordinator WHERE faculty_id = ? AND batch_id = ? AND year = ?");
                $stmt->bind_param("iis", $coordinator_id, $batch_id, $year);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count > 0) {
                    $coordinatorError = "Coordinator is already enrolled in this batch for the current year.";
                } else {
                    // Insert into coordinator table
                    $stmt = $conn->prepare("INSERT INTO coordinator (faculty_id, batch_id, year) VALUES (?, ?, ?)");
                    $stmt->bind_param("iis", $coordinator_id, $batch_id, $year);
                    $stmt->execute();
                    $stmt->close();

                    echo '<script>alert("Coordinator assigned successfully."); window.location.href = "coordinator.php";</script>';
                }
            }
        } else {
            // Insert into coordinator table if not enrolled in the current year
            if ($batch_id == "all") {
                $sql = "INSERT INTO coordinator (faculty_id, batch_id, year) SELECT ?, BatchID, ? FROM batches";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $coordinator_id, $year);
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $conn->prepare("INSERT INTO coordinator (faculty_id, batch_id, year) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $coordinator_id, $batch_id, $year);
                $stmt->execute();
                $stmt->close();
            }
            echo '<script>alert("Coordinator assigned successfully."); window.location.href = "coordinator.php";</script>';
        }
    }
}

// Get faculty options
$facultyOptions = "";
$sql = "SELECT faculty_id, username FROM faculty";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $facultyOptions .= "<option value='{$row['faculty_id']}'>{$row['username']}</option>";
    }
}

// Get batch options
$batchOptions = "<option value='all'>All</option>";
$sql = "SELECT BatchID, BatchName FROM batches";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $batchOptions .= "<option value='{$row['BatchID']}'>{$row['BatchName']}</option>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Assign Coordinator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
    <style>
        .form-all {
            padding: 20px 30px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
        }
        .form-heading {
            color: #0a4a91;
            font-weight: 700;
        }
        label {
            font-weight: 500;
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
                        <li class="breadcrumb-item"><a href="coordinator.php">Coordinator</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Assign Coordinator</li>
                    </ol>
                </nav>
                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Assign Coordinator</h2>
                    <form action="addcoordinator.php" method="post">
                        <div class="mb-3 mt-3">
                            <label for="coordinator_id">Coordinator Name:</label>
                            <select class="form-select" id="coordinator_id" name="coordinator_id" required>
                                <option value="" selected>Select Coordinator</option>
                                <?php echo $facultyOptions; ?>
                            </select>
                            <span class="error" style="color: red;"><?php echo $coordinatorError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="batch_id">Batch:</label>
                            <select class="form-select" id="batch_id" name="batch_id" required>
                                <option value="" selected>Select Batch</option>
                                <?php echo $batchOptions; ?>
                            </select>
                            <span class="error" style="color: red;"><?php echo $batchError; ?></span>
                        </div>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="coordinator.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
                <script>
                    $(document).ready(function() {
                        $('#coordinator_id').select2();
                        $('#batch_id').select2();
                    });
                </script>
            </div>
        </div>
    </div>
</div>
</body>
</html>
