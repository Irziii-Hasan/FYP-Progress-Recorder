<?php
include 'session_admin.php'; // Include session handling

// Initialize variables for error handling and form prefilling
$coordinatorError = "";
$batchError = "";
$coordinatorValue = "";
$batchValue = "";

include 'config.php';

// Fetching faculty options
$facultyOptions = "";
$sql = "SELECT faculty_id, username FROM faculty";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $selected = ($row['faculty_id'] == $coordinatorValue) ? "selected" : "";
        $facultyOptions .= "<option value='{$row['faculty_id']}' $selected>{$row['username']}</option>";
    }
}

// Fetching batch options
$batchOptions = "";
$sql = "SELECT BatchID, BatchName FROM batches";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $selected = ($row['BatchID'] == $batchValue) ? "selected" : "";
        $batchOptions .= "<option value='{$row['BatchID']}' $selected>{$row['BatchName']}</option>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $coordinator_id = $_POST['coordinator_id'];
    $batch_id = $_POST['batch_id'];

    if (empty($coordinator_id)) {
        $coordinatorError = "Coordinator is required";
    }

    if (empty($batch_id)) {
        $batchError = "Batch is required";
    }

    if (empty($coordinatorError) && empty($batchError)) {
        // Update coordinator assignment in the database
        $stmt = $conn->prepare("UPDATE coordinator SET faculty_id=?, batch_id=? WHERE coordinator_id=?");
        $stmt->bind_param("iii", $coordinator_id, $batch_id, $_GET['id']);

        if ($stmt->execute()) {
            echo '<script>alert("Coordinator updated successfully.");</script>';
            echo '<script>window.location.href = "coordinator.php";</script>';
        } else {
            echo '<script>alert("Error updating coordinator: ' . $conn->error . '");</script>';
        }
        $stmt->close();
    }
}

// Fetch current coordinator details if editing
if (isset($_GET['id'])) {
    $coordinator_id = $_GET['id'];

    // Fetch coordinator details
    $sql = "SELECT faculty_id, batch_id FROM coordinator WHERE coordinator_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $coordinator_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $coordinatorValue = $row['faculty_id'];
        $batchValue = $row['batch_id'];
    } else {
        echo '<script>alert("No coordinator found.");</script>';
        echo '<script>window.location.href = "coordinator.php";</script>';
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Coordinator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
        .error {
            color: red;
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
                        <li class="breadcrumb-item active" aria-current="page">Edit Coordinator</li>
                    </ol>
                </nav>
                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Edit Coordinator</h2>
                    <form action="editCoordinator.php?id=<?php echo htmlspecialchars($_GET['id']); ?>" method="post">
                        <div class="mb-3 mt-3">
                            <label for="coordinator_id">Coordinator Name:</label>
                            <select class="form-select" id="coordinator_id" name="coordinator_id" required>
                                <option value="" selected>Select Coordinator</option>
                                <?php echo $facultyOptions; ?>
                            </select>
                            <span class="error"><?php echo $coordinatorError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="batch_id">Batch:</label>
                            <select class="form-select" id="batch_id" name="batch_id" required>
                                <option value="" selected>Select Batch</option>
                                <?php echo $batchOptions; ?>
                            </select>
                            <span class="error"><?php echo $batchError; ?></span>
                        </div>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="coordinator.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                <script src="script.js"></script>
                <script>
                    // Preserve selected coordinator and batch values
                    document.addEventListener('DOMContentLoaded', function() {
                        var coordinatorIdSelect = document.getElementById('coordinator_id');
                        var batchIdSelect = document.getElementById('batch_id');
                        
                        // Set selected options
                        coordinatorIdSelect.value = "<?php echo $coordinatorValue; ?>";
                        batchIdSelect.value = "<?php echo $batchValue; ?>";
                    });
                </script>
            </div>
        </div>
    </div>
</div>
</body>
</html>
