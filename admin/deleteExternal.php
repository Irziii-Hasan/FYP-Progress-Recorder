<?php
include 'session_admin.php'; // Include session handling
include 'config.php';

// Check if the ID parameter is set in the URL
if(isset($_GET['id'])) {
    // Escape user inputs for security
    $juw_id = $conn->real_escape_string($_GET['id']);

    // SQL to delete record
    $sql = "DELETE FROM external WHERE juw_id='$juw_id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the external list page after deletion
        header("Location: external.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();
?>
