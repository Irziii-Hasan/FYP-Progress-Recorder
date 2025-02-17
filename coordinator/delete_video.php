<?php
include 'config.php'; // Ensure this file includes database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;

    if ($video_id > 0) {
        // Get the video details
        $stmt = $conn->prepare("SELECT file_path FROM videos WHERE id = ?");
        $stmt->bind_param("i", $video_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $video = $result->fetch_assoc();
            $file_path = $video['file_path'];

            // Delete the video record
            $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->bind_param("i", $video_id);
            $stmt->execute();

            // Delete the video file from the server
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            echo "<script>alert('Video deleted successfully'); window.location.href='gallery.php';</script>";
        } else {
            echo "<script>alert('Video not found'); window.location.href='gallery.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Invalid video ID'); window.location.href='gallery.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request'); window.location.href='gallery.php';</script>";
}

$conn->close();
?>
