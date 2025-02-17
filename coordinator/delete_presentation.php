<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    $room_id = $data['room_id'] ?? null;
    $batch_id = $data['batch_id'] ?? null;
    $date = $data['date'] ?? null;
    $time = $data['time'] ?? null;
    $project_id = $data['project_id'] ?? null;
    $form_id = $data['form_id'] ?? null;

    if ($room_id && $batch_id && $date && $time && $project_id && $form_id ) {
        $sql = "DELETE FROM presentations WHERE 
                room_id = ? AND 
                batch = ? AND 
                date = ? AND 
                time = ? AND 
                project_id = ? AND 
                form_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('iissii', $room_id, $batch_id, $date, $time, $project_id, $form_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Presentation deleted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete presentation.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare the query.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
