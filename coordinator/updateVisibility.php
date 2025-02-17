<?php
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from POST request
    $batch = $_POST['batch'];
    $event = $_POST['event'];
    $send_to = $_POST['send_to']; // 'All' or 'None'

    // Prepare the SQL query to update the 'send_to' attribute based on batch and event
    $sql = "UPDATE presentations 
            SET send_to = ? 
            WHERE batch = ? AND type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $send_to, $batch, $event);

    // Execute the query and return a JSON response
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Visibility updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update visibility.']);
    }

    $stmt->close();
}
?>
