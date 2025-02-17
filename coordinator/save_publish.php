<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sendTo = isset($_POST['send_to']) ? $conn->real_escape_string($_POST['send_to']) : '';

    // Validate and sanitize inputs as needed

    // Update the presentations table
    $sql = "UPDATE presentations SET send_to = '$sendTo' WHERE /* condition to match records displayed in the table */";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
