<?php

include 'session_coordinator.php';
include 'config.php';

$documentNameError = ""; 
$fileError = ""; 
$sendToError = ""; // Add error variable for send to

$documentNameValue = ""; 
$sendToValue = ""; // Add value variable for send to

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Sanitize input fields
  $document_name = htmlspecialchars($_POST['document_name']);
  $send_to = htmlspecialchars($_POST['send_to']); // Sanitize send_to
  $upload_date = date("Y-m-d");

  // File upload logic
  $target_dir = "../coordinator/uploads/"; // Navigate one directory up to reach supervisor
  $target_file = $target_dir . basename($_FILES["file"]["name"]);
  $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  // Validate input fields
  if (empty($document_name)) {
      $documentNameError = "Document name is required";
  }
  if (empty($send_to)) {
      $sendToError = "Send to selection is required"; // Validate send_to
  }

  $valid_types = array("doc", "docx", "ppt", "pptx", "xls", "xlsx", "pdf");
  if (!in_array($file_type, $valid_types)) {
      $fileError = "Invalid file type. Only DOC, DOCX, PPT, PPTX, XLS, XLSX, and PDF files are allowed.";
  }

  if (empty($documentNameError) && empty($fileError) && empty($sendToError)) {
      // Check for duplicate document_name
      $check_query = "SELECT * FROM templates WHERE document_name = '$document_name'";
      $result = $conn->query($check_query);

      if ($result->num_rows > 0) {
          $documentNameError = "A template with this document name already exists. Please choose a different name.";
      } else {
          if (!file_exists($target_dir)) {
              mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
          }

          if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
              $sql = "INSERT INTO templates (document_name, file_path, upload_date, send_to) VALUES ('$document_name', '$target_file', '$upload_date', '$send_to')";
              if ($conn->query($sql) === TRUE) {
                  header("Location: templates.php");
              } else {
                  echo '<script>alert("An error occurred while uploading the template. Please try again.");</script>';
              }
          } else {
              $fileError = "There was an error uploading your file.";
          }
      }
  }

  $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Upload Template</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <!-- Custom styles -->
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

    .error {
      color: red;
    }

    .select-wrapper {
      margin-top: 1rem;
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
            <li class="breadcrumb-item"><a href="templates.php">Templates</a></li>
            <li class="breadcrumb-item active" aria-current="page">Upload Template</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Upload Template</h2>
          <form action="uploadTemplates.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="mb-3 mt-3">
              <label for="document_name" class="form-label">Document Name</label>
              <input type="text" class="form-control" id="document_name" name="document_name" value="<?php echo htmlspecialchars($documentNameValue); ?>" required>
              <span class="error"><?php echo $documentNameError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="file" class="form-label">Select file</label>
              <input type="file" class="form-control" id="file" name="file" required>
              <span class="error"><?php echo $fileError; ?></span>
            </div>
            <div class="mb-3 mt-3 select-wrapper">
              <label for="send_to" class="form-label">Send To</label>
              <select class="form-select" id="send_to" name="send_to" required>
                <option value="" disabled selected>Select recipient</option>
                <option value="All">All</option>
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
              </select>
              <span class="error"><?php echo $sendToError; ?></span>
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="templates.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Upload</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Font Awesome -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <script>
    function validateForm() {
      var document_name = document.getElementById("document_name").value;
      var file = document.getElementById("file").value;
      var send_to = document.getElementById("send_to").value;
      var isValid = true;

      if (document_name === "") {
        document.getElementById("documentNameError").innerText = "Document name is required";
        isValid = false;
      } else {
        document.getElementById("documentNameError").innerText = "";
      }

      if (file === "") {
        document.getElementById("fileError").innerText = "File is required";
        isValid = false;
      } else {
        document.getElementById("fileError").innerText = "";
      }

      if (send_to === "") {
        document.getElementById("sendToError").innerText = "Send to selection is required";
        isValid = false;
      } else {
        document.getElementById("sendToError").innerText = "";
      }

      return isValid;
    }
  </script>
</body>
</html>
