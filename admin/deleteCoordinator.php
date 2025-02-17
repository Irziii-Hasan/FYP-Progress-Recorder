<?php
include 'config.php';

$response = array('success' => false, 'message' => '');

// Check if the ID is provided
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM coordinator WHERE coordinator_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Failed to delete the coordinator.';
    }
    
    $stmt->close();
} else {
    $response['message'] = 'No ID provided.';
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
