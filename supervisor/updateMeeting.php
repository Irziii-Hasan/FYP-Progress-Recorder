<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "FYP_Progress_Recorder";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $meeting_id = $_POST['meeting_id'];
    $marks = $_POST['marks'];
    $feedback = $_POST['feedback'];
    $attendance_status = $_POST['attendance_status'];

    $sql = "UPDATE meetings SET marks = ?, feedback = ?, attendance_status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issi', $marks, $feedback, $attendance_status, $meeting_id);

    if ($stmt->execute()) {
        header("Location: Meetings.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
