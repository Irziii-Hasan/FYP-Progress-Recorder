<?php

include 'session_admin.php'; // Include session handling

// Initialize error messages and values
$nameError = ""; 
$contactError = ""; 
$organizationError = ""; 
$designationError = ""; 
$postalAddressError = ""; 
$emailError = ""; 
$newJuwIdError = "";

$nameValue = ""; 
$contactValue = ""; 
$organizationValue = ""; 
$designationValue = ""; 
$postalAddressValue = ""; 
$emailValue = ""; 
$juwIdValue = "";

// Database connection 
include 'config.php';

if (isset($_GET['id'])) {
    $juw_id = $_GET['id'];

    // Fetch the current values for the selected external member
    $stmt = $conn->prepare("SELECT * FROM external WHERE juw_id = ?");
    $stmt->bind_param("s", $juw_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $juwIdValue = $row['juw_id'];
        $nameValue = $row['name'];
        $contactValue = $row['contact'];
        $organizationValue = $row['organization'];
        $designationValue = $row['designation'];
        $postalAddressValue = $row['postal_address'];
        $emailValue = $row['email'];
    } else {
        echo '<script>alert("No record found");</script>';
        echo '<script>window.location.href = "external.php";</script>';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $juw_id = $_POST['juw_id'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $organization = $_POST['organization'];
    $designation = $_POST['designation'];
    $postal_address = $_POST['postal_address'];
    $email = $_POST['email'];

    // Validate name, designation, and organization
    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
      $nameError = "Name must contain only alphabets and spaces";
  }
  
    if (!preg_match("/^[a-zA-Z]+$/", $designation)) {
        $designationError = "Designation must contain only alphabets";
    }
    if (!preg_match("/^[a-zA-Z]+$/", $organization)) {
        $organizationError = "Organization must contain only alphabets";
    }

    // Validate contact number
    if (!preg_match("/^03\d{9}$/", $contact)) {
        $contactError = "Contact number must be 11 digits and start with '03'";
    }

    // Validate email
    $email = trim($email);
    if (empty($email)) {
        $emailError = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
    }

    // Check if the new JUW ID already exists
    
    

    if (empty($nameError) && empty($contactError) && empty($organizationError) && empty($designationError) && empty($emailError) && empty($newJuwIdError)) {
        // Update the external record in the database
        $stmt = $conn->prepare("UPDATE external SET juw_id=?, name=?, contact=?, organization=?, designation=?, postal_address=?, email=? WHERE juw_id=?");
        $stmt->bind_param("ssssssss", $juw_id, $name, $contact, $organization, $designation, $postal_address, $email, $juw_id);
        if ($stmt->execute()) {
            echo '<script>alert("Record updated successfully");</script>';
            echo '<script>window.location.href = "external.php";</script>';
        } else {
            echo '<script>alert("Error updating external record: ' . $conn->error . '");</script>';
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit External</title>
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
            <li class="breadcrumb-item"><a href="external.php">External</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit External</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Edit External</h2>
          <form action="editExternal.php?id=<?php echo htmlspecialchars($juw_id); ?>" method="post">
            <div class="mb-3 mt-3">
              <label for="juw_id">User ID:</label>
              <input type="text" class="form-control" id="juw_id" name="juw_id" value="<?php echo htmlspecialchars($juwIdValue); ?>" readonly>
            </div>
            <div class="mb-3 mt-3">
              <label for="name">Full Name:</label>
              <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
              <span class="error"><?php echo $nameError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required>
              <span class="error"><?php echo $emailError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="contact">Contact Number:</label>
              <input type="tel" class="form-control" id="contact" placeholder="Enter Contact Number" name="contact" value="<?php echo htmlspecialchars($contactValue); ?>" required>
              <span class="error"><?php echo $contactError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="organization">Organization:</label>
              <input type="text" class="form-control" id="organization" placeholder="Enter Organization" name="organization" value="<?php echo htmlspecialchars($organizationValue); ?>" required>
              <span class="error"><?php echo $organizationError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="designation">Designation:</label>
              <input type="text" class="form-control" id="designation" placeholder="Enter Designation" name="designation" value="<?php echo htmlspecialchars($designationValue); ?>" required>
              <span class="error"><?php echo $designationError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="postal_address">Postal Address:</label>
              <input type="text" class="form-control" id="postal_address" placeholder="Enter Postal Address" name="postal_address" value="<?php echo htmlspecialchars($postalAddressValue); ?>" required>
              <span class="error"><?php echo $postalAddressError; ?></span>
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="external.php" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
