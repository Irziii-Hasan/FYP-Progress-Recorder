<?php
include 'session_external_evaluator.php'; // Include session management
include 'config.php';

// Fetch button visibility values
$buttons_sql = "SELECT id, fyp_i_mid, fyp_ii_mid, fyp_i_final, fyp_ii_final, mid_form FROM visible_pages WHERE id = 1";
$stmt = $conn->prepare($buttons_sql);
$stmt->execute();
$result = $stmt->get_result();

$buttons = [];
if ($result->num_rows > 0) {
    $buttons = $result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | View Buttons</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        .container {
            margin-top: 20px;
        }
        .button-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
        }
        .btn-custom {
            width: 200px;
            height: 60px;
            margin: 10px;
            font-size: 1rem;
            text-align: center;
        }
        .btn-disabled {
            display: none;
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
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
                        <li class="breadcrumb-item active" aria-current="page">View Buttons</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">Available Forms</h1>
                    </div>
                    <div class="container mt-5">
                        <div class="button-container">
                            <?php if ($buttons['fyp_i_mid'] === 'yes'): ?>
                                <button class="btn btn-primary btn-custom" onclick="redirectTo('fyp_i_mid')">
                                    FYP-I Mid
                                </button>
                            <?php endif; ?>

                            <?php if ($buttons['fyp_ii_mid'] === 'yes'): ?>
                                <button class="btn btn-success btn-custom" onclick="redirectTo('fyp_ii_mid')">
                                    FYP-II Mid
                                </button>
                            <?php endif; ?>

                            <?php if ($buttons['fyp_i_final'] === 'yes'): ?>
                                <button class="btn btn-danger btn-custom" onclick="redirectTo('fyp_i_final')">
                                    FYP-I Final
                                </button>
                            <?php endif; ?>

                            <?php if ($buttons['fyp_ii_final'] === 'yes'): ?>
                                <button class="btn btn-warning btn-custom" onclick="redirectTo('fyp_ii_final')">
                                    FYP-II Final
                                </button>
                            <?php endif; ?>

                            <?php if ($buttons['mid_form'] === 'yes'): ?>
                                <button class="btn btn-info btn-custom" onclick="redirectTo('mid_form')">
                                    Mid Form
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function redirectTo(buttonType) {
        // Define redirection paths based on button type
        let paths = {
            'fyp_i_mid': 'fyp1_mid_marks.php?type=fyp_i_mid',
            'fyp_ii_mid': 'fyp2_mid_marks.php?type=fyp_ii_mid',
            'fyp_i_final': 'fyp1_summer_mid_marks.php?type=fyp_i_final',
            'fyp_ii_final': 'fyp1_summer_mid_marks.php?type=fyp_ii_final',
            'mid_form': 'MidEvaluation.php?type=mid_form'
        };

        // Get the URL based on the button type
        let url = paths[buttonType];

        // Redirect to the URL
        if (url) {
            window.location.href = url;
        }
    }
</script>
</body>
</html>
