<?php
include 'session_coordinator.php';
include 'config.php'; // Ensure this file includes database connection

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Adjust the path for the student uploads directory
$upload_dir = '../student/uploads/videos/'; // Path to the videos folder from the coordinator folder

// Query to fetch videos
$sql = "SELECT * FROM videos WHERE title LIKE '%$search%' OR description LIKE '%$search%' OR uploaded_at LIKE '%$search%'";
$result = $conn->query($sql);

$videos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Construct the correct file path for each video
        $row['file_path'] = $upload_dir . basename($row['file_path']);
        $videos[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Video Gallery</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .heading {
            color: #0a4a91;
            font-weight: 700;
        }
        .container {
            margin-top: 20px;
        }
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            background: #000;
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .description {
            height: 1.6rem; /* Adjust based on your design */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            transition: height 0.3s ease;
        }
        .description.expanded {
            height: auto;
            white-space: normal;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toggle-description {
            cursor: pointer;
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
                        <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">FYP Gallery</h1>
                    </div>

                    <!-- Search Form -->
                    <form class="mb-4" method="GET" action="">
                        <div class="row mb-3 justify-content-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <div class="input-group-text"><i class="bi bi-search"></i></div>
                                    </span>
                                    <input class="form-control me-2" type="search" name="search" id="myInput" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <?php if (empty($videos)): ?>
                            <p class="text-center">No videos available.</p>
                        <?php else: ?>
                            <?php foreach ($videos as $video): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title"><?php echo htmlspecialchars($video['title']); ?></h5>
                                        </div>
                                        <div class="video-container">
                                            <video class="card-img-top" controls>
                                                <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="card-body">
                                            <p class="description" id="desc-<?php echo htmlspecialchars($video['id']); ?>">
                                                <strong>Description:</strong> <?php echo htmlspecialchars($video['description']); ?>
                                            </p>
                                            <span class="toggle-description" data-target="#desc-<?php echo htmlspecialchars($video['id']); ?>">
                                                <i class="bi bi-three-dots"></i>
                                            </span>
                                        </div>
                                        <div class="card-footer">
                                            <p class="card-text text-muted mb-0">Uploaded on <?php echo htmlspecialchars($video['uploaded_at']); ?></p>
                                            <!-- Delete Button -->
                                            <form method="POST" action="delete_video.php" style="display: inline;">
                                                <input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video['id']); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-description').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = document.querySelector(this.getAttribute('data-target'));
                target.classList.toggle('expanded');
                this.innerHTML = target.classList.contains('expanded') ? '<i class="bi bi-dash"></i>' : '<i class="bi bi-three-dots"></i>';
            });
        });
    });
</script>

</body>
</html>
