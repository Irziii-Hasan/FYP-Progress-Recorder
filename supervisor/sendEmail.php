<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "FYP_Progress_Recorder";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meeting_id'])) {
    $meeting_id = $_POST['meeting_id'];
    
    // Updated SQL query to fetch email addresses and supervisor name
    $sql = "SELECT meetings.title, meetings.date, meetings.time, meetings.description, 
                   faculty.email AS supervisor_email, faculty.username AS supervisor_name,
                   student1.email AS student1_email, 
                   student2.email AS student2_email, 
                   student3.email AS student3_email, 
                   student4.email AS student4_email
            FROM meetings
            JOIN projects ON meetings.project_id = projects.id
            LEFT JOIN student AS student1 ON projects.student1 = student1.student_id
            LEFT JOIN student AS student2 ON projects.student2 = student2.student_id
            LEFT JOIN student AS student3 ON projects.student3 = student3.student_id
            LEFT JOIN student AS student4 ON projects.student4 = student4.student_id
            LEFT JOIN faculty ON projects.supervisor = faculty.faculty_id
            WHERE meetings.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $meeting_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $meeting = $result->fetch_assoc();
        $supervisorEmail = $meeting['supervisor_email'];
        $supervisorName = $meeting['supervisor_name'];
        $studentEmails = [
            $meeting['student1_email'],
            $meeting['student2_email'],
            $meeting['student3_email'],
            $meeting['student4_email']
        ];
        
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
            
            // Validate supervisor email
            if (filter_var($supervisorEmail, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($supervisorEmail);
            } else {
                echo 'Invalid supervisor email: ' . htmlspecialchars($supervisorEmail) . '<br>';
            }

            // Validate student emails
            foreach ($studentEmails as $studentEmail) {
                if (!empty($studentEmail) && filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($studentEmail);
                } else {
                    echo 'Invalid student email: ' . htmlspecialchars($studentEmail) . '<br>';
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Meeting Scheduled: ' . htmlspecialchars($meeting['title']);
            $mail->Body = '<p>Dear Team,</p>
                           <p>A meeting has been scheduled with the following details:</p>
                           <p><strong>Title:</strong> ' . htmlspecialchars($meeting['title']) . '</p>
                           <p><strong>Date:</strong> ' . htmlspecialchars($meeting['date']) . '</p>
                           <p><strong>Time:</strong> ' . htmlspecialchars($meeting['time']) . '</p>
                           <p><strong>Description:</strong> ' . nl2br(htmlspecialchars($meeting['description'])) . '</p>
                           <p><strong>Supervisor:</strong> ' . htmlspecialchars($supervisorName) . '</p>
                           <p>Best regards,<br><p>' . htmlspecialchars($supervisorName) . '</p>
</p>';
            
            $mail->send();
            echo 'Email has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Meeting not found';
    }
    
    $stmt->close();
} else {
    echo 'Invalid request';
}

$conn->close();
?>
