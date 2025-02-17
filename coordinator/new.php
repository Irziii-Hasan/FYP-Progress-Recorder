<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fyp_progress_recorder";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch project data
$sql = "SELECT id, title, description FROM projects";
$result = $conn->query($sql);

// Initialize variables
$predicted_progress = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $selected_project_id = $_POST['id'];

    // Fetch additional data for prediction
    $data_query = "
        SELECT 
            (SELECT COUNT(*) FROM assignments WHERE id = ?) AS total_assignments,
            (SELECT COUNT(*) FROM meetings WHERE id = ?) AS total_meetings;
    ";
    $stmt = $conn->prepare($data_query);
    $stmt->bind_param("ii", $selected_project_id, $selected_project_id);
    $stmt->execute();
    $result_data = $stmt->get_result()->fetch_assoc();

    // Prepare data for prediction API
    $api_data = json_encode([
        'total_assignments' => $result_data['total_assignments'],
        'total_meetings' => $result_data['total_meetings'],
    ]);

    // Call prediction API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:5000/predict"); // Replace with your model API URL
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $api_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo "Error in API call: " . curl_error($ch);
    } else {
        $predicted_progress = json_decode($response, true)['predicted_progress'];
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Progress Prediction</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Project Progress Prediction</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="project">Select Project:</label>
            <select class="form-control" name="id" id="project">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo $row['title']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Predict Progress</button>
    </form>

    <?php if ($predicted_progress !== null) { ?>
        <div class="alert alert-info mt-3">
            Predicted Progress: <strong><?php echo $predicted_progress; ?>%</strong>
        </div>
    <?php } ?>
</div>
</body>
</html>
