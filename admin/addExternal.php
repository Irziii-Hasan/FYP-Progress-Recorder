<?php
include 'session_admin.php'; // Include session handling
include 'config.php';

$nameError = ""; 
$contactError = ""; 
$organizationError = ""; 
$designationError = ""; 
$postalAddressError = ""; 
$emailError = ""; 
$juwIdError = "";

$nameValue = ""; 
$contactValue = ""; 
$organizationValue = ""; 
$designationValue = ""; 
$postalAddressValue = ""; 
$emailValue = ""; 
$juwIdValue = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create table if not exists
    $sql_create_table = "CREATE TABLE IF NOT EXISTS external (
        juw_id VARCHAR(255) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        contact VARCHAR(15) NOT NULL,
        organization VARCHAR(255) NOT NULL,
        designation VARCHAR(255) NOT NULL,
        postal_address TEXT NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql_create_table) !== TRUE) {
        die("Error creating table: " . $conn->error);
    }

    // Sanitize input fields
    $juw_id = htmlspecialchars($_POST['juw_id']);
    $name = htmlspecialchars($_POST['name']);
    $contact = htmlspecialchars($_POST['contact']);
    $organization = htmlspecialchars($_POST['organization']);
    $designation = htmlspecialchars($_POST['designation']);
    $postal_address = htmlspecialchars($_POST['postal_address']);
    $email = htmlspecialchars($_POST['email']);

    // Generate random password
    $random_password = bin2hex(random_bytes(8)); // Generates an 8-character random hexadecimal string

    // Validate input fields
 // Validate input fields
if (empty($juw_id) || $juw_id === "0") {
    $juwIdError = "Valid JUW ID is required";
}

    if (empty($name)) {
        $nameError = "Name is required";
    }
    if (empty($contact)) {
        $contactError = "Contact number is required";
    }
    if (empty($organization)) {
        $organizationError = "Organization is required";
    }
    if (empty($designation)) {
        $designationError = "Designation is required";
    }
    if (empty($postal_address)) {
        $postalAddressError = "Postal address is required";
    }
    if (empty($email)) {
        $emailError = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
    }

    if (empty($juwIdError) && empty($nameError) && empty($contactError) && empty($organizationError) && empty($designationError) && empty($postalAddressError) && empty($emailError)) {
        $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
        $sql_external = "INSERT INTO external (juw_id, name, contact, organization, designation, postal_address, email, password) 
                         VALUES ('$juw_id', '$name', '$contact', '$organization', '$designation', '$postal_address', '$email', '$hashed_password')";

        if ($conn->query($sql_external) === TRUE) {
            echo '<script>alert("New external record created successfully");</script>';
        } else {
            echo '<script>alert("Error: ' . $sql_external . '<br>' . $conn->error . '");</script>';
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP| Add External</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
            <li class="breadcrumb-item"><a href="external.php">External</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add External</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Add External</h2>
          <form action="addExternal.php" method="post">
            <div class="mb-3 mt-3">
              <label for="juw_id">User ID:</label>
              <input type="text" class="form-control" id="juw_id" placeholder="Enter User ID" name="juw_id" value="<?php echo htmlspecialchars($juwIdValue); ?>" required>
              <span class="error"><?php echo $juwIdError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="name">Name:</label>
              <input type="text" class="form-control" id="name" placeholder="Enter Full Name" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
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
              <a href="external.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<script>
  function validateForm() {
    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var contact = document.getElementById("contact").value;
    var juw_id = document.getElementById("juw_id").value;
    var organization = document.getElementById("organization").value;
    var designation = document.getElementById("designation").value;
    var postal_address = document.getElementById("postal_address").value;
    var isValid = true;

    if (name === "") {
      document.getElementById("nameError").innerText = "Name is required";
      isValid = false;
    } else {
      document.getElementById("nameError").innerText = "";
    }

    // Add similar validations for other fields

    return isValid;
  }
</script>
</body>
</html>
