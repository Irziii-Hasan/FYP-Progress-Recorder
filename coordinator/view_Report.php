<?php
include 'session_coordinator.php';
include 'config.php';

// Get the selected batch, event type, and columns from the query parameters
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$event = isset($_GET['event']) ? $_GET['event'] : '';
$selected_columns = isset($_GET['columns']) ? $_GET['columns'] : [];

// Get heading and subheading from query parameters
$heading = isset($_GET['heading']) ? $_GET['heading'] : 'Report';
$subheading = isset($_GET['subheading']) ? $_GET['subheading'] : '';

// Fetch records based on the selected batch and event type
if ($batch && $event) {
    $sql = "SELECT b.batchName AS batch, r.room_number, p.date, p.time, e.EventName AS event_name, pr.title AS project_title, 
               GROUP_CONCAT(DISTINCT i.username SEPARATOR ', ') AS internal_evaluator, 
               GROUP_CONCAT(DISTINCT ex.name SEPARATOR ', ') AS external_evaluator,
               CONCAT_WS(', ', s1.username, s2.username, s3.username, s4.username) AS student_name
        FROM presentations p
        LEFT JOIN rooms r ON p.room_id = r.room_id
        LEFT JOIN projects pr ON p.project_id = pr.id
        LEFT JOIN faculty i ON p.internal_evaluator_id = i.faculty_id
        LEFT JOIN external ex ON p.external_evaluator_id = ex.external_id
        LEFT JOIN batches b ON p.batch = b.batchID
        LEFT JOIN events e ON p.type = e.eventID
        LEFT JOIN student s1 ON pr.student1 = s1.student_id
        LEFT JOIN student s2 ON pr.student2 = s2.student_id
        LEFT JOIN student s3 ON pr.student3 = s3.student_id
        LEFT JOIN student s4 ON pr.student4 = s4.student_id
        WHERE b.batchName = ? AND e.EventName = ?
        GROUP BY b.batchName, r.room_number, p.date, p.time, e.EventName, pr.title";

    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $batch, $event);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP | View Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.14/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        /* Hide everything except for the reportContainer during print */
        @media print {
            body * {
                visibility: hidden;  /* Hide all elements */
            }

            #reportContainer, #reportContainer * {
                visibility: visible;  /* Make the report container and its contents visible */
            }

            #reportContainer {
                position: absolute;
                top: 0;
                left: 20mm;  /* Add left margin to prevent text from being cut off */
                right: 0;
            }

            /* Hide print and download buttons */
            .btn {
                display: none;
            }

            /* Excel-like table styling for print */
            table {
                width: 100%;
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid black;  /* Add solid borders to table cells */
                padding: 8px;  /* Add padding for readability */
                text-align: left;  /* Left-align the text in the table cells */
            }

            th {
                background-color: #f2f2f2;  /* Light grey background for table headers */
            }
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
                        <li class="breadcrumb-item"><a href="view_Schedule.php">View Schedule</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Records</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="text-end">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#saveReportModal">Save Report</button>
                    </div>

                    <!-- Form for Heading and Subheading -->
                    <form id="headingForm" class="mb-4">
                        <input type="hidden" name="batch" value="<?php echo htmlspecialchars($batch); ?>">
                        <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
                        <input type="hidden" name="columns[]" value="<?php echo htmlspecialchars(implode(',', $selected_columns)); ?>">
                        <div class="mb-3">
                            <label for="heading" class="form-label">Report Heading:</label>
                            <input type="text" class="form-control" id="heading" name="heading" value="<?php echo htmlspecialchars($heading); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="subheading" class="form-label">Report Subheading:</label>
                            <input type="text" class="form-control" id="subheading" name="subheading" value="<?php echo htmlspecialchars($subheading); ?>">
                        </div>
                        <button type="button" class="btn btn-primary" onclick="updateReport()">Update Report</button>
                    </form>

                    <!-- Report Display -->
                    <div id="reportContainer">
                        <h2 class="text-center mb-4" id="reportHeading"><?php echo htmlspecialchars($heading); ?></h2>
                        <h4 class="text-center mb-4" id="reportSubheading"><?php echo htmlspecialchars($subheading); ?></h4>
                        <table class="table table-striped mt-4">
                            <thead>
                                <tr>
                                    <?php if (in_array('batch', $selected_columns)) echo '<th>Batch</th>'; ?>
                                    <?php if (in_array('room_number', $selected_columns)) echo '<th>Room</th>'; ?>
                                    <?php if (in_array('date', $selected_columns)) echo '<th>Date</th>'; ?>
                                    <?php if (in_array('time', $selected_columns)) echo '<th>Time</th>'; ?>
                                    <?php if (in_array('event_name', $selected_columns)) echo '<th>Event</th>'; ?>
                                    <?php if (in_array('project_title', $selected_columns)) echo '<th>Project Title</th>'; ?>
                                    <?php if (in_array('student_name', $selected_columns)) echo '<th>Student Names</th>'; ?>
                                    <?php if (in_array('internal_evaluator', $selected_columns)) echo '<th>Internal Evaluator</th>'; ?>
                                    <?php if (in_array('external_evaluator', $selected_columns)) echo '<th>External Evaluator</th>'; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): 
                                        // Format the date and time as needed
                                        $formatted_date = date('d-m-Y', strtotime($row['date']));
                                        $formatted_time = date('h:i A', strtotime($row['time']));
                                    ?>
                                        <tr>
                                            <?php if (in_array('batch', $selected_columns)) echo '<td>' . htmlspecialchars($row['batch']) . '</td>'; ?>
                                            <?php if (in_array('room_number', $selected_columns)) echo '<td>' . htmlspecialchars($row['room_number']) . '</td>'; ?>
                                            <?php if (in_array('date', $selected_columns)) echo '<td>' . htmlspecialchars($formatted_date) . '</td>'; ?>
                                            <?php if (in_array('time', $selected_columns)) echo '<td>' . htmlspecialchars($formatted_time) . '</td>'; ?>
                                            <?php if (in_array('event_name', $selected_columns)) echo '<td>' . htmlspecialchars($row['event_name']) . '</td>'; ?>
                                            <?php if (in_array('project_title', $selected_columns)) echo '<td>' . htmlspecialchars($row['project_title']) . '</td>'; ?>
                                            <?php if (in_array('student_name', $selected_columns)) echo '<td>' . htmlspecialchars($row['student_name']) . '</td>'; ?>
                                            <?php if (in_array('internal_evaluator', $selected_columns)) echo '<td>' . htmlspecialchars($row['internal_evaluator']) . '</td>'; ?>
                                            <?php if (in_array('external_evaluator', $selected_columns)) echo '<td>' . htmlspecialchars($row['external_evaluator']) . '</td>'; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No records found for this batch and event.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Buttons for Download and Print -->
                    <div class="text-end mt-4">
                        <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>
                        <button class="btn btn-secondary" onclick="window.print()">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="saveReportModal" tabindex="-1" aria-labelledby="saveReportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="saveReportModalLabel">Save Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="saveReportForm">
            <div class="mb-3">
                <label for="reportHeading" class="form-label">Report Heading</label>
                <input type="text" class="form-control" id="reportHeading" name="heading" required>
            </div>
            
            <div class="mb-3">
                <label for="recipient" class="form-label">Who is this report for?</label>
                <select class="form-select" name="recipient" id="recipient" required>
                    <option value="">Select Recipient</option>
                    <option value="external">External Evaluator</option>
                    <option value="internal">Internal Evaluator</option>
                    <option value="students">Students</option>
                    <option value="faculty">Faculty</option>
                    <option value="external_internal">External & Internal Both</option>
                </select>
            </div>
            
            <input type="hidden" id="batch" name="batch" value="<?php echo htmlspecialchars($batch); ?>">
            <input type="hidden" id="eventID" name="eventID" value="<?php echo htmlspecialchars($event); ?>">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="saveReport()">Save Report</button>
      </div>
    </div>
  </div>
