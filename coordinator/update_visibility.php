<?php
include 'session_coordinator.php';
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_id = intval($_POST['form_id']); // Sanitize input
    $visible = ($_POST['visible'] === 'yes') ? 'yes' : 'no'; // Sanitize visibility value

    // SQL query to update the visibility column
    $sql = "UPDATE customized_form SET visible = '$visible' WHERE id = $form_id";

    if ($conn->query($sql) === TRUE) {
        echo "Visibility updated successfully!";
    } else {
        echo "Error updating visibility: " . $conn->error;
    }

    $conn->close();
}
?>
