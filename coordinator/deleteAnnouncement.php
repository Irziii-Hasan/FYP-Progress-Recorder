<?php

include 'session_coordinator.php';
include 'config.php'; // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Announcement deleted successfully!";
    } else {
        echo "Error deleting announcement: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
