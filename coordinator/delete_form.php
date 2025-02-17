<?php
include 'session_coordinator.php'; // Include session handling
include 'config.php';

// Check if form ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $form_id = intval($_GET['id']); // Sanitize input

    // Prepare and execute the deletion query
    $stmt = $conn->prepare("DELETE FROM customized_form WHERE id = ?");
    $stmt->bind_param("i", $form_id);

    if ($stmt->execute()) {
        // Successful deletion
        $_SESSION['message'] = "Form successfully deleted.";
    } else {
        // Error during deletion
        $_SESSION['message'] = "Failed to delete form. Please try again.";
    }

    $stmt->close();
} else {
    // Invalid or missing form ID
    $_SESSION['message'] = "Invalid form ID.";
}

$conn->close();

// Redirect back to the forms list page
header("Location: forms.php");
exit();
?>
