<?php
// update_report.php

header('Content-Type: application/json');

$heading = $_POST['heading'] ?? '';
$subheading = $_POST['subheading'] ?? '';
$batch = $_POST['batch'] ?? '';
$event = $_POST['event'] ?? '';
$columns = $_POST['columns'] ?? [];

if (empty($heading) || empty($subheading)) {
    echo json_encode(['success' => false, 'message' => 'Heading and Subheading cannot be empty.']);
    exit;
}

// Update report headings in the database or session here
// ...

echo json_encode([
    'success' => true,
    'heading' => htmlspecialchars($heading),
    'subheading' => htmlspecialchars($subheading)
]);
