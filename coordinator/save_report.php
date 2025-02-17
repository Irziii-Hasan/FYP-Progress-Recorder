<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient = $_POST['recipient'];
    $heading = $_POST['heading'];
    $batch = $_POST['batch'];
    $eventID = $_POST['eventID'];

    // Handle the PDF file upload
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
        // Set the destination path
        $uploadDir = '../coordinator/uploads/report/';
        $fileName = basename($_FILES['pdf']['name']);
        $uploadFile = $uploadDir . $fileName;

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadFile)) {
            // Insert the report information into the database
            $sql = "INSERT INTO reports (report_path, recipient, heading, batch, event_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $uploadFile, $recipient, $heading, $batch, $eventID);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Report saved successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save report in the database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload the PDF.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No PDF file uploaded.']);
    }
}
?>
