<?php
/**
 * Approve a weekly follow-up (Company Supervisor)
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('company_supervisor');

$followup_id = isset($_GET['followup_id']) ? intval($_GET['followup_id']) : 0;
$company_id = $_SESSION['company_id'] ?? null;

if ($followup_id <= 0) {
    setFlashMessage('Invalid weekly follow-up specified', 'error');
    header('Location: dashboard.php');
    exit();
}

// Fetch weekly follow-up and related assignment info
$weekly = fetchOne(
    "SELECT wf.*, ta.assignment_id, ta.company_id, s.student_id, u.full_name AS student_name, s.student_number, c.company_name
     FROM weekly_followups wf
     JOIN training_assignments ta ON wf.assignment_id = ta.assignment_id
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN companies c ON ta.company_id = c.company_id
     WHERE wf.followup_id = ?",
    [$followup_id]
);

if (!$weekly || $weekly['company_id'] != $company_id) {
    setFlashMessage('You are not authorized to approve this weekly follow-up', 'error');
    header('Location: dashboard.php');
    exit();
}

// Handle approval only on POST (do not auto-approve on GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($weekly['company_supervisor_signed'])) {
    $res = executeUpdate("UPDATE weekly_followups SET company_supervisor_signed = 1, company_supervisor_signed_date = NOW() WHERE followup_id = ?", [$followup_id]);
    if ($res !== false) {
        setFlashMessage('Weekly follow-up approved', 'success');
    } else {
        setFlashMessage('Failed to approve weekly follow-up', 'error');
    }
    header('Location: approve_week.php?followup_id=' . $followup_id);
    exit();
}

include '../includes/header.php';
?>

<section class="container">
    <h2>Approve Weekly Follow-Up</h2>

    <div class="card mb-3">
        <div class="card-header"><h3>Student & Week</h3></div>
        <div class="card-body">
            <p><strong>Student:</strong> <?php echo htmlspecialchars($weekly['student_name']); ?> (<?php echo htmlspecialchars($weekly['student_number']); ?>)</p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($weekly['company_name']); ?></p>
            <p><strong>Week:</strong> <?php echo htmlspecialchars($weekly['week_number']); ?> (<?php echo formatDate($weekly['week_start_date']); ?> - <?php echo formatDate($weekly['week_end_date']); ?>)</p>
            <p><strong>Submitted at:</strong> <?php echo formatDate($weekly['submitted_at']); ?></p>
            <p>
                <strong>Currently Signed by Company:</strong>
                <?php if (!empty($weekly['company_supervisor_signed'])): ?>
                    Yes
                    <?php if (!empty($weekly['company_supervisor_signed_date'])): ?>
                        — Signed on <?php echo formatDate($weekly['company_supervisor_signed_date']); ?>
                    <?php endif; ?>
                <?php else: ?>
                    No
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3>Tasks / Duties</h3></div>
        <div class="card-body">
            <?php
            $tasks = fetchAll("SELECT * FROM weekly_tasks WHERE followup_id = ? ORDER BY task_order ASC", [$followup_id]);
            if ($tasks): ?>
                <table class="table table-bordered">
                    <thead><tr><th>Order</th><th>Task / Duty</th><th>Notes</th><th>Gained Skills</th></tr></thead>
                    <tbody>
                    <?php foreach ($tasks as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['task_order']); ?></td>
                            <td><?php echo htmlspecialchars($t['tasks_duties']); ?></td>
                            <td><?php echo htmlspecialchars($t['notes']); ?></td>
                            <td><?php echo htmlspecialchars($t['gained_skills']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No tasks recorded for this week.</p>
            <?php endif; ?>
        </div>
    </div>

    <form method="post">
        <?php if (empty($weekly['company_supervisor_signed'])): ?>
            <button type="submit" class="btn btn-primary">Approve Weekly Follow-Up</button>
            <a href="view_submission.php?assignment_id=<?php echo $weekly['assignment_id']; ?>" class="btn btn-secondary">Cancel</a>
        <?php else: ?>
            <div class="alert alert-success">This weekly follow-up was approved on <?php echo !empty($weekly['company_supervisor_signed_date']) ? formatDate($weekly['company_supervisor_signed_date']) : 'an earlier date'; ?>.</div>
            <a href="view_submission.php?assignment_id=<?php echo $weekly['assignment_id']; ?>" class="btn btn-secondary">Back</a>
        <?php endif; ?>
    </form>
</section>

<?php include '../includes/footer.php'; ?>
