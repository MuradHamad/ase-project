<?php
/**
 * Supervisor Dashboard
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('supervisor');

$page_title = 'Supervisor Dashboard';

$user_id = getCurrentUserId();
$supervisor_id = $_SESSION['supervisor_id'];

// Get supervisor's students
$students = fetchAll(
    "SELECT ta.*, s.student_number, u.full_name as student_name,
     c.company_name
     FROM training_assignments ta
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN companies c ON ta.company_id = c.company_id
     WHERE ta.academic_supervisor_id = ?
     ORDER BY ta.created_at DESC",
    [$supervisor_id]
);

// Get statistics
$total_students = count($students);
$pending_grades = 0;
$completed = 0;

foreach ($students as $student) {
    // Check for pending grades
    $stage1 = fetchOne(
        "SELECT sr.* FROM stage1_reports sr 
         WHERE sr.assignment_id = ? AND sr.status = 'submitted' 
         AND NOT EXISTS (SELECT 1 FROM stage1_grades WHERE report_id = sr.report_id)",
        [$student['assignment_id']]
    );
    
    if ($stage1) {
        $pending_grades++;
    }
    
    if ($student['status'] === 'completed') {
        $completed++;
    }
}

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_students; ?></div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value"><?php echo $pending_grades; ?></div>
            <div class="stat-label">Pending Grades</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value"><?php echo $completed; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>My Students</h2>
    </div>
    <div class="card-body">
        <?php if (empty($students)): ?>
            <p>You don't have any assigned students yet.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Company</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['company_name']); ?></td>
                                <td><?php echo formatDate($student['training_start_date']); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $student['status'] === 'completed' ? 'success' : 
                                            ($student['status'] === 'in_progress' ? 'info' : 'pending'); 
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $student['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="grade.php?assignment_id=<?php echo $student['assignment_id']; ?>" 
                                       class="btn btn-sm btn-primary">Grade</a>
                                    <a href="view_student.php?assignment_id=<?php echo $student['assignment_id']; ?>" 
                                       class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

