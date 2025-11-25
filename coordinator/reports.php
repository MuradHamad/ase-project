<?php
/**
 * Coordinator Reports
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('coordinator');

$page_title = 'Reports';

$user_id = getCurrentUserId();

// Get summary statistics
$total_students = fetchOne("SELECT COUNT(DISTINCT student_id) as cnt FROM training_assignments");
$total_companies = fetchOne("SELECT COUNT(*) as cnt FROM companies WHERE approval_status = 'approved'");
$completed_assignments = fetchOne("SELECT COUNT(*) as cnt FROM training_assignments WHERE status = 'completed'");
$in_progress = fetchOne("SELECT COUNT(*) as cnt FROM training_assignments WHERE status = 'in_progress'");

// Get student performance data
$student_marks = fetchAll(
    "SELECT stm.*, ta.*, u.full_name as student_name, s.student_number, c.company_name
     FROM student_total_marks stm
     JOIN training_assignments ta ON stm.assignment_id = ta.assignment_id
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN companies c ON ta.company_id = c.company_id
     ORDER BY stm.total_mark DESC LIMIT 20"
);

// Get company statistics
$company_stats = fetchAll(
    "SELECT c.company_id, c.company_name, c.approval_status,
     COUNT(ta.assignment_id) as student_count,
     AVG(stm.total_mark) as avg_student_mark
     FROM companies c
     LEFT JOIN training_assignments ta ON c.company_id = ta.company_id
     LEFT JOIN student_total_marks stm ON ta.assignment_id = stm.assignment_id
     WHERE c.approval_status = 'approved'
     GROUP BY c.company_id
     ORDER BY student_count DESC"
);

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_students['cnt']; ?></div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_companies['cnt']; ?></div>
            <div class="stat-label">Approved Companies</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value"><?php echo $in_progress['cnt']; ?></div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value"><?php echo $completed_assignments['cnt']; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<h2>Student Performance</h2>
<?php if (!empty($student_marks)): ?>
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Company</th>
                            <th>Stage 1</th>
                            <th>Weekly</th>
                            <th>Company Eval</th>
                            <th>Final Report</th>
                            <th>Total</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student_marks as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($m['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($m['company_name']); ?></td>
                                <td><?php echo number_format($m['stage1_mark'], 2); ?></td>
                                <td><?php echo number_format($m['weekly_followups_mark'], 2); ?></td>
                                <td><?php echo number_format($m['company_evaluation_mark'], 2); ?></td>
                                <td><?php echo number_format($m['final_report_mark'], 2); ?></td>
                                <td><strong><?php echo number_format($m['total_mark'], 2); ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($m['final_grade'] ?? '—'); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No student performance data available yet.</p>
<?php endif; ?>

<h2 style="margin-top: 30px;">Company Statistics</h2>
<?php if (!empty($company_stats)): ?>
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Students Assigned</th>
                            <th>Avg Student Mark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($company_stats as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['company_name']); ?></td>
                                <td><?php echo (int)$c['student_count']; ?></td>
                                <td><?php echo $c['avg_student_mark'] ? number_format($c['avg_student_mark'], 2) : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No company data available yet.</p>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
