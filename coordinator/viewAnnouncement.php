<?php
include 'session_coordinator.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';//db connection

// Query to select course durations
$sql = "SELECT id, title FROM course_durations";
$stmt = $conn->prepare($sql); // Preparing the query
$stmt->execute();
$courseDurations = $stmt->get_result(); // Fetching the result

// If a course title is selected, filter the announcements by that course duration's start and end dates
if (isset($_POST['course_title'])) {
    $courseTitle = $_POST['course_title'];
    $sql = "SELECT a.id, a.message, a.created_at, a.audience_type 
            FROM announcements a
            JOIN course_durations c ON a.created_at BETWEEN c.start_date AND c.end_date
            WHERE c.title = ? ORDER BY a.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $courseTitle); // Binding the course title
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default query for displaying all announcements
    $sql = "SELECT id, message, created_at, audience_type FROM announcements ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Announcements</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom styles -->
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
    .btn-add {
      float: right;
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
                        <li class="breadcrumb-item active" aria-current="page">Announcement List</li>
                    </ol>
                </nav>
                <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="heading">Announcement List</h2>
                    <a href="announcement.php" class="btn btn-primary">Upload Announcement</a>
                </div>   
                <div class="col-md-3">
                    <!-- Filter by Course Duration -->
                    <form method="POST" action="viewAnnouncement.php">
                        <select name="course_title" class="form-select" onchange="this.form.submit()">
                            <option value="">Select Course Duration</option>
                            <?php while ($row = $courseDurations->fetch_assoc()): ?>
                                <option value="<?php echo $row['title']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>
                
                
                <div class="table-container mt-4">                 
                    <?php if ($result->num_rows > 0): ?>
                        <table class="table table-bordered table-striped">  
                            <thead>
                                <tr>
                                    <th>Announcement</th>
                                    <th>Created At</th>
                                    <th>Audience</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                                        <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['audience_type']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info" onclick="sendEmail(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-envelope"></i>
                                            </button>
                                        
                                        
                                                <a href="editAnnouncement.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" onclick="deleteAnnouncement(<?php echo $row['id']; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                                
                    <?php else: ?>
                        <div class="alert alert-warning">No announcements found.</div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

function sendEmail(announcementId) {
    if (confirm("Are you sure you want to send email")){
        $.ajax({
        url: 'sendEmail.php',
        type: 'POST',
        data: { announcement_id: announcementId },
        success: function(response) {
            alert(response);
        },
        error: function(xhr, status, error) {
            alert('An error occurred: ' + error);
        }
    });
    }
}
</script>

<script>
function deleteAnnouncement(announcementId) {
    if (confirm("Are you sure you want to delete this announcement?")) {
        fetch('deleteAnnouncement.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${announcementId}`
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        })
        .catch(error => alert('An error occurred: ' + error));
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>
