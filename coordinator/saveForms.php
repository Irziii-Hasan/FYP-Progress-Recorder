<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $batchName = $_POST['batch'];
    $eventName = $_POST['event'];
    $form_id = $_POST['form_id'];

    if (!empty($form_id)) {
        $sql = "UPDATE presentations p
                LEFT JOIN batches b ON p.batch = b.batchID
                LEFT JOIN events e ON p.type = e.eventID
                SET p.form_id = ?
                WHERE b.batchName = ? AND e.EventName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iss', $form_id, $batchName, $eventName);
        $stmt->execute();
    }

    // Redirect back to the view schedule page with a success message
    header('Location: view_schedule.php?success=1');
    exit;
}
?>
