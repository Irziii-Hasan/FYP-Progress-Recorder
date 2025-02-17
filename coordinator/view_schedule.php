<?php
include 'session_coordinator.php';
include 'config.php';

$sql = "SELECT DISTINCT b.batchName AS batch, e.EventName AS event, f.title AS form_title, p.send_to
        FROM presentations p
        LEFT JOIN batches b ON p.batch = b.batchID
        LEFT JOIN events e ON p.type = e.eventID
        LEFT JOIN customized_form f ON p.form_id = f.id
        ORDER BY b.batchName ASC, e.EventName ASC";

$result = $conn->query($sql);

// Fetch all forms from the customized_form table
$forms_sql = "SELECT id, title FROM customized_form";
$forms_result = $conn->query($forms_sql);

// Fetch form assignments for each batch and event
$form_assignments_sql = "SELECT p.batch, p.type, f.title AS form_title
                         FROM presentations p
                         LEFT JOIN customized_form f ON p.form_id = f.id";
$form_assignments_result = $conn->query($form_assignments_sql);
$form_assignments = [];
while ($row = $form_assignments_result->fetch_assoc()) {
    $form_assignments[$row['batch'] . '_' . $row['type']] = $row['form_title'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP | View Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        
        .button-row {
            margin-bottom: 20px;
        }
        .bordered-container {
            border: 1.5px solid #cbcbcb;
            border-radius: 10px;
            padding: 20px;
            background-color: white;
            width: 100%;
            overflow-x: auto;
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

    <div class="container-fluid row" id="content">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Presentations</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="heading">Scheduled Presentations</h2>
                        <div class="col text-end">
                            <a href="list_reports.php" class="btn btn-success">Reports</a>
                            <a href="assignPresentation.php" class="btn btn-primary">Assign Presentation</a>
                        </div>
                        
                    </div>    
                    <div class="row justify-content-center">
                        <div class="col-lg-12 bordered-container">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Scheduled Event</th>
                                        <th class="text-center">View Details</th>
                                        <th class="text-end">Form Selection</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['batch'] . ' - ' . $row['event']); ?></td>
                                                <td class="text-center">
                                                    <form action="viewRecords.php" method="GET">
                                                        <input type="hidden" name="batch" value="<?php echo htmlspecialchars($row['batch']); ?>">
                                                        <input type="hidden" name="event" value="<?php echo htmlspecialchars($row['event']); ?>">
                                                        <button type="submit" class="btn btn-primary">View Records</button>
                                                    </form>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-outline-success"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#formModal" 
                                                            data-batch="<?php echo htmlspecialchars($row['batch']); ?>" 
                                                            data-event="<?php echo htmlspecialchars($row['event']); ?>">
                                                        <?php echo isset($row['form_title']) ? htmlspecialchars($row['form_title']) : 'Select Form'; ?>
                                                    </button>
                                                </td>
                                                <!-- New Publish Button with Confirmation -->
                                                <td class="text-end">
                                                    <?php if ($row['send_to'] === 'all'): ?>
                                                        <form action="publishPresentation.php" method="POST" onsubmit="return confirm('Do you want to unpublish this presentation?')">
                                                            <input type="hidden" name="batch_name" value="<?php echo htmlspecialchars($row['batch']); ?>">
                                                            <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($row['event']); ?>">
                                                            <input type="hidden" name="action" value="unpublish"> <!-- Action for unpublish -->
                                                            <button type="submit" class="btn btn-secondary">Unpublish</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="publishPresentation.php" method="POST" onsubmit="return confirmPublish('<?php echo htmlspecialchars($row['batch']); ?>', '<?php echo htmlspecialchars($row['event']); ?>')">
                                                            <input type="hidden" name="batch_name" value="<?php echo htmlspecialchars($row['batch']); ?>">
                                                            <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($row['event']); ?>">
                                                            <input type="hidden" name="action" value="publish"> <!-- Action for publish -->
                                                            <button type="submit" class="btn btn-warning">Publish</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>

                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No presentations scheduled yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="saveForms.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Select Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="batch" id="modalBatch">
                    <input type="hidden" name="event" id="modalEvent">
                    
                    <div class="mb-3">
                        <label for="formSelect" class="form-label">Form</label>
                        <select name="form_id" id="formSelect" class="form-select" required>
                            <option value="">Select Form</option>
                            <?php while ($form_row = $forms_result->fetch_assoc()): ?>
                                <option value="<?php echo $form_row['id']; ?>"><?php echo htmlspecialchars($form_row['title']); ?></option>
                            <?php endwhile; ?>
                            <?php $forms_result->data_seek(0); // Reset form result pointer ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>


var formModal = document.getElementById('formModal');
formModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; 
    var batch = button.getAttribute('data-batch'); 
    var event = button.getAttribute('data-event'); 

    var modalBatch = formModal.querySelector('#modalBatch');
    var modalEvent = formModal.querySelector('#modalEvent');

    modalBatch.value = batch;
    modalEvent.value = event;
});

function confirmPublish(batch, event) {
    // Display the confirmation dialog to the user
    return confirm(`Do you want to publish the presentation for batch "${batch}" and event "${event}"?`);
}

</script>
</body>
</html>