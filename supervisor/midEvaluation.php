
<?php

include 'session_supervisor.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    include 'config.php';

    // Use null coalescing operator to avoid undefined index warnings
    $project_id = $_POST['project_id'] ?? '';
    $examiner = $_POST['examiner'] ?? '';
    $signature = $_POST['signature'] ?? '';
    $progress_satisfaction = $_POST['progress_satisfaction'] ?? '';
    $report_complete = $_POST['report_complete'] ?? '';
    $turnitin_attached = $_POST['turnitin_attached'] ?? '';
    $plagiarism_acceptable = $_POST['plagiarism_acceptable'] ?? '';
    $meetings_attended = $_POST['meetings_attended'] ?? '';
    $qualifies_evaluation = $_POST['qualifies_evaluation'] ?? '';
    $overall_comments = $_POST['overall_comments'] ?? '';
    $comments_1 = $_POST['comments_1'] ?? '';
    $comments_2 = $_POST['comments_2'] ?? '';
    $comments_3 = $_POST['comments_3'] ?? '';
    $comments_4 = $_POST['comments_4'] ?? '';
    $comments_5 = $_POST['comments_5'] ?? '';
    $comments_6 = $_POST['comments_6'] ?? '';

    // Insert query
    $sql = "INSERT INTO midEvaluation (project_id, examiner, signature, progress_satisfaction, report_complete, turnitin_attached, plagiarism_acceptable, meetings_attended, qualifies_evaluation, overall_comments, comments_1, comments_2, comments_3, comments_4, comments_5, comments_6) VALUES ('$project_id', '$examiner', '$signature', '$progress_satisfaction', '$report_complete', '$turnitin_attached', '$plagiarism_acceptable', '$meetings_attended', '$qualifies_evaluation', '$overall_comments', '$comments_1', '$comments_2', '$comments_3', '$comments_4', '$comments_5', '$comments_6')";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Record added successfully");</script>';
    } else {
        echo '<script>alert("Error: ' . $sql . '<br>' . $conn->error . '");</script>';
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mid Evaluation Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <style>
    .form-all {
      padding: 20px 30px;
      border: 1px solid #cbcbcb;
      border-radius: 20px;
      background-color: white;
    }

    .form-heading {
      color: #0a4a91;
      font-weight: 700;
    }

    label {
      font-weight: 500;
    }

    .error {
      color: red;
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
                            <li class="breadcrumb-item active" aria-current="page">Mid Evaluation Form</li>
                        </ol>
                    </nav>
                    <div class="container mt-3 form-all" style="max-width: 900px;">
                        <!-- Pages Heading -->
                        <h2 class="text-center form-heading">Mid Evaluation Form</h2>
                        <h3 class="text-center" style="color: #051747; font-size: 20px">
                            Department of Computer Science and Software Engineering <br />
                            Jinnah University for Women<br />
                            Approval for FYP-I Mid Evaluation - (Form-J)
                        </h3>
                        <form action="midEvaluation.php" class="form-group" method="POST" style="font-weight: bold; color: #051747">
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Project ID:</label>
                                <select id="project_id" name="project_id" class="form-select" onchange="fetchProjectDetails()">
                                    <option value="">Select Project ID</option>
                                    <!-- Options will be populated using PHP -->
                                    <?php
                                        include 'config.php';

                                        $sql = "SELECT project_id FROM projects";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row["project_id"] . "'>" . $row["project_id"] . "</option>";
                                            }
                                        }

                                        $conn->close();
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="project_title" class="form-label">Project Title:</label>
                                <input type="text" id="project_title" name="project_title" class="form-control" readonly />
                            </div>
                            <div class="mb-3">
                                <label>Group Members:</label>
                                <input type="text" id="student_name_1" name="student_name_1" class="form-control mb-2" placeholder="Group Member 1" readonly />
                                <input type="text" id="student_name_2" name="student_name_2" class="form-control mb-2" placeholder="Group Member 2" readonly />
                                <input type="text" id="student_name_3" name="student_name_3" class="form-control mb-2" placeholder="Group Member 3" readonly />
                                <input type="text" id="student_name_4" name="student_name_4" class="form-control mb-2" placeholder="Group Member 4" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="supervisor" class="form-label">Supervisor:</label>
                                <input type="text" id="supervisor" name="supervisor" class="form-control" readonly />
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>--------</th>
                                        <th>Yes</th>
                                        <th>No</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>I am satisfied with the progress of FYP-I Mid</td>
                                        <td><input type="radio" name="progress_satisfaction" value="Yes" /></td>
                                        <td><input type="radio" name="progress_satisfaction" value="No" /></td>
                                        <td><textarea name="comments_1" class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Report is complete for FYP-I Mid and no changes are required</td>
                                        <td><input type="radio" name="report_complete" value="Yes" /></td>
                                        <td><input type="radio" name="report_complete" value="No" /></td>
                                        <td><textarea name="comments_2" class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Turnitin report is attached.</td>
                                        <td><input type="radio" name="turnitin_attached" value="Yes" /></td>
                                        <td><input type="radio" name="turnitin_attached" value="No" /></td>
                                        <td><textarea name="comments_3" class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Does plagiarism report is acceptable?</td>
                                        <td><input type="radio" name="plagiarism_acceptable" value="Yes" /></td>
                                        <td><input type="radio" name="plagiarism_acceptable" value="No" /></td>
                                        <td><textarea name="comments_4" class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>All group members attended all meeting</td>
                                        <td><input type="radio" name="meetings_attended" value="Yes" /></td>
                                        <td><input type="radio" name="meetings_attended" value="No" /></td>
                                        <td><textarea name="comments_5" class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>The project qualifies for FYP-I Mid evaluation</td>
                                        <td><input type="radio" name="qualifies_evaluation" value="Yes" /></td>
                                        <td><input type="radio" name="qualifies_evaluation" value="No" /></td>
                                        <td><textarea name="comments_6" class="form-control"></textarea></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="mb-3">
                                <label for="overall_comments" class="form-label">Overall Comments:</label>
                                <textarea id="overall_comments" name="overall_comments" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="examiner" class="form-label">Examiner:</label>
                                <input type="text" id="examiner" name="examiner" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label for="signature" class="form-label">Signature:</label>
                                <input type="text" id="signature" name="signature" class="form-control" />
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fetchProjectDetails() {
            var projectId = document.getElementById("project_id").value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_project_details.php?project_id=" + projectId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var projectDetails = JSON.parse(xhr.responseText);
                    document.getElementById("project_title").value = projectDetails.title;
                    document.getElementById("student_name_1").value = projectDetails.student1;
                    document.getElementById("student_name_2").value = projectDetails.student2;
                    document.getElementById("student_name_3").value = projectDetails.student3;
                    document.getElementById("student_name_4").value = projectDetails.student4;
                    document.getElementById("supervisor").value = projectDetails.supervisor;
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
