<?php
/**
 * View a student's assignment and their reports (supervisor)
 */

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

// Get assignment and verify this supervisor supervises it
$assignment = fetchOne(
    "SELECT ta.*, s.student_id, u.full_name as student_name, s.student_number,
     c.company_name, ac.supervisor_id FROM training_assignments ta
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN companies c ON ta.company_id = c.company_id
     JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
     WHERE ta.assignment_id = ?",
    [$assignment_id]
);

if (!$assignment || $assignment['supervisor_id'] != $supervisor_id) {
    setFlashMessage('You are not authorized to view this assignment', 'error');
    header('Location: dashboard.php');
    exit();
}

// Get reports
$stage1 = fetchOne("SELECT * FROM stage1_reports WHERE assignment_id = ?", [$assignment_id]);
$weekly_count = fetchOne("SELECT COUNT(*) as cnt FROM weekly_followups WHERE assignment_id = ?", [$assignment_id]);
$final = fetchOne("SELECT * FROM final_reports WHERE assignment_id = ?", [$assignment_id]);

include '../includes/header.php';
?>

<section class="container">
    <h2>Student Assignment View</h2>

    <div class="card">
        <div class="card-header">
            <h3><?php echo htmlspecialchars($assignment['student_name']); ?></h3>
            <p>Student ID: <?php echo htmlspecialchars($assignment['student_number']); ?></p>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Company</label>
                    <p><?php echo htmlspecialchars($assignment['company_name']); ?></p>
                </div>
                <div class="form-group">
                    <label>Training Start</label>
                    <p><?php echo formatDate($assignment['training_start_date']); ?></p>
                </div>
                <div class="form-group">
                    <label>Training End</label>
                    <p><?php echo formatDate($assignment['training_end_date']); ?></p>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <p><?php echo htmlspecialchars($assignment['status']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h3 style="margin-top: 20px;">Reports Status</h3>

    <div class="card">
        <div class="card-body">
            <h4>Stage 1 Report</h4>
            <?php if ($stage1): ?>
                <p>Submitted: <?php echo formatDate($stage1['submitted_at']); ?></p>
                <p>Status: <strong><?php echo htmlspecialchars($stage1['status']); ?></strong></p>
                <a href="grade.php?assignment_id=<?php echo $assignment_id; ?>&type=stage1" class="btn btn-sm btn-primary">Grade</a>
            <?php else: ?>
                <p>Not submitted yet</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card" style="margin-top: 15px;">
        <div class="card-body">
            <h4>Weekly Follow-ups</h4>
            <p>Submitted: <strong><?php echo $weekly_count['cnt']; ?> weeks</strong></p>
        </div>
    </div>

    <div class="card" style="margin-top: 15px;">
        <div class="card-body">
            <h4>Final Report</h4>
            <?php if ($final): ?>
                <p>Submitted: <?php echo formatDate($final['submitted_at']); ?></p>
                <p>Status: <strong><?php echo htmlspecialchars($final['status']); ?></strong></p>
                <a href="grade.php?assignment_id=<?php echo $assignment_id; ?>&type=final" class="btn btn-sm btn-primary">Grade</a>
            <?php else: ?>
                <p>Not submitted yet</p>
            <?php endif; ?>
        </div>
    </div>

    <p style="margin-top: 20px;">
        <a class="btn btn-secondary" href="dashboard.php">Back to Dashboard</a>
    </p>
</section>

<?php include '../includes/footer.php'; ?>
