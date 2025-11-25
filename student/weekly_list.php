<?php
/**
 * Student Weekly Follow-ups List
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'My Weekly Follow-ups';

$student_id = $_SESSION['student_id'];

// Get current/latest assignment
$assignment = fetchOne(
    "SELECT ta.* FROM training_assignments ta WHERE ta.student_id = ? ORDER BY ta.created_at DESC LIMIT 1",
    [$student_id]
);

if (!$assignment) {
    setFlashMessage('No training assignment found.', 'error');
    header('Location: dashboard.php');
    exit();
}

$followups = fetchAll(
    "SELECT wf.* FROM weekly_followups wf WHERE wf.assignment_id = ? ORDER BY wf.week_number DESC",
    [$assignment['assignment_id']]
);

include '../includes/header.php';
?>

<section class="container">
    <h2>Weekly Follow-ups for Assignment #<?php echo htmlspecialchars($assignment['assignment_id']); ?></h2>

    <p>Training period: <?php echo formatDate($assignment['training_start_date']) . ' - ' . formatDate($assignment['training_end_date']); ?></p>

    <p>
        <a class="btn btn-primary" href="weekly_followup.php">Submit New Weekly Follow-up</a>
    </p>

    <?php if ($followups === false): ?>
        <p class="text-danger">Unable to load weekly follow-ups. Please try again later.</p>
    <?php elseif (empty($followups)): ?>
        <p>No weekly follow-ups submitted yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Week</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Company Signed</th>
                    <th>Academic Signed</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($followups as $f): ?>
                    <tr>
                        <td><?php echo (int)$f['week_number']; ?></td>
                        <td><?php echo formatDate($f['week_start_date']); ?></td>
                        <td><?php echo formatDate($f['week_end_date']); ?></td>
                        <td><?php echo $f['company_supervisor_signed'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $f['academic_supervisor_signed'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo formatDate($f['submitted_at']); ?></td>
                        <td>
                            <a class="btn btn-sm" href="view_weekly.php?followup_id=<?php echo $f['followup_id']; ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
