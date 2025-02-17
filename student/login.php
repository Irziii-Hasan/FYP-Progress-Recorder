<?php
// Start PHP session
session_start();

// Check if user is already logged in, redirect to dashboard if true
if (isset($_SESSION['student_id'])) {
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

    // Query to check if juw_id exists in student table
    $sql_student = "SELECT * FROM student WHERE juw_id = '$juw_id'";
    $result_student = $conn->query($sql_student);

    if ($result_student->num_rows == 1) {
        $row_student = $result_student->fetch_assoc();

        // Verify password
        if ($row_student['password'] == $password) {
            $student_id = $row_student['student_id'];

            // Query to check if student_id exists in any of the 4 student columns in the projects table
            $sql_project = "
                SELECT p.duration, d.start_date, d.end_date
                FROM projects p
                INNER JOIN course_durations d ON p.duration = d.id
                WHERE 
                    (p.student1 = '$student_id' OR 
                     p.student2 = '$student_id' OR 
                     p.student3 = '$student_id' OR 
                     p.student4 = '$student_id')
            ";
            $result_project = $conn->query($sql_project);

            if ($result_project->num_rows > 0) {
                $valid_duration = false;
                while ($row_project = $result_project->fetch_assoc()) {
                    $start_date = $row_project['start_date'];
                    $end_date = $row_project['end_date'];
                    $current_date = date('Y-m-d');

                    // Check if the current date is within the duration
                    if ($current_date >= $start_date && $current_date <= $end_date) {
                        $valid_duration = true;
                        break;
                    }
                }

                if ($valid_duration) {
                    // Valid duration, allow login
                    $_SESSION['student_id'] = $row_student['student_id'];
                    $_SESSION['juw_id'] = $row_student['juw_id'];
                    $_SESSION['role'] = 'student'; // Set role as student

                    // Redirect to student dashboard
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error_message = "Your course duration is not active.";
                }
            } else {
                $error_message = "You are not associated with any valid project.";
            }
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Invalid User ID.";
    }

    $conn->close(); // Close database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
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
            background-color: #051747;
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
            <img src="icons/login.png" alt="Student Illustration">
            <h2>Welcome Back!</h2>
            <p>Manage your projects and access your student dashboard easily.</p>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <div class="form-container custom-login-form">
                <h2 class="text-center">Student Login</h2>
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