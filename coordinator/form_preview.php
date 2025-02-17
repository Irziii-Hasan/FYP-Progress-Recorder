<?php
include 'session_coordinator.php'; // Include session handling
include 'config.php';

$form_id = $_GET['form_id'];

// Fetch form details
$query = "SELECT * FROM customized_form WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $form_id);
$stmt->execute();
$form_result = $stmt->get_result()->fetch_assoc();

// Fetch form details
$query_details = "SELECT * FROM form_detail WHERE form_id = ?";
$stmt = $conn->prepare($query_details);
$stmt->bind_param('i', $form_id);
$stmt->execute();
$details_result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Form Preview</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <style>
        .preview-section {
            padding: 20px 30px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
            margin-bottom: 20px;
        }

        .preview-heading {
            color: #0a4a91;
            font-weight: 700;
        }

        .preview-title {
            font-size: 1.9em;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .preview-marks {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .preview-table {
            width: 100%;
            border-collapse: collapse;
        }

        .preview-table th, .preview-table td {
            border: 1px solid #cbcbcb;
            padding: 8px;
            text-align: left;
        }

        .preview-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid" id="content">
        <div class="row">
            <div class="col-md-12">
                <!-- BREADCRUMBS -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item"><a href="forms.php">Forms</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Preview</li>
                    </ol>
                </nav>

                <div class="container mt-3 preview-section" style="width: 650px;">
                    <h2 class="text-center preview-heading">Form Preview</h2>
                    
                    <div class="preview-title"><?php echo htmlspecialchars($form_result['title']); ?></div>
                    <div class="preview-marks">
                        <span>Passing Marks: <?php echo $form_result['passing_marks']; ?></span>
                        <span>Total Marks: <?php echo $form_result['total_marks']; ?></span>
                    </div>
                    
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Max Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($detail_row = $details_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($detail_row['description']); ?></td>
                                    <td><?php echo $detail_row['max_marks']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
