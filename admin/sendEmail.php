<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

// Database configuration
include 'config.php';

// Check if 'juw_id' parameter is present in POST request
if (isset($_POST['juw_id']) && !empty($_POST['juw_id']) && isset($_POST['user_type']) && !empty($_POST['user_type'])) {
    // Sanitize the input to prevent SQL injection
    $juw_id = mysqli_real_escape_string($conn, $_POST['juw_id']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);

    // Define a function to send email
    function sendEmail($conn, $juw_id, $table, $nameAttribute) {
        // Retrieve user details based on the JUW ID
        $sql = "SELECT $nameAttribute, email, juw_id,password FROM $table WHERE juw_id = '$juw_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $username = $row[$nameAttribute];
            $email = $row['email'];
            $password = $row['password']; // Assuming password is same as JUW ID

            // Create an instance of PHPMailer
            $mail = new PHPMailer(true);

            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'zareenkhan33939@gmail.com'; // Replace with your SMTP username
                $mail->Password = 'ydxy gmke aonb jptr'; // Replace with your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Email settings
                $mail->setFrom('zareenkhan33939@gmail.com', 'Zareen Khan'); // Replace with your sender email and name
                $mail->addAddress($email); // Add a recipient

                // Email content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Your Account Details';
                $mail->Body    = "Dear $username,<br><br>Your account details are as follows:<br><br>User ID: $juw_id<br>Password: $password<br><br>Best Regards,<br>Your Company";
                $mail->AltBody = "Dear $username,\n\nYour account details are as follows:\n\nUser ID: $juw_id\nPassword: $password\n\nBest Regards,\nYour Company";

                $mail->send();
                echo "<div class='alert alert-success'>Message has been sent to $email</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>No matching record found for User ID: $juw_id in $table</div>";
        }
    }

    // Check user type and send email accordingly
    switch ($user_type) {
        case 'student':
            sendEmail($conn, $juw_id, 'student', 'username');
            break;
        case 'faculty':
            sendEmail($conn, $juw_id, 'faculty', 'username');
            break;
        case 'external':
            sendEmail($conn, $juw_id, 'external', 'name');
            break;
        default:
            echo "<div class='alert alert-danger'>Invalid user type provided.</div>";
    }

} else {
    echo "<div class='alert alert-danger'>Invalid request. User ID or user type not provided.</div>";
}

$conn->close();
?>
