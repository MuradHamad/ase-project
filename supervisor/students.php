<?php
require_once __DIR__ . '/../includes/header.php';
requireRole('supervisor');

$page_title = 'My Students';

$supervisor_id = getCurrentUserId();

$sql = "SELECT ta.assignment_id, ta.training_start_date, ta.training_end_date, ta.status,
               s.student_id, s.student_number, u.full_name AS student_name,
               c.company_name
        FROM training_assignments ta
        JOIN students s ON ta.student_id = s.student_id
        JOIN users u ON s.student_id = u.user_id
        LEFT JOIN companies c ON ta.company_id = c.company_id
        WHERE ta.academic_supervisor_id = ?
        ORDER BY ta.training_start_date DESC";

$rows = fetchAll($sql, [$supervisor_id]);

?>
<section class="container">
    <h2>My Students</h2>

    <?php if ($rows === false): ?>
        <p class="text-danger">Unable to load students. Please try again later.</p>
    <?php elseif (empty($rows)): ?>
        <p>No assigned students found.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Student No.</th>
                    <th>Company</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['student_number']); ?></td>
                    <td><?php echo htmlspecialchars($r['company_name']); ?></td>
                    <td><?php echo formatDate($r['training_start_date']); ?></td>
                    <td><?php echo formatDate($r['training_end_date']); ?></td>
                    <td><?php echo htmlspecialchars($r['status']); ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/supervisor/grade.php?assignment_id=<?php echo $r['assignment_id']; ?>">Grade</a>
                        <a class="btn btn-sm" href="<?php echo BASE_URL; ?>/supervisor/view_assignment.php?assignment_id=<?php echo $r['assignment_id']; ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