</div>

<script>
async function updateReport() {
    const form = document.getElementById('headingForm');
    const formData = new FormData(form);

    try {
        const response = await fetch('update_report.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Update headings on the page
            document.getElementById('reportHeading').innerText = data.heading;
            document.getElementById('reportSubheading').innerText = data.subheading;
        } else {
            console.error('Error updating report:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const pageWidth = doc.internal.pageSize.getWidth();

    // Get report heading and subheading
    const reportHeading = document.getElementById('reportHeading').innerText || 'Report';
    const reportSubheading = document.getElementById('reportSubheading').innerText;

    // Set styles for the heading
    const headingFontSize = 16;  // Larger font size for heading
    const headingColor = [0, 0, 139];  // Dark blue color (RGB)

    // Center the headings
    const headingX = (pageWidth - doc.getTextWidth(reportHeading)) / 2;
    const subheadingX = (pageWidth - doc.getTextWidth(reportSubheading)) / 2;

    // Set the heading and subheading
    doc.setFontSize(headingFontSize);
    doc.setTextColor(...headingColor);  // Set heading color
    doc.setFont('helvetica', 'bold');  // Set heading to bold
    doc.text(reportHeading, headingX, 20);
    
    doc.setFontSize(16);  // Normal font size for subheading
    doc.setTextColor(0, 0, 0);  // Black color for subheading
    doc.setFont('helvetica', 'normal');  // Regular font for subheading
    doc.text(reportSubheading, subheadingX, 30);  // Adjust the Y position for the subheading

    // Add some spacing between the subheading and the table
    const tableStartY = 40;

    // Capture the table and style it as a simple table
    const table = document.querySelector('table');

    // Use autoTable to generate a simple table with bold headers
    doc.autoTable({
        html: table,
        startY: tableStartY,
        theme: 'plain',  // This ensures the table is as simple as possible
        styles: {
            fontSize: 10,  // Set font size
            cellPadding: 3,  // Adjust cell padding
            textColor: [0, 0, 0],  // Black text
            lineColor: [0, 0, 0],  // Black border lines
            lineWidth: 0.1  // Thin line for cell borders
        },
        headStyles: {
            fillColor: [255, 255, 255],  // Remove any background color in headers
            textColor: [0, 0, 0],  // Black text for headers
            fontStyle: 'bold'  // Bold font for headers
        }
    });

    // Use the heading as the file name for the PDF
    doc.save(`${reportHeading}.pdf`);
}

function saveReport() {
    // Get report heading and subheading
    const reportHeading = document.getElementById('reportHeading').innerText || 'Report';
    const reportSubheading = document.getElementById('reportSubheading').innerText;

    // Generate the PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const pageWidth = doc.internal.pageSize.getWidth();
    
    // Add headings
    const headingFontSize = 16;
    const headingColor = [0, 0, 139];
    
    const headingX = (pageWidth - doc.getTextWidth(reportHeading)) / 2;
    const subheadingX = (pageWidth - doc.getTextWidth(reportSubheading)) / 2;
    
    doc.setFontSize(headingFontSize);
    doc.setTextColor(...headingColor);
    doc.setFont('helvetica', 'bold');
    doc.text(reportHeading, headingX, 20);
    
    doc.setFontSize(16);
    doc.setTextColor(0, 0, 0);
    doc.setFont('helvetica', 'normal');
    doc.text(reportSubheading, subheadingX, 30);

    // Capture the table and add it to the PDF
    const table = document.querySelector('table');
    doc.autoTable({
        html: table,
        startY: 40,
        theme: 'plain',
        styles: {
            fontSize: 10,
            cellPadding: 3,
            textColor: [0, 0, 0],
            lineColor: [0, 0, 0],
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [255, 255, 255],
            textColor: [0, 0, 0],
            fontStyle: 'bold'
        }
    });

    // Convert the PDF to a Blob
    const pdfBlob = doc.output('blob');

    // Create FormData and append the Blob
    const form = document.getElementById('saveReportForm');
    const formData = new FormData(form);
    formData.append('pdf', pdfBlob, `${reportHeading}.pdf`);

    // Send the form data and the PDF to the server
    fetch('save_report.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Report saved successfully!');
            window.location.reload();
        } else {
            alert('Failed to save report: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
</body>
</html>
