<?php
/**
 * Student Dashboard
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'Student Dashboard';

$user_id = getCurrentUserId();
$student_id = $_SESSION['student_id'];

// Get student's current training assignment
$sql = "SELECT ta.*, c.company_name, c.email as company_email, c.phone as company_phone,
        u.full_name as supervisor_name, u.email as supervisor_email
        FROM training_assignments ta
        JOIN companies c ON ta.company_id = c.company_id
        JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
        JOIN users u ON ac.supervisor_id = u.user_id
        WHERE ta.student_id = ? 
        ORDER BY ta.created_at DESC 
        LIMIT 1";
$current_assignment = fetchOne($sql, [$student_id]);

// Get student's reports status
$reports_status = [
    'stage1' => null,
    'weekly' => [],
    'final' => null,
    'company_eval' => null
];

if ($current_assignment) {
    // Stage 1 Report
    $stage1 = fetchOne(
        "SELECT * FROM stage1_reports WHERE assignment_id = ?",
        [$current_assignment['assignment_id']]
    );
    $reports_status['stage1'] = $stage1;
    
    // Weekly Follow-ups
    $weekly = fetchAll(
        "SELECT * FROM weekly_followups WHERE assignment_id = ? ORDER BY week_number ASC",
        [$current_assignment['assignment_id']]
    );
    $reports_status['weekly'] = $weekly;
    
    // Final Report
    $final = fetchOne(
        "SELECT * FROM final_reports WHERE assignment_id = ?",
        [$current_assignment['assignment_id']]
    );
    $reports_status['final'] = $final;
    
    // Company Evaluation
    $company_eval = fetchOne(
        "SELECT * FROM company_evaluations WHERE assignment_id = ?",
        [$current_assignment['assignment_id']]
    );
    $reports_status['company_eval'] = $company_eval;
    
    // Get total marks
    $total_marks = fetchOne(
        "SELECT * FROM student_total_marks WHERE assignment_id = ?",
        [$current_assignment['assignment_id']]
    );
}

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $current_assignment ? '✓' : '—'; ?></div>
            <div class="stat-label">Training Assignment</div>
        </div>
        <div class="stat-card <?php echo $reports_status['stage1'] ? 'success' : 'warning'; ?>">
            <div class="stat-value">
                <?php echo $reports_status['stage1'] ? '✓' : '—'; ?>
            </div>
            <div class="stat-label">Stage 1 Report</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo count($reports_status['weekly']); ?></div>
            <div class="stat-label">Weekly Follow-ups</div>
        </div>
        <div class="stat-card <?php echo $reports_status['final'] ? 'success' : 'warning'; ?>">
            <div class="stat-value">
                <?php echo $reports_status['final'] ? '✓' : '—'; ?>
            </div>
            <div class="stat-label">Final Report</div>
        </div>
    </div>
</div>

<?php if ($current_assignment): ?>
    <div class="card">
        <div class="card-header">
            <h2>Current Training Assignment</h2>
            <span class="badge badge-<?php 
                echo $current_assignment['status'] === 'completed' ? 'success' : 
                    ($current_assignment['status'] === 'in_progress' ? 'info' : 'pending'); 
            ?>">
                <?php echo ucfirst(str_replace('_', ' ', $current_assignment['status'])); ?>
            </span>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label><strong>Company:</strong></label>
                    <p><?php echo htmlspecialchars($current_assignment['company_name']); ?></p>
                </div>
                <div class="form-group">
                    <label><strong>Academic Supervisor:</strong></label>
                    <p><?php echo htmlspecialchars($current_assignment['supervisor_name']); ?></p>
                </div>
                <div class="form-group">
                    <label><strong>Training Start Date:</strong></label>
                    <p><?php echo formatDate($current_assignment['training_start_date']); ?></p>
                </div>
                <?php if ($current_assignment['training_end_date']): ?>
                <div class="form-group">
                    <label><strong>Training End Date:</strong></label>
                    <p><?php echo formatDate($current_assignment['training_end_date']); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Reports & Submissions</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Report Type</th>
                            <th>Status</th>
                            <th>Submitted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Stage 1 Report (Company Profile)</td>
                            <td>
                                <?php if ($reports_status['stage1']): ?>
                                    <span class="badge badge-<?php echo $reports_status['stage1']['status'] === 'graded' ? 'success' : 'info'; ?>">
                                        <?php echo ucfirst($reports_status['stage1']['status']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Not Submitted</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $reports_status['stage1'] ? formatDate($reports_status['stage1']['submitted_at']) : '—'; ?>
                            </td>
                            <td>
                                <a href="stage1_form.php" class="btn btn-sm btn-primary"><?php echo $reports_status['stage1'] ? 'Edit' : 'Submit'; ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td>Weekly Follow-ups</td>
                            <td>
                                <span class="badge badge-info">
                                    <?php echo count($reports_status['weekly']); ?> submitted
                                </span>
                            </td>
                            <td>
                                <?php 
                                if (!empty($reports_status['weekly'])) {
                                    echo formatDate(end($reports_status['weekly'])['submitted_at']);
                                } else {
                                    echo '—';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="weekly_followup.php" class="btn btn-sm btn-primary">Submit Weekly</a>
                                <a href="weekly_list.php" class="btn btn-sm btn-secondary">View All</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Final Report</td>
                            <td>
                                <?php if ($reports_status['final']): ?>
                                    <span class="badge badge-<?php echo $reports_status['final']['status'] === 'graded' ? 'success' : 'info'; ?>">
                                        <?php echo ucfirst($reports_status['final']['status']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Not Submitted</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $reports_status['final'] ? formatDate($reports_status['final']['submitted_at']) : '—'; ?>
                            </td>
                            <td>
                                <a href="final_report.php" class="btn btn-sm btn-primary"><?php echo $reports_status['final'] ? 'Edit' : 'Submit'; ?></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (isset($total_marks) && $total_marks['total_mark'] > 0): ?>
    <div class="card">
        <div class="card-header">
            <h2>Total Marks</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Stage 1 Report:</label>
                    <p><strong><?php echo number_format($total_marks['stage1_mark'], 2); ?>/5</strong></p>
                </div>
                <div class="form-group">
                    <label>Weekly Follow-ups:</label>
                    <p><strong><?php echo number_format($total_marks['weekly_followups_mark'], 2); ?></strong></p>
                </div>
                <div class="form-group">
                    <label>Final Report:</label>
                    <p><strong><?php echo number_format($total_marks['final_report_mark'], 2); ?>/20</strong></p>
                </div>
                <div class="form-group">
                    <label>Total Mark:</label>
                    <p><strong style="font-size: 24px; color: var(--primary-color);">
                        <?php echo number_format($total_marks['total_mark'], 2); ?>
                    </strong></p>
                </div>
                <?php if ($total_marks['final_grade']): ?>
                <div class="form-group">
                    <label>Final Grade:</label>
                    <p><strong style="font-size: 24px; color: var(--primary-color);">
                        <?php echo $total_marks['final_grade']; ?>
                    </strong></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    <div class="card">
        <div class="card-body text-center">
            <h2>No Training Assignment Yet</h2>
            <p>You haven't been assigned to a company yet.</p>
            <a href="companies.php" class="btn btn-primary">Browse Companies</a>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>


