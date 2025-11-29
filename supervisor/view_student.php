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

// Fetch assignment and validate supervisor
$assignment = fetchOne(
    "SELECT ta.*, s.student_id, u.full_name AS student_name, s.student_number,
            c.company_name, ac.supervisor_id 
     FROM training_assignments ta
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

// Fetch reports
$stage1 = fetchOne("SELECT * FROM stage1_reports WHERE assignment_id = ?", [$assignment_id]);
$weekly_followups = fetchAll(
    "SELECT * FROM weekly_followups WHERE assignment_id = ? ORDER BY week_number ASC",
    [$assignment_id]
);
$final = fetchOne("SELECT * FROM final_reports WHERE assignment_id = ?", [$assignment_id]);

include '../includes/header.php';
?>

<section class="container">
    <h2>Student Assignment View</h2>

    <div class="card mb-3">
        <div class="card-header">
            <h3><?php echo htmlspecialchars($assignment['student_name']); ?></h3>
            <p>Student ID: <?php echo htmlspecialchars($assignment['student_number']); ?></p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label>Company</label>
                    <p><?php echo htmlspecialchars($assignment['company_name']); ?></p>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Training Start</label>
                    <p><?php echo formatDate($assignment['training_start_date']); ?></p>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Training End</label>
                    <p><?php echo formatDate($assignment['training_end_date']); ?></p>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Status</label>
                    <p><?php echo htmlspecialchars($assignment['status']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h3>Reports Status</h3>

    <!-- Stage 1 Report -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Stage 1 Report</h4>
            <?php if ($stage1): ?>
                <p>Submitted: <?php echo formatDate($stage1['submitted_at']); ?></p>
                <p>Status: <strong><?php echo htmlspecialchars($stage1['status']); ?></strong></p>
                <p>Grade: <strong><?php echo htmlspecialchars($stage1['supervisor_grade'] ?? '-'); ?></strong></p>
                <a href="view_stage1.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-sm btn-primary">View</a>
            <?php else: ?>
                <p>Not submitted yet</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Weekly Reports -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Weekly Follow-Ups</h4>

            <?php if (empty($weekly_followups)): ?>
                <p>No weekly follow-ups submitted yet.</p>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Submitted At</th>
                            <th>Grade</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weekly_followups as $wf): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($wf['week_number']); ?></td>
                                <td><?php echo formatDate($wf['submitted_at']); ?></td>
                                <td><?php echo htmlspecialchars($wf['supervisor_grade'] ?? '-'); ?></td>
                                <td>
                                    <a href="view_week.php?followup_id=<?php echo $wf['followup_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Final Report -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Final Report</h4>
            <?php if ($final): ?>
                <p>Submitted: <?php echo formatDate($final['submitted_at']); ?></p>
                <p>Status: <strong><?php echo htmlspecialchars($final['status']); ?></strong></p>
                <p>Grade: <strong><?php echo htmlspecialchars($final['supervisor_grade'] ?? '-'); ?></strong></p>
                <a href="view_final.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-sm btn-primary">View</a>
            <?php else: ?>
                <p>Not submitted yet</p>
            <?php endif; ?>
        </div>
    </div>

    <a class="btn btn-secondary mt-3" href="dashboard.php">Back to Dashboard</a>
</section>

<?php include '../includes/footer.php'; ?>
