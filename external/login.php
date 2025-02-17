<?php
// Start PHP session
session_start();

// Check if user is already logged in, redirect to dashboard if true
if (isset($_SESSION['external_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection 
    include 'config.php';

    // Escape user inputs for security
    $juw_id = $conn->real_escape_string($_POST['juw_id']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query to check if juw_id exists in external table
    $sql_external = "SELECT * FROM external WHERE juw_id = '$juw_id'";
    $result_external = $conn->query($sql_external);

    if ($result_external->num_rows == 1) {
        $row_external = $result_external->fetch_assoc();
        $external_id = $row_external['external_id'];

        // Verify password
        if ($row_external['password'] == $password) {
            $_SESSION['external_id'] = $row_external['external_id'];
            $_SESSION['role'] = 'external'; // Set role as external

            // Redirect to external dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "Invalid password";
        }
    } else {
        $error_message = "Invalid User ID";
    }

    $conn->close(); // Close database connection
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>External Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: rgb(236, 236, 236);
            margin: 0;
            padding: 0;
        }

        .login-container {
            display: flex;
            width: 70%;
            max-width: 900px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            overflow: hidden;
        }

        .left-section {
            flex: 1;
            background-color: #041233;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .left-section img {
            max-width: 80%;
            height: auto;
            margin-bottom: 20px;
        }

        .left-section h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .left-section p {
            font-size: 16px;
            line-height: 1.5;
        }

        .right-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .custom-login-form {
            width: 100%;
            max-width: 300px;
        }

        .custom-login-form h2 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .custom-login-form .form-group label {
            font-weight: 500;
            color: #555;
        }

        .custom-login-form .login-button {
            background-color: #051747;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            color: white;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .custom-login-form .login-button:hover {
            background-color: #0A2A5C;
        }

        .custom-login-form .login-button:active {
            background-color: #041233;
        }

        .custom-login-form .alert {
            font-size: 14px;
        }

        .custom-login-form .other-role-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #2068d9;
            text-decoration: none;
            font-weight: 500;
        }

        .custom-login-form .other-role-link:hover {
            color: #2068d9;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Include Navigation -->
    <?php include 'loginnav.php'; ?>

    <!-- Login Container -->
    <div class="login-container">
        <!-- Left Section -->
        <div class="left-section">
            <img src="icons/login.png" alt="External Illustration">
            <h2>Welcome External Evaluator!</h2>
            <p>Log in to evaluate projects and provide valuable feedback.</p>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <div class="form-container custom-login-form">
                <h2 class="text-center">External Login</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="juw_id">User ID</label>
                        <input type="text" class="form-control" id="juw_id" name="juw_id" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <button type="submit" class="login-button">Login</button>
                </form>
                <a href="../index.html" class="other-role-link">Want to login as another role?</a>
            </div>
        </div>
    </div>
</body>
</html>
