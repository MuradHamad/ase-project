<?php
/**
 * Company Supervisor Dashboard
 * View trainees assigned to this company and quick actions
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('company_supervisor');

$page_title = 'Company Supervisor Dashboard';

$company_id = $_SESSION['company_id'] ?? null;

// Fetch assignments for this company
$assignments = [];
if ($company_id) {
    $sql = "SELECT ta.assignment_id, u.full_name AS student_name, s.student_number, ta.training_start_date, ta.status
            FROM training_assignments ta
            JOIN users u ON ta.student_id = u.user_id
            JOIN students s ON ta.student_id = s.student_id
            WHERE ta.company_id = ?
            ORDER BY ta.training_start_date DESC";
    $assignments = fetchAll($sql, [$company_id]);
}

include '../includes/header.php';
?>

<div class="container">
    <h2>My Trainees</h2>

    <?php if (empty($assignments)): ?>
        <div class="card">
            <div class="card-body">
                <p>No trainees assigned to your company yet.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Assignment ID</th>
                                <th>Student</th>
                                <th>Student ID</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $a): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($a['assignment_id']); ?></td>
                                    <td><?php echo htmlspecialchars($a['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($a['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars(formatDate($a['training_start_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($a['status']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="view_submission.php?assignment_id=<?php echo $a['assignment_id']; ?>">View Submissions</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
