<?php
session_start();
include 'config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student's projects
$query = "SELECT id FROM projects WHERE (student1 = ? OR student2 = ? OR student3 = ? OR student4 = ?) AND created_at >= '2025-01-01'";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $student_id, $student_id, $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row['id'];
}
$stmt->close();

$recommended_projects = [];
if (!empty($projects)) {
    $placeholders = implode(',', array_fill(0, count($projects), '?'));
    $query = "SELECT pr.recommended_project_id, p.title, p.description, (pr.similarity_score * 100) AS similarity_score, f.username AS supervisor, v.file_path 
              FROM project_recommendations pr
              JOIN projects p ON pr.recommended_project_id = p.id
              LEFT JOIN faculty f ON p.supervisor = f.faculty_id
              LEFT JOIN videos v ON p.title = v.title
              WHERE pr.project_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat("i", count($projects)), ...$projects);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recommended_projects[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommended Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            animation: fadeIn 0.5s ease-in;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
        }
        .similarity-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.9rem;
            background: rgba(10, 74, 145, 0.9);
        }
        .video-thumbnail {
            height: 200px;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .video-thumbnail:hover {
            transform: scale(0.98);
        }
        .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: rgba(255,255,255,0.8);
            text-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .empty-state {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            opacity: 0.7;
        }
        .project-title {
            color: #0a4a91;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .supervisor-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.8rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container py-5">
    <h1 class="text-center mb-5 heading">Recommended Projects <i class="fas fa-lightbulb ms-2"></i></h1>

    <?php if (!empty($recommended_projects)): ?>
        <div class="row" data-masonry='{"percentPosition": true}'>
            <?php foreach ($recommended_projects as $project): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <?php if (!empty($project['file_path'])): ?>
                                <div class="video-thumbnail position-relative" data-bs-toggle="modal" data-bs-target="#videoModal" 
                                     onclick="document.getElementById('videoSource').src = '<?= htmlspecialchars($project['file_path']) ?>'">
                                    <div class="play-icon"><i class="fas fa-play"></i></div>
                                </div>
                            <?php else: ?>
                                <div class="video-thumbnail bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-video-slash fa-2x text-light"></i>
                                </div>
                            <?php endif; ?>
                            <span class="similarity-badge badge rounded-pill bg-primary">
                                <i class="fas fa-percentage me-1"></i><?= round($project['similarity_score'], 2) ?>%
                            </span>
                        </div>
                        <div class="card-body">
                            <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>
                            <p class="card-text text-muted"><?= htmlspecialchars($project['description']) ?></p>
                            <div class="supervisor-info">
                                <i class="fas fa-user-tie me-2 text-primary"></i>
                                <span class="text-secondary">Supervised by </span>
                                <strong><?= htmlspecialchars($project['supervisor'] ?? 'Not assigned') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="text-center mb-3">
                <i class="fas fa-box-open fa-4x text-secondary mb-4"></i>
                <h3 class="text-muted mb-3">No Recommendations Found</h3>
                <p class="text-muted">We couldn't find any project recommendations based on your current projects.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <video id="videoSource" controls style="width: 100%; height: auto">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
<script>
    // Initialize Masonry layout
    window.onload = () => {
        const masonry = new Masonry('[data-masonry]', {
            itemSelector: '.col-lg-4',
            percentPosition: true
        });
    };

    // Reset video when modal closes
    document.getElementById('videoModal').addEventListener('hidden.bs.modal', () => {
        const video = document.getElementById('videoSource');
        video.pause();
        video.currentTime = 0;
    });
    
</script>

</body>
</html>