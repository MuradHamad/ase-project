<?php
/**
 * Dean Dashboard
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('dean');

$page_title = 'Dean Dashboard';

$user_id = getCurrentUserId();

// Get training request letters statistics
$total_letters = fetchOne("SELECT COUNT(*) as cnt FROM training_request_letters");
$total_letters_count = ($total_letters && isset($total_letters['cnt'])) ? $total_letters['cnt'] : 0;

$issued_this_month = fetchOne(
    "SELECT COUNT(*) as cnt FROM training_request_letters WHERE MONTH(issued_date) = MONTH(NOW()) AND YEAR(issued_date) = YEAR(NOW())"
);
$issued_this_month_count = ($issued_this_month && isset($issued_this_month['cnt'])) ? $issued_this_month['cnt'] : 0;

// Get recent letters
$recent_letters = fetchAll(
    "SELECT l.*, ta.assignment_id, u.full_name AS student_name, s.student_number
     FROM training_request_letters l
     JOIN training_assignments ta ON l.assignment_id = ta.assignment_id
     JOIN users u ON ta.student_id = u.user_id
     JOIN students s ON ta.student_id = s.student_id
     ORDER BY l.issued_date DESC LIMIT 10"
);

// Get pending assignments (not yet assigned a letter)
$pending_assignments = fetchAll(
    "SELECT ta.assignment_id, u.full_name AS student_name, s.student_number, c.company_name, ta.training_start_date
     FROM training_assignments ta
     JOIN users u ON ta.student_id = u.user_id
     JOIN students s ON ta.student_id = s.student_id
     JOIN companies c ON ta.company_id = c.company_id
     WHERE ta.status IN ('assigned', 'in_progress')
     AND NOT EXISTS (SELECT 1 FROM training_request_letters WHERE assignment_id = ta.assignment_id)
     ORDER BY ta.training_start_date ASC"
);

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_letters_count; ?></div>
            <div class="stat-label">Total Letters Issued</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value"><?php echo $issued_this_month_count; ?></div>
            <div class="stat-label">This Month</div>
        </div>
    </div>
</div>

<p>
    <a class="btn btn-primary" href="letters.php">Issue New Letter</a>
</p>

<!-- Pending Assignments (without letters) -->
<?php if (!empty($pending_assignments)): ?>
    <div class="card">
        <div class="card-header">
            <h2>Pending Assignments (Need Letters)</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Company</th>
                            <th>Start Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_assignments as $a): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($a['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($a['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($a['company_name']); ?></td>
                                <td><?php echo htmlspecialchars(formatDate($a['training_start_date'])); ?></td>
                                <td>
                                    <a href="letters.php?assignment_id=<?php echo $a['assignment_id']; ?>" class="btn btn-sm btn-primary">Issue Letter</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Recent Letters -->
<?php if (!empty($recent_letters)): ?>
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>Recent Letters</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Issued Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_letters as $l): ?>
                            <tr>
                                <td><?php echo $l['letter_id']; ?></td>
                                <td><?php echo htmlspecialchars($l['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($l['student_number']); ?></td>
                                <td><?php echo formatDate($l['issued_date']); ?></td>
                                <td>
                                    <a href="letters.php?letter_id=<?php echo $l['letter_id']; ?>" class="btn btn-sm">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
