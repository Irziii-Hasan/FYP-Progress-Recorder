<?php
if (isset($_GET['id'])) {
    $project_id = intval($_GET['id']);

    // Database connection
    include 'config.php';

    // Fetch project data
    $sql = "SELECT projects.id, projects.project_id, projects.title, projects.description, 
            student1.username AS student1_name, 
            student2.username AS student2_name, 
            student3.username AS student3_name, 
            student4.username AS student4_name, 
            supervisor.username AS supervisor_name, 
            co_supervisor.username AS co_supervisor_name,
            external.name AS external_supervisor_name,
            batches.batchName AS batch_name, 
            DATE_FORMAT(projects.created_at, '%Y-%m-%d') AS created_at
            FROM projects 
            LEFT JOIN student AS student1 ON projects.student1 = student1.student_id
            LEFT JOIN student AS student2 ON projects.student2 = student2.student_id
            LEFT JOIN student AS student3 ON projects.student3 = student3.student_id
            LEFT JOIN student AS student4 ON projects.student4 = student4.student_id
            LEFT JOIN faculty AS supervisor ON projects.supervisor = supervisor.faculty_id
            LEFT JOIN faculty AS co_supervisor ON projects.co_supervisor = co_supervisor.faculty_id
            LEFT JOIN external ON projects.external_supervisor = external.external_id
            LEFT JOIN batches ON projects.batch = batches.batchID
            WHERE projects.id = $project_id";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();

        $students = [];
        if (!empty($project['student1_name'])) $students[] = $project['student1_name'];
        if (!empty($project['student2_name'])) $students[] = $project['student2_name'];
        if (!empty($project['student3_name'])) $students[] = $project['student3_name'];
        if (!empty($project['student4_name'])) $students[] = $project['student4_name'];

        echo json_encode([
            'project_id' => $project['project_id'],

            'title' => $project['title'],
            'description' => $project['description'],
            'supervisor_name' => $project['supervisor_name'],
            'co_supervisor_name' => $project['co_supervisor_name'],
            'external_supervisor_name' => $project['external_supervisor_name'],
            'students' => implode(', ', $students),
            'batch_name' => $project['batch_name'],
            'created_at' => $project['created_at']
        ]);
    } else {
        echo json_encode(['error' => 'Project not found.']);
    }

    $conn->close();
}
?>
