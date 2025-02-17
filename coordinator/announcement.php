<?php
include 'session_coordinator.php'; //add session

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php'; //add db connection


// Create announcements table if it doesn't exist
$tableCreationQuery = "CREATE TABLE IF NOT EXISTS announcements (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    audience_type VARCHAR(255) NOT NULL
)";

if ($conn->query($tableCreationQuery) !== TRUE) {
    echo "Error creating table: " . $conn->error;
}

$message = "";
$titleError = "";
$announcementValue = "";
$audienceValue = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = htmlspecialchars(trim($_POST['announcement']));
    $audience = isset($_POST['audience']) ? $_POST['audience'] : [];

    if (empty($announcement)) {
        $titleError = "Announcement is required";
    } else {
        $announcementValue = $announcement;

        // Convert array of selected audiences to a comma-separated string
        $audienceString = implode(",", $audience);

        foreach ($audience as $audienceType) {
            // Insert each audience type as a separate record
            $stmt = $conn->prepare("INSERT INTO announcements (message, audience_type) VALUES (?, ?)");
            $stmt->bind_param("ss", $announcement, $audienceType);

            if ($stmt->execute()) {
                header("Location: viewAnnouncement.php");
            } else {
                $message = "Error: " . $stmt->error;
                break;
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Post Announcement</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
                <div class="container mt-5 d-flex justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="card-title text-center">Post Announcement</h2>
                                <?php if ($message): ?>
                                    <div class="alert alert-success"><?php echo $message; ?></div>
                                <?php endif; ?>
                                <form action="announcement.php" method="post">
                                    <div class="mb-3">
                                        <label for="announcement" class="form-label">Announcement:</label>
                                        <textarea class="form-control" id="announcement" name="announcement" rows="2" required><?php echo htmlspecialchars($announcementValue); ?></textarea>
                                        <span class="text-danger"><?php echo $titleError; ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="audience" class="form-label">Audience:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceAll" name="audience[]" value="All">
                                            <label class="form-check-label" for="audienceAll">All</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceSupervisor" name="audience[]" value="Supervisor">
                                            <label class="form-check-label" for="audienceSupervisor">Supervisor</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceStudents" name="audience[]" value="Students">
                                            <label class="form-check-label" for="audienceStudents">Students</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="audienceExternal" name="audience[]" value="External">
                                            <label class="form-check-label" for="audienceExternal">External</label>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-block">
                                        <a href="dashboard.php" class="btn btn-light">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Post Announcement</button>
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

        // When any other checkbox is checked
        otherCheckboxes.forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                if (checkbox.checked) {
                    allCheckbox.checked = false; // Uncheck "All"
                }
            });
        });
    });
</script>

</body>
</html>
