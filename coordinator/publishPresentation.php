<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_name = $_POST['batch_name'];
    $event_name = $_POST['event_name'];
    $action = $_POST['action']; // "publish" or "unpublish"

    // Update `send_to` based on the action
    $send_to_value = ($action === 'publish') ? 'all' : 'none';

    $sql = "
        UPDATE presentations p
        JOIN batches b ON p.batch = b.batchID
        JOIN events e ON p.type = e.eventID
        SET p.send_to = ?
        WHERE b.batchName = ? AND e.EventName = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $send_to_value, $batch_name, $event_name);

    if ($stmt->execute()) {
        header('Location: view_Schedule.php?status=success');
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>
