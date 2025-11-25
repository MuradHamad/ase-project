<?php
/**
 * Supervisor Grading Page
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('supervisor');

$page_title = 'Grade Student Reports';

$user_id = getCurrentUserId();
$supervisor_id = $_SESSION['supervisor_id'];
$assignment_id = intval($_GET['assignment_id'] ?? 0);

if (!$assignment_id || !canAccessAssignment($assignment_id)) {
    setFlashMessage('Invalid assignment.', 'error');
    header('Location: dashboard.php');
    exit();
}

// Get assignment details
$assignment = fetchOne(
    "SELECT ta.*, s.student_number, u.full_name as student_name,
     c.company_name
     FROM training_assignments ta
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN companies c ON ta.company_id = c.company_id
     WHERE ta.assignment_id = ?",
    [$assignment_id]
);

// Get reports
$stage1_report = fetchOne(
    "SELECT sr.* FROM stage1_reports sr WHERE sr.assignment_id = ?",
    [$assignment_id]
);

$weekly_followups = fetchAll(
    "SELECT * FROM weekly_followups WHERE assignment_id = ? ORDER BY week_number ASC",
    [$assignment_id]
);

$final_report = fetchOne(
    "SELECT * FROM final_reports WHERE assignment_id = ?",
    [$assignment_id]
);

// Handle grading submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['grade_stage1']) && $stage1_report) {
        $intro_mark = floatval($_POST['introduction_mark'] ?? 0);
        $goals_mark = floatval($_POST['intended_goals_mark'] ?? 0);
        $details_mark = floatval($_POST['company_details_mark'] ?? 0);
        $total = $intro_mark + $goals_mark + $details_mark;
        $comments = sanitizeInput($_POST['comments'] ?? '');
        
        // Check if grade already exists
        $existing_grade = fetchOne(
            "SELECT * FROM stage1_grades WHERE report_id = ?",
            [$stage1_report['report_id']]
        );
        
        if ($existing_grade) {
            $sql = "UPDATE stage1_grades SET introduction_mark = ?, intended_goals_mark = ?, 
                    company_details_mark = ?, total_mark = ?, comments = ?, graded_at = NOW()
                    WHERE grade_id = ?";
            executeUpdate($sql, [$intro_mark, $goals_mark, $details_mark, $total, $comments, $existing_grade['grade_id']]);
        } else {
            $sql = "INSERT INTO stage1_grades 
                    (report_id, supervisor_id, introduction_mark, intended_goals_mark, 
                     company_details_mark, total_mark, comments)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            executeUpdate($sql, [$stage1_report['report_id'], $supervisor_id, $intro_mark, $goals_mark, $details_mark, $total, $comments]);
        }
        
        // Update report status
        executeUpdate("UPDATE stage1_reports SET status = 'graded' WHERE report_id = ?", [$stage1_report['report_id']]);
        
        setFlashMessage('Stage 1 Report graded successfully!', 'success');
        header('Location: grade.php?assignment_id=' . $assignment_id);
        exit();
    }
}

// Get existing grades
$stage1_grade = null;
if ($stage1_report) {
    $stage1_grade = fetchOne(
        "SELECT * FROM stage1_grades WHERE report_id = ?",
        [$stage1_report['report_id']]
    );
}

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Grade Reports - <?php echo htmlspecialchars($assignment['student_name']); ?></h2>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label>Student:</label>
                <p><?php echo htmlspecialchars($assignment['student_name']); ?> (<?php echo htmlspecialchars($assignment['student_number']); ?>)</p>
            </div>
            <div class="form-group">
                <label>Company:</label>
                <p><?php echo htmlspecialchars($assignment['company_name']); ?></p>
            </div>
        </div>

        <!-- Stage 1 Report Grading -->
        <?php if ($stage1_report): ?>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Stage 1 Report (Company Profile)</h3>
                </div>
                <div class="card-body">
                    <?php if ($stage1_report['status'] === 'submitted' || ($stage1_grade && $stage1_report['status'] === 'graded')): ?>
                        <form method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="introduction_mark">Introduction Mark (max 1):</label>
                                    <input type="number" id="introduction_mark" name="introduction_mark" 
                                           step="0.01" min="0" max="1" required
                                           value="<?php echo $stage1_grade['introduction_mark'] ?? '0'; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="intended_goals_mark">Intended Goals Mark (max 1):</label>
                                    <input type="number" id="intended_goals_mark" name="intended_goals_mark" 
                                           step="0.01" min="0" max="1" required
                                           value="<?php echo $stage1_grade['intended_goals_mark'] ?? '0'; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="company_details_mark">Company Details Mark (max 3):</label>
                                    <input type="number" id="company_details_mark" name="company_details_mark" 
                                           step="0.01" min="0" max="3" required
                                           value="<?php echo $stage1_grade['company_details_mark'] ?? '0'; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Total Mark:</label>
                                    <p><strong id="total_mark"><?php echo $stage1_grade ? number_format($stage1_grade['total_mark'], 2) : '0.00'; ?>/5</strong></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="comments">Comments:</label>
                                <textarea id="comments" name="comments" rows="3"><?php echo htmlspecialchars($stage1_grade['comments'] ?? ''); ?></textarea>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="grade_stage1" class="btn btn-primary">
                                    <?php echo $stage1_grade ? 'Update Grade' : 'Submit Grade'; ?>
                                </button>
                            </div>
                        </form>
                        
                        <script>
                        // Calculate total mark dynamically
                        ['introduction_mark', 'intended_goals_mark', 'company_details_mark'].forEach(id => {
                            document.getElementById(id).addEventListener('input', function() {
                                const intro = parseFloat(document.getElementById('introduction_mark').value) || 0;
                                const goals = parseFloat(document.getElementById('intended_goals_mark').value) || 0;
                                const details = parseFloat(document.getElementById('company_details_mark').value) || 0;
                                document.getElementById('total_mark').textContent = (intro + goals + details).toFixed(2) + '/5';
                            });
                        });
                        </script>
                    <?php else: ?>
                        <p>Report not yet submitted.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

