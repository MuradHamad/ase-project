<?php
/**
 * Approve Stage 1 Report (Company Supervisor)
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('company_supervisor');

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$company_id = $_SESSION['company_id'] ?? null;

if ($assignment_id <= 0) {
    setFlashMessage('Invalid assignment selected', 'error');
    header('Location: dashboard.php');
    exit();
}

// Verify assignment
$assignment = fetchOne("SELECT * FROM training_assignments WHERE assignment_id = ?", [$assignment_id]);
if (!$assignment || $assignment['company_id'] != $company_id) {
    setFlashMessage('You are not authorized to view this assignment', 'error');
    header('Location: dashboard.php');
    exit();
}

// Fetch latest stage1 report for this assignment
$stage1 = fetchOne("SELECT * FROM stage1_reports WHERE assignment_id = ? ORDER BY submitted_at DESC LIMIT 1", [$assignment_id]);

if (!$stage1) {
    setFlashMessage('Stage 1 report not submitted yet.', 'error');
    header('Location: view_submission.php?assignment_id=' . $assignment_id);
    exit();
}

// Handle approval only on POST (do not auto-approve on GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($stage1['company_supervisor_signature'])) {
    $res = executeUpdate("UPDATE stage1_reports SET company_supervisor_signature = 1, company_supervisor_signed_date = NOW() WHERE report_id = ?", [$stage1['report_id']]);
    if ($res !== false) {
        setFlashMessage('Stage 1 report approved', 'success');
    } else {
        setFlashMessage('Failed to approve stage 1 report', 'error');
    }
    header('Location: approve_stage1.php?assignment_id=' . $assignment_id);
    exit();
}


include '../includes/header.php';
?>

<section class="container">
    <h2>Stage 1 Report - Approve</h2>

    <div class="card mb-3">
        <div class="card-header"><h3>Student Submission</h3></div>
        <div class="card-body">
            <div style="white-space: pre-wrap; border:1px solid #eaeaea; padding:12px; background:#fff;">
                <?php echo nl2br(htmlspecialchars($stage1['introduction_text'] . "\n\n" . $stage1['intended_goals'] . "\n\n" . $stage1['company_details'])); ?>
            </div>
            <p style="margin-top:8px;"><strong>Submitted at:</strong> <?php echo formatDate($stage1['submitted_at']); ?></p>
            <p>
                <strong>Company Signed:</strong>
                <?php if (!empty($stage1['company_supervisor_signature'])): ?>
                    Yes
                    <?php if (!empty($stage1['company_supervisor_signed_date'])): ?>
                        — Signed on <?php echo formatDate($stage1['company_supervisor_signed_date']); ?>
                    <?php endif; ?>
                <?php else: ?>
                    No
                <?php endif; ?>
            </p>
        </div>
    </div>

    <form method="post">
        <?php if (empty($stage1['company_supervisor_signature'])): ?>
            <button type="submit" class="btn btn-primary">Approve Stage 1</button>
            <a href="view_submission.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-secondary">Cancel</a>
        <?php else: ?>
            <div class="alert alert-success">This Stage 1 report was approved on <?php echo !empty($stage1['company_supervisor_signed_date']) ? formatDate($stage1['company_supervisor_signed_date']) : 'an earlier date'; ?>.</div>
            <a href="view_submission.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-secondary">Back</a>
        <?php endif; ?>
    </form>
</section>

<?php include '../includes/footer.php'; ?>
