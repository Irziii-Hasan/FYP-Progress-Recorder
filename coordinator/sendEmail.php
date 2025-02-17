<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['announcement_id'])) {
    $announcement_id = $_POST['announcement_id'];
    
    // Fetch the announcement details
    $sql = "SELECT message, audience_type FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $announcement = $result->fetch_assoc();
        $message = $announcement['message'];
        $audienceType = $announcement['audience_type'];
        
        // Fetch the email addresses based on the audience type
        $emailQuery = "";
        switch ($audienceType) {
            case 'all':
                $emailQuery = "SELECT email FROM faculty UNION SELECT email FROM student UNION SELECT email FROM external";
                break;
            case 'supervisor':
                $emailQuery = "SELECT email FROM faculty";
                break;
            case 'students':
                $emailQuery = "SELECT email FROM student";
                break;
            case 'external':
                $emailQuery = "SELECT email FROM external";
                break;
        }
        
        $emailResult = $conn->query($emailQuery);
        $emails = [];
        
        while ($row = $emailResult->fetch_assoc()) {
            $emails[] = $row['email'];
        }
        
        // Create instance of PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'zareenkhan33939@gmail.com';
            $mail->Password = 'ydxy gmke aonb jptr'; // Replace with your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
            
            // Recipients
            $mail->setFrom('zareenkhan33939@gmail.com', 'FYP Progress Recorder');
            
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($email);
                }
            }
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Announcement';
            $mail->Body = $message;
            
            $mail->send();
            echo 'Emails have been sent successfully!';
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No announcement found.";
    }
    
    $stmt->close();
}

$conn->close();
?>
