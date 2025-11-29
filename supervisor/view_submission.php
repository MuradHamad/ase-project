<?php
/**
 * View a report (stage1 or final) for student
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'View Report';
$student_id = $_SESSION['student_id'];

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!in_array($type, ['stage1', 'final']) || $id <= 0) {
    setFlashMessage('Invalid report requested', 'error');
    header('Location: reports.php');
    exit();
}

// Load report and verify ownership
if ($type === 'stage1') {
    $report = fetchOne("SELECT r.*, ta.student_id FROM stage1_reports r JOIN training_assignments ta ON r.assignment_id = ta.assignment_id WHERE r.report_id = ?", [$id]);
} else {
    $report = fetchOne("SELECT r.*, ta.student_id FROM final_reports r JOIN training_assignments ta ON r.assignment_id = ta.assignment_id WHERE r.report_id = ?", [$id]);
}

if (!$report || $report['student_id'] != $student_id) {
    setFlashMessage('You are not authorized to view this report', 'error');
    header('Location: dashboard.php');
    exit();
}

// Fetch student and assignment details
$student_info = fetchOne(
    "SELECT u.full_name, s.student_number, s.academic_year, s.semester
     FROM students s
     JOIN users u ON s.student_id = u.user_id
     WHERE s.student_id = ?",
    [$student_id]
);

$assignment_details = fetchOne(
    "SELECT ta.*, c.company_name, u.full_name as supervisor_name
     FROM training_assignments ta
     JOIN companies c ON ta.company_id = c.company_id
     JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
     JOIN users u ON ac.supervisor_id = u.user_id
     WHERE ta.assignment_id = ?",
    [$report['assignment_id']]
);

include '../includes/header.php';
?>

<div class="card final-report mt-4">
    <div class="card-header">
        <h2><?php echo $type === 'stage1' ? 'Stage 1 Report' : 'Final Report'; ?></h2>
        <p>Assignment #: <?php echo htmlspecialchars($report['assignment_id']); ?></p>
        <p>Submitted at: <?php echo formatDate($report['submitted_at']); ?></p>
    </div>
    <div class="card-body">

        <!-- Student and Assignment Info -->
        <div class="mb-3">
            <p><strong>Student Name:</strong> <?php echo htmlspecialchars($student_info['full_name']); ?></p>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_info['student_number']); ?></p>
            <p><strong>Academic Year / Semester:</strong> <?php echo htmlspecialchars($student_info['academic_year'] . ' / ' . $student_info['semester']); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($assignment_details['company_name']); ?></p>
            <p><strong>Academic Supervisor:</strong> <?php echo htmlspecialchars($assignment_details['supervisor_name']); ?></p>
        </div>

        <?php if ($type === 'stage1'): ?>
            <h3>Introduction</h3>
            <div class="border p-2 rounded mb-3"><?php echo nl2br(htmlspecialchars($report['introduction_text'] ?? '')); ?></div>

            <h3>Intended Goals</h3>
            <div class="border p-2 rounded mb-3"><?php echo nl2br(htmlspecialchars($report['intended_goals'] ?? '')); ?></div>

            <h3>Company Details</h3>
            <div class="border p-2 rounded mb-3"><?php echo nl2br(htmlspecialchars($report['company_details'] ?? '')); ?></div>

            <h4>Company Supervisor Signature</h4>
            <p><?php echo htmlspecialchars($report['company_supervisor_signature'] ?? ''); ?> <?php echo $report['company_supervisor_signed_date'] ? '(' . formatDate($report['company_supervisor_signed_date']) . ')' : ''; ?></p>

        <?php else: ?>
            <h3>Acknowledgment</h3>
            <div class="border p-2 rounded mb-3"><?php echo nl2br(htmlspecialchars($report['acknowledgment'] ?? '')); ?></div>

            <h3>Objectives</h3>
            <div class="border p-2 rounded mb-3"><?php echo nl2br(htmlspecialchars($report['objectives'] ?? '')); ?></div>

            <h3>Details of Training Experience</h3>
            <div class="border p-2 rounded mb-2"><strong>Technical:</strong><br><?php echo nl2br(htmlspecialchars($report['training_experience_technical'] ?? '')); ?></div>
            <div class="border p-2 rounded mb-2"><strong>Personal:</strong><br><?php echo nl2br(htmlspecialchars($report['training_experience_personal'] ?? '')); ?></div>
            <div class="border p-2 rounded mb-3"><strong>Communication:</strong><br><?php echo nl2br(htmlspecialchars($report['training_experience_communication'] ?? '')); ?></div>

            <h3>Suggestions</h3>
            <div class="border p-2 rounded mb-3"><?php echo nl2br(htmlspecialchars($report['suggestions'] ?? '')); ?></div>
        <?php endif; ?>
    </div>

    <div class="card-footer">
        <a href="reports.php" class="btn btn-secondary">Back</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
