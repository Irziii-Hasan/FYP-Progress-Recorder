<?php
include 'session_coordinator.php';
include 'config.php';

// Get the selected batch and event type from the form submission
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$event = isset($_GET['event']) ? $_GET['event'] : '';

if ($batch && $event) {
    // Fetch records based on the selected batch and event type
    
   $sql = "
   SELECT 
    p.presentation_id, 
    b.batchName AS batch, 
    r.room_number, 
    p.date, 
    p.time, 
    e.EventName AS event_name, 
    pr.project_id AS project_id,
    pr.title AS project_title, 
    GROUP_CONCAT(DISTINCT i.username ORDER BY i.username ASC SEPARATOR ', ') AS internal_evaluator,
    GROUP_CONCAT(DISTINCT ex.name ORDER BY ex.name ASC SEPARATOR ', ') AS external_evaluator,
    GROUP_CONCAT(DISTINCT s.username ORDER BY s.username ASC SEPARATOR ', ') AS student_names
FROM presentations p
LEFT JOIN rooms r ON p.room_id = r.room_id
LEFT JOIN projects pr ON p.project_id = pr.id
LEFT JOIN student s ON s.student_id IN (pr.student1, pr.student2, pr.student3, pr.student4)
LEFT JOIN faculty i ON FIND_IN_SET(i.faculty_id, p.internal_evaluator_id)
LEFT JOIN external ex ON p.external_evaluator_id = ex.external_id
LEFT JOIN batches b ON p.batch = b.batchID
LEFT JOIN events e ON p.type = e.eventID
WHERE b.batchName = ? AND e.EventName = ?
GROUP BY 
    pr.project_id, 
    pr.title, 
    p.date, 
    p.time, 
    r.room_number, 
    b.batchName, 
    e.EventName;

";




    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $batch, $event);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no batch or event is selected, display a message
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP | Schedule Presentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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

        .add-evaluator-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
    <script>
        function updateProjects() {
            var batch = document.getElementById('batch').value;
            var type = document.getElementById('type').value;
            var projectsSelect = document.getElementById('projects');
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    projectsSelect.innerHTML = '<option value="" disabled selected>Select here</option>';
                    response.forEach(function(project) {
                        var option = document.createElement('option');
                        option.value = project.id;
                        option.textContent = project.title;
                        projectsSelect.appendChild(option);
                    });
                }
            };

            xhttp.open("GET", "?batch=" + batch + "&type=" + type, true);
            xhttp.send();
        }



        function updateRooms() {
            var date = document.getElementById('date').value;
            var time = document.getElementById('time').value;
            var roomSelect = document.getElementById('room_number');
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    roomSelect.innerHTML = '<option value="" disabled selected>Select here</option>';
                    response.forEach(function(room) {
                        var option = document.createElement('option');
                        option.value = room.room_id;
                        option.textContent = room.room_number;
                        roomSelect.appendChild(option);
                    });
                }
            };

            xhttp.open("GET", "?date=" + date + "&time=" + time, true);
            xhttp.send();
        }
        function addEvaluator(type) {
            var containerId = type === 'internal' ? 'internalEvaluatorsContainer' : 'externalEvaluatorsContainer';
            var container = document.getElementById(containerId);

            var newEvaluatorDiv = document.createElement('div');
            newEvaluatorDiv.className = 'position-relative mb-2';

            var newSelect = document.createElement('select');
            newSelect.className = 'form-control';
            newSelect.name = type === 'internal' ? 'internalEvaluators[]' : 'externalEvaluators[]';

            var defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            defaultOption.textContent = 'Select here';

            newSelect.appendChild(defaultOption);

            <?php foreach ($internalEvaluators as $evaluator): ?>
            if (type === 'internal') {
                var option = document.createElement('option');
                option.value = '<?php echo htmlspecialchars($evaluator['faculty_id']); ?>';
                option.textContent = '<?php echo htmlspecialchars($evaluator['username']); ?>';
                newSelect.appendChild(option);
            }
            <?php endforeach; ?>

            <?php foreach ($externalEvaluators as $evaluator): ?>
            if (type === 'external') {
                var option = document.createElement('option');
                option.value = '<?php echo htmlspecialchars($evaluator['external_id']); ?>';
                option.textContent = '<?php echo htmlspecialchars($evaluator['name']); ?>';
                newSelect.appendChild(option);
            }
            <?php endforeach; ?>

            newEvaluatorDiv.appendChild(newSelect);

            var removeIcon = document.createElement('span');
            removeIcon.className = 'bi bi-x-circle-fill text-danger add-evaluator-icon';
            removeIcon.onclick = function () {
                newEvaluatorDiv.remove();
            };

            newEvaluatorDiv.appendChild(removeIcon);
            container.appendChild(newEvaluatorDiv);
        }

    </script>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="wrapper">
  <?php include 'sidebar.php'; ?>

  <div class="container-fluid" id="content">
    <div class="row">
      <div class="col-md-12">
      <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item"><a href="view_schedule.php">Presentations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Presentations</li>
          </ol>
        </nav>
        <div class="container mt-5" style="width: 650px;">
            <div class="form-all mt-4 mb-5">
            <h2 class="text-center form-heading">Schedule Presentation</h2>
                <form action="" method="post">
                <div class="mb-3">
                    <label for="type" class="form-label">Event Type</label>
                    <select name="type" id="type" class="form-control" onchange="updateProjects();" required>
                        <option value="" disabled selected>Select here</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['eventID']); ?>"><?php echo htmlspecialchars($type['EventName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="batch" class="form-label">Batch</label>
                    <select name="batch" id="batch" class="form-control" onchange="updateProjects();" required>
                        <option value="" disabled selected>Select here</option>
                        <?php foreach ($batches as $batch): ?>
                            <option value="<?php echo htmlspecialchars($batch['batchID']); ?>"><?php echo htmlspecialchars($batch['batchName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>


                    <div class="mb-3">
                        <label for="projects" class="form-label">Projects:</label>
                        <select class="form-control" id="projects" name="projects" required>
                            <option value="" disabled selected>Select here</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date:</label>
                        <input type="date" class="form-control" id="date" name="date" onchange="updateRooms()" required>
                    </div>

                    <div class="mb-3">
                        <label for="time" class="form-label">Time:</label>
                        <input type="time" class="form-control" id="time" name="time" onchange="updateRooms()" required>
                    </div>

                    <div class="mb-3">
                        <label for="room_number" class="form-label">Room:</label>
                        <select class="form-control" id="room_number" name="room_number" required>
                            <option value="" disabled selected>Select here</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="internalEvaluators" class="form-label">Internal Evaluator(s):</label>
                        <div id="internalEvaluatorsContainer">
                            <div class="position-relative mb-2">
                                <select class="form-control" name="internalEvaluators[]" required>
                                    <option value="" disabled selected>Select here</option>
                                    <?php foreach ($internalEvaluators as $evaluator): ?>
                                        <option value="<?php echo htmlspecialchars($evaluator['faculty_id']); ?>"><?php echo htmlspecialchars($evaluator['username']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- This icon should be non-functional for the first internal evaluator -->
                                <i class="bi bi-plus-circle text-success add-evaluator-icon" onclick="addEvaluator('internal');"></i>
                                </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="externalEvaluator" class="form-label">External Evaluator(s) (Optional):</label>
                        <div id="externalEvaluatorsContainer">
                            <div class="position-relative mb-2">
                                <select class="form-control" name="externalEvaluators[]">
                                    <option value="" disabled selected>Select here</option>
                                    <?php foreach ($externalEvaluators as $evaluator): ?>
                                        <option value="<?php echo htmlspecialchars($evaluator['external_id']); ?>"><?php echo htmlspecialchars($evaluator['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="bi bi-plus-circle text-success add-evaluator-icon" onclick="addEvaluator('external');"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Schedule Presentation</button>
                </form>
            </div>
        </div>
    </div>
    </div>
  </div>

</body>
</html>
