<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('supervisor');

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
if ($assignment_id <= 0) {
    setFlashMessage('Invalid assignment specified', 'error');
    header('Location: dashboard.php');
    exit();
}

$supervisor_id = $_SESSION['supervisor_id'];

// Fetch assignment
$assignment = fetchOne(
    "SELECT ta.*, s.student_number, u.full_name AS student_name, ac.supervisor_id
     FROM training_assignments ta
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
     WHERE ta.assignment_id = ?",
    [$assignment_id]
);

if (!$assignment || $assignment['supervisor_id'] != $supervisor_id) {
    setFlashMessage('You are not authorized to view this assignment', 'error');
    header('dashboard.php');
    exit();
}

// Fetch submissions
$stage1 = fetchOne("SELECT * FROM stage1_reports WHERE assignment_id = ?", [$assignment_id]);
$weekly_followups = fetchAll("SELECT * FROM weekly_followups WHERE assignment_id = ? ORDER BY week_number ASC", [$assignment_id]);
$final = fetchOne("SELECT * FROM final_reports WHERE assignment_id = ?", [$assignment_id]);

// Calculate total marks
$total_marks = 0;
if ($stage1 && is_numeric($stage1['supervisor_grade'])) $total_marks += $stage1['supervisor_grade'];
foreach ($weekly_followups as $w) {
    if (is_numeric($w['supervisor_grade'])) $total_marks += $w['supervisor_grade'];
}
if ($final && is_numeric($final['supervisor_grade'])) $total_marks += $final['supervisor_grade'];

include '../includes/header.php';
?>

<section class="container">
    <h2>Grades for <?php echo htmlspecialchars($assignment['student_name']); ?></h2>
    <p>Student ID: <?php echo htmlspecialchars($assignment['student_number']); ?></p>

    <div class="card mb-3">
        <div class="card-body">
            <h4>Submissions Overview</h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Submission</th>
                        <th>Week / Date</th>
                        <th>Grade</th>
                        <th>Comments</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Stage 1 -->
                    <tr>
                        <td>Stage 1 Report</td>
                        <td>-</td>
                        <td><?php echo htmlspecialchars($stage1['supervisor_grade'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($stage1['supervisor_comments'] ?? '-'); ?></td>
                        <td>
                            <?php if ($stage1): ?>
                                <a href="view_stage1.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-sm btn-primary">View</a>
                            <?php else: ?>
                                Not submitted
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Weekly Follow-ups -->
                    <?php if ($weekly_followups): ?>
                        <?php foreach ($weekly_followups as $w): ?>
                            <tr>
                                <td>Week <?php echo $w['week_number']; ?> Follow-up</td>
                                <td><?php echo formatDate($w['week_start_date']); ?> - <?php echo formatDate($w['week_end_date']); ?></td>
                                <td><?php echo htmlspecialchars($w['supervisor_grade'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($w['supervisor_comments'] ?? '-'); ?></td>
                                <td>
                                    <a href="view_week.php?followup_id=<?php echo $w['followup_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No weekly follow-ups submitted yet</td>
                        </tr>
                    <?php endif; ?>

                    <!-- Final Report -->
                    <tr>
                        <td>Final Report</td>
                        <td>-</td>
                        <td><?php echo htmlspecialchars($final['supervisor_grade'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($final['supervisor_comments'] ?? '-'); ?></td>
                        <td>
                            <?php if ($final): ?>
                                <a href="view_final.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-sm btn-primary">View</a>
                            <?php else: ?>
                                Not submitted
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Total Marks -->
    <div class="card mb-3">
        <div class="card-body">
            <h4>Total Marks</h4>
            <p><?php echo htmlspecialchars($total_marks); ?></p>
        </div>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</section>

<?php include '../includes/footer.php'; ?>
