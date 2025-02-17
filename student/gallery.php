<?php
include 'session_student.php'; // Include session management
include 'config.php'; // Include database connection

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM videos WHERE title LIKE '%$search%' OR description LIKE '%$search%' OR uploaded_at LIKE '%$search%'";
$result = $conn->query($sql);
$videos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
    <title>Video Gallery</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
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
        .card {
            margin-bottom: 20px;
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
                        <a href="upload.php" class="btn btn-primary">Upload Video</a>
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
                                            <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($video['description']); ?></p>
                                            <p class="card-text text-end"><small class="text-muted">Uploaded on <?php echo htmlspecialchars($video['uploaded_at']); ?></small></p>
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

</body>
</html>
