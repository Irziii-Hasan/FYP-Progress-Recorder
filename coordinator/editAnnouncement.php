<?php
include 'session_coordinator.php'; //add session

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php'; //add db connection

// Fetch announcement details for editing
$announcementId = isset($_GET['id']) ? $_GET['id'] : '';
$announcementValue = "";
$audienceValue = [];
$titleError = "";
$message = "";

if ($announcementId) {
    $query = "SELECT * FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $announcementId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $announcementData = $result->fetch_assoc();
        $announcementValue = $announcementData['message'];
        $audienceValue = explode(",", $announcementData['audience_type']); // Assuming audience types are stored as comma-separated
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = htmlspecialchars(trim($_POST['announcement']));
    $audience = isset($_POST['audience']) ? $_POST['audience'] : [];

    if (empty($announcement)) {
        $titleError = "Announcement is required";
    } else {
        $announcementValue = $announcement;

        // Convert array of selected audiences to a comma-separated string
        $audienceString = implode(",", $audience);

        // Update announcement in database
        $stmt = $conn->prepare("UPDATE announcements SET message = ?, audience_type = ? WHERE id = ?");
        $stmt->bind_param("ssi", $announcement, $audienceString, $announcementId);

        if ($stmt->execute()) {
            header("Location: viewAnnouncement.php");
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Announcement</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
                        <li class="breadcrumb-item"><a href="viewAnnouncement.php">Announcement List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Announcement</li>
                    </ol>
                </nav>
                <div class="container mt-5 d-flex justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="card-title text-center">Edit Announcement</h2>
                                <?php if ($message): ?>
                                    <div class="alert alert-success"><?php echo $message; ?></div>
                                <?php endif; ?>
                                <form action="editannouncement.php?id=<?php echo $announcementId; ?>" method="post">
                                    <div class="mb-3">
                                        <label for="announcement" class="form-label">Announcement:</label>
                                        <textarea class="form-control" id="announcement" name="announcement" rows="2" required><?php echo htmlspecialchars($announcementValue); ?></textarea>
                                        <span class="text-danger"><?php echo $titleError; ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="audience" class="form-label">Audience:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceAll" name="audience[]" value="all" <?php echo in_array('all', $audienceValue) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="audienceAll">All</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceSupervisor" name="audience[]" value="supervisor" <?php echo in_array('supervisor', $audienceValue) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="audienceSupervisor">Supervisor</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceStudents" name="audience[]" value="students" <?php echo in_array('students', $audienceValue) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="audienceStudents">Students</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceExternal" name="audience[]" value="external" <?php echo in_array('external', $audienceValue) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="audienceExternal">External</label>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-block">
                                        <a href="viewAnnouncement.php" class="btn btn-light">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Announcement</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const allCheckbox = document.getElementById("audienceAll");
        const otherCheckboxes = document.querySelectorAll(
            "#audienceSupervisor, #audienceStudents, #audienceExternal"
        );

        // When "All" is checked
        allCheckbox.addEventListener("change", function () {
            if (allCheckbox.checked) {
                otherCheckboxes.forEach(checkbox => {
                    checkbox.disabled = true; // Disable other checkboxes
                    checkbox.checked = false; // Uncheck other checkboxes
                });
            } else {
                otherCheckboxes.forEach(checkbox => {
                    checkbox.disabled = false; // Enable other checkboxes
                });
            }
        });

        // When any other checkbox is checked, disable the "All" checkbox and disable others
        otherCheckboxes.forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                if (checkbox.checked) {
                    allCheckbox.checked = false; // Uncheck "All"
                    otherCheckboxes.forEach(otherCheckbox => {
                        if (otherCheckbox !== checkbox) {
                            otherCheckbox.disabled = true; // Disable the other checkboxes
                        }
                    });
                } else {
                    // When the checkbox is unchecked, enable others
                    otherCheckboxes.forEach(otherCheckbox => {
                        otherCheckbox.disabled = false;
                    });
                }
            });
        });
    });
</script>


</body>
</html>
