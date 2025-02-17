<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Role</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Choose Role</h3>
                    </div>
                    <div class="card-body text-center">
                        <a href="supervisor/dashboard.php" class="btn btn-primary btn-block">Login as Supervisor</a>
                        <a href="coordinator/dashboard.php" class="btn btn-secondary btn-block">Login as Coordinator</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
