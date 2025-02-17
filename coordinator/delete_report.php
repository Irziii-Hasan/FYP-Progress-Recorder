<?php
include 'config.php';

if (isset($_GET['id'])) {
    $reportId = intval($_GET['id']);

    // Fetch the report path from the database
    $sql = "SELECT report_path FROM reports WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reportId);
    $stmt->execute();
    $stmt->bind_result($reportPath);
    $stmt->fetch();
    $stmt->close();

    // Delete the PDF file from the server
    if (file_exists($reportPath)) {
        unlink($reportPath);
    }

    // Delete the report record from the database
    $sql = "DELETE FROM reports WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reportId);

    if ($stmt->execute()) {
        // Redirect back to the list page with success message
        header("Location: list_reports.php?msg=Report deleted successfully");
    } else {
        // Redirect back to the list page with error message
        header("Location: list_reports.php?msg=Error deleting report");
    }

    $stmt->close();
}
?>
