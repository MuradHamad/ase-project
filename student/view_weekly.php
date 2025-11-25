<?php
/**
 * View a single weekly follow-up (student)
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'View Weekly Follow-up';

$followup_id = isset($_GET['followup_id']) ? intval($_GET['followup_id']) : 0;
if ($followup_id <= 0) {
    setFlashMessage('Invalid follow-up specified', 'error');
    header('Location: weekly_list.php');
    exit();
}

$followup = fetchOne("SELECT wf.*, ta.assignment_id FROM weekly_followups wf JOIN training_assignments ta ON wf.assignment_id = ta.assignment_id WHERE wf.followup_id = ?", [$followup_id]);

if (!$followup) {
    setFlashMessage('Weekly follow-up not found', 'error');
    header('Location: weekly_list.php');
    exit();
}

// Security: ensure this followup belongs to logged-in student
$student_id = $_SESSION['student_id'];
$assign_check = fetchOne("SELECT * FROM training_assignments WHERE assignment_id = ? AND student_id = ?", [$followup['assignment_id'], $student_id]);
if (!$assign_check) {
    setFlashMessage('You are not authorized to view this follow-up', 'error');
    header('Location: dashboard.php');
    exit();
}

$tasks = fetchAll("SELECT * FROM weekly_tasks WHERE followup_id = ? ORDER BY task_order", [$followup_id]);

include '../includes/header.php';
?>

<section class="container">
    <h2>Weekly Follow-up - Week <?php echo (int)$followup['week_number']; ?></h2>

    <p><strong>Period:</strong> <?php echo formatDate($followup['week_start_date']) . ' to ' . formatDate($followup['week_end_date']); ?></p>
    <p><strong>Submitted At:</strong> <?php echo formatDate($followup['submitted_at']); ?></p>
    <p><strong>Company Signed:</strong> <?php echo $followup['company_supervisor_signed'] ? 'Yes' : 'No'; ?> <?php echo $followup['company_supervisor_signed_date'] ? '(' . formatDate($followup['company_supervisor_signed_date']) . ')' : ''; ?></p>
    <p><strong>Academic Signed:</strong> <?php echo $followup['academic_supervisor_signed'] ? 'Yes' : 'No'; ?> <?php echo $followup['academic_supervisor_signed_date'] ? '(' . formatDate($followup['academic_supervisor_signed_date']) . ')' : ''; ?></p>

    <h3>Tasks</h3>
    <?php if ($tasks === false): ?>
        <p class="text-danger">Unable to load tasks.</p>
    <?php elseif (empty($tasks)): ?>
        <p>No tasks recorded for this week.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tasks and Duties</th>
                    <th>Notes</th>
                    <th>Gained Skills</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $i => $t): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo nl2br(htmlspecialchars($t['tasks_duties'])); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($t['notes'])); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($t['gained_skills'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p>
        <a class="btn btn-secondary" href="weekly_list.php">Back to list</a>
    </p>
</section>

<?php include '../includes/footer.php'; ?>
