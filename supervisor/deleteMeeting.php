<?php
include 'config.php';

if (isset($_GET['id'])) {
    $meeting_id = $_GET['id'];

    // SQL to delete the meeting
    $sql_delete = "DELETE FROM meetings WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $meeting_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
