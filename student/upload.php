<?php
include 'session_student.php';
include 'config.php';

// Initialize error and value variables
$titleError = $descriptionError = "";
$titleValue = $descriptionValue = "";

// Handle file upload and form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    
    // File upload settings
    $target_dir = "uploads/videos/";
    $target_file = $target_dir . basename($_FILES["videoFile"]["name"]);
    $uploadOk = 1;
    $videoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate input fields
    if (empty($title)) {
        $titleError = "Title is required.";
    } else {
        $titleValue = $title;
    }

    if (empty($description)) {
        $descriptionError = "Description is required.";
    } else {
        $descriptionValue = $description;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadOk = 0;
        $error_message = "Sorry, file already exists.";
    }

    // Allow certain file formats
    $allowed_extensions = ['mp4', 'avi', 'mov', 'wmv'];
    if (!in_array($videoFileType, $allowed_extensions)) {
        $uploadOk = 0;
        $error_message = "Sorry, only MP4, AVI, MOV & WMV files are allowed.";
    }

    // Proceed with file upload if no errors
    if ($uploadOk == 1 && empty($titleError) && empty($descriptionError)) {
        if (move_uploaded_file($_FILES["videoFile"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO videos (title, description, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $description, $target_file);

            if ($stmt->execute()) {
                $success_message = "The file has been uploaded.";
                header("Location: upload.php?message=$success_message&type=success");
            } else {
                $error_message = "There was an error uploading your file.";
                header("Location: upload.php?message=$error_message&type=danger");
            }

            $stmt->close();
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
            header("Location: upload.php?message=$error_message&type=danger");
        }
    } else {
        header("Location: upload.php?message=$error_message&type=danger");
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            padding: 20px 30px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
            margin: 50px auto;
            max-width: 600px;
        }
        .form-heading {
            color: #0a4a91;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }
        .error {
            color: red;
        }
        .upload-box {
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 50px;
            text-align: center;
            cursor: pointer;
            position: relative;
            height: 150px;
        }
        .upload-box.dragover {
            border-color: #28a745;
            background-color: #eaffea;
        }
        .upload-box p {
            margin: 0;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="container-fluid" id="content">
    <div class="row">
        <div class="col-md-12">
            <!-- BREADCRUMBS -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                    <li class="breadcrumb-item"><a href="gallery.php">Gallery</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Upload</li>
                </ol>
            </nav>

            <!-- Video Upload Form -->
            <div class="form-container">
                <h2 class="form-heading">Upload Your Video</h2>
                
                <?php if (!empty($titleError) || !empty($descriptionError)): ?>
                    <div class="alert alert-danger">
                        <?php echo $titleError; ?>
                        <?php echo $descriptionError; ?>
                    </div>
                <?php endif; ?>

                <form id="upload-form" action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($titleValue); ?>" required>
                        <span class="error"><?php echo $titleError; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($descriptionValue); ?></textarea>
                        <span class="error"><?php echo $descriptionError; ?></span>
                    </div>
                    <div class="upload-box" id="drop-zone">
                        <p id="file-name">Drag and drop your video file here or click to select</p>
                        <input type="file" name="videoFile" id="file-input" accept="video/*" required>
                    </div>
                    <div class="d-grid gap-2 d-md-block mt-3">
                        <a href="gallery.php" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Upload Video</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script>
    // Drag and drop functionality
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const fileNameDisplay = document.getElementById('file-name');
    
    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('dragover');
        fileInput.files = event.dataTransfer.files;
        fileNameDisplay.textContent = fileInput.files[0].name;
    });

    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        fileNameDisplay.textContent = fileInput.files[0].name;
    });

    // Display message function
    function displayMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'alert alert-' + type;
        messageDiv.textContent = message;
        document.querySelector('.form-container').prepend(messageDiv);
        setTimeout(() => {
            messageDiv.remove();
        }, 5000); 
    }

    window.addEventListener('load', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('message')) {
            const message = urlParams.get('message');
            const type = urlParams.get('type') || 'success';
            displayMessage(message, type);
        }
    });
</script>
</body>
</html>
