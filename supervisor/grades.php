<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('supervisor');

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
if ($assignment_id <= 0) {
    setFlashMessage('Invalid assignment specified', 'error');
    header('Location: dashboard.php');
    exit();
}

$supervisor_id = $_SESSION['supervisor_id'];

// Fetch assignment
$assignment = fetchOne(
    "SELECT ta.*, s.student_number, u.full_name AS student_name, ac.supervisor_id
     FROM training_assignments ta
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
     WHERE ta.assignment_id = ?",
    [$assignment_id]
);

if (!$assignment || $assignment['supervisor_id'] != $supervisor_id) {
    setFlashMessage('You are not authorized to view this assignment', 'error');
    header('dashboard.php');
    exit();
}

// Fetch submissions
$stage1 = fetchOne("SELECT * FROM stage1_reports WHERE assignment_id = ?", [$assignment_id]);
$weekly_followups = fetchAll("SELECT * FROM weekly_followups WHERE assignment_id = ? ORDER BY week_number ASC", [$assignment_id]);
$final = fetchOne("SELECT * FROM final_reports WHERE assignment_id = ?", [$assignment_id]);

// Calculate total marks
$total_marks = 0;
if ($stage1 && is_numeric($stage1['supervisor_grade'])) $total_marks += $stage1['supervisor_grade'];
foreach ($weekly_followups as $w) {
    if (is_numeric($w['supervisor_grade'])) $total_marks += $w['supervisor_grade'];
}
if ($final && is_numeric($final['supervisor_grade'])) $total_marks += $final['supervisor_grade'];

include '../includes/header.php';
?>

<section class="container">
    <h2>Grades for <?php echo htmlspecialchars($assignment['student_name']); ?></h2>
    <p>Student ID: <?php echo htmlspecialchars($assignment['student_number']); ?></p>

    <!-- Stage 1 -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Stage 1 Report</h4>
            <?php if ($stage1): ?>
                <p>Status: <?php echo htmlspecialchars($stage1['status']); ?></p>
                <p>Grade: <?php echo htmlspecialchars($stage1['supervisor_grade']); ?></p>
                <p>Comments: <?php echo htmlspecialchars($stage1['supervisor_comments']); ?></p>
                <a href="view_stage1.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-sm btn-primary">View</a>
            <?php else: ?>
                <p>Not submitted yet</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Weekly Follow-ups -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Weekly Follow-ups</h4>
            <?php if ($weekly_followups): ?>
                <ul class="list-group">
                    <?php foreach ($weekly_followups as $w): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Week <?php echo $w['week_number']; ?> (<?php echo formatDate($w['week_start_date']); ?> - <?php echo formatDate($w['week_end_date']); ?>)
                            <span>
                                Grade: <?php echo htmlspecialchars($w['supervisor_grade']); ?>
                                <a href="view_week.php?followup_id=<?php echo $w['followup_id']; ?>" class="btn btn-sm btn-primary ms-2">View</a>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No weekly follow-ups submitted yet</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Final Report -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Final Report</h4>
            <?php if ($final): ?>
                <p>Status: <?php echo htmlspecialchars($final['status']); ?></p>
                <p>Grade: <?php echo htmlspecialchars($final['supervisor_grade']); ?></p>
                <p>Comments: <?php echo htmlspecialchars($final['supervisor_comments']); ?></p>
                <a href="view_final.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-sm btn-primary">View</a>
            <?php else: ?>
                <p>Not submitted yet</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Total -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Total Marks</h4>
            <p><?php echo htmlspecialchars($total_marks); ?></p>
        </div>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</section>

<?php include '../includes/footer.php'; ?>
