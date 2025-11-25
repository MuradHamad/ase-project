<?php
/**
 * View a report (stage1 or final) for student
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!in_array($type, ['stage1', 'final']) || $id <= 0) {
    setFlashMessage('Invalid report requested', 'error');
    header('Location: reports.php');
    exit();
}

// Load report and verify ownership via assignment -> student
if ($type === 'stage1') {
    $report = fetchOne("SELECT r.*, ta.student_id FROM stage1_reports r JOIN training_assignments ta ON r.assignment_id = ta.assignment_id WHERE r.report_id = ?", [$id]);
} else {
    $report = fetchOne("SELECT r.*, ta.student_id FROM final_reports r JOIN training_assignments ta ON r.assignment_id = ta.assignment_id WHERE r.report_id = ?", [$id]);
}

if (!$report) {
    setFlashMessage('Report not found', 'error');
    header('Location: reports.php');
    exit();
}

if ($report['student_id'] != $_SESSION['student_id']) {
    setFlashMessage('You are not authorized to view this report', 'error');
    header('Location: dashboard.php');
    exit();
}

include '../includes/header.php';
?>

<section class="container">
    <h2><?php echo $type === 'stage1' ? 'Stage 1 Report' : 'Final Report'; ?></h2>
    <p>Submitted at: <?php echo formatDate($report['submitted_at']); ?></p>

    <?php if ($type === 'stage1'): ?>
        <h3>Introduction</h3>
        <div><?php echo nl2br(htmlspecialchars($report['introduction_text'] ?? '')); ?></div>

        <h3>Intended Goals</h3>
        <div><?php echo nl2br(htmlspecialchars($report['intended_goals'] ?? '')); ?></div>

        <h3>Company Details</h3>
        <div><?php echo nl2br(htmlspecialchars($report['company_details'] ?? '')); ?></div>

        <h4>Company Supervisor Signature</h4>
        <p><?php echo htmlspecialchars($report['company_supervisor_signature'] ?? ''); ?> <?php echo $report['company_supervisor_signed_date'] ? '(' . formatDate($report['company_supervisor_signed_date']) . ')' : ''; ?></p>

    <?php else: ?>
        <h3>Acknowledgment</h3>
        <div><?php echo nl2br(htmlspecialchars($report['acknowledgment'] ?? '')); ?></div>

        <h3>Objectives</h3>
        <div><?php echo nl2br(htmlspecialchars($report['objectives'] ?? '')); ?></div>

        <h3>Details of training experience</h3>
        <div><strong>Technical:</strong><br><?php echo nl2br(htmlspecialchars($report['training_experience_technical'] ?? '')); ?></div>
        <div><strong>Personal:</strong><br><?php echo nl2br(htmlspecialchars($report['training_experience_personal'] ?? '')); ?></div>
        <div><strong>Communication:</strong><br><?php echo nl2br(htmlspecialchars($report['training_experience_communication'] ?? '')); ?></div>

        <h3>Suggestions</h3>
        <div><?php echo nl2br(htmlspecialchars($report['suggestions'] ?? '')); ?></div>
    <?php endif; ?>

    <p>
        <a class="btn btn-secondary" href="reports.php">Back</a>
    </p>
</section>

<?php include '../includes/footer.php'; ?>
