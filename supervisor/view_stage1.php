<?php
/**
 * View Stage 1 Report (Supervisor)
 */

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

// Fetch assignment and validate supervisor
$assignment = fetchOne(
    "SELECT ta.*, s.student_id, u.full_name AS student_name, s.student_number,
            c.company_name, ac.supervisor_id
     FROM training_assignments ta
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN companies c ON ta.company_id = c.company_id
     JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
     WHERE ta.assignment_id = ?",
    [$assignment_id]
);

if (!$assignment || $assignment['supervisor_id'] != $supervisor_id) {
    setFlashMessage('You are not authorized to view this assignment', 'error');
    header('Location: dashboard.php');
    exit();
}

// Fetch Stage 1 report
$report = fetchOne("SELECT * FROM stage1_reports WHERE assignment_id = ?", [$assignment_id]);

if (!$report) {
    setFlashMessage('Stage 1 report has not been submitted yet.', 'error');
    header('Location: view_student.php?assignment_id=' . $assignment_id);
    exit();
}

// Determine company supervisor signature status
$company_signed_text = 'Pending Review';
if (isset($report['company_supervisor_signature'])) {
    if ($report['company_supervisor_signature'] == 1) {
        $company_signed_text = 'Approved';
    } elseif ($report['company_supervisor_signature'] == 0) {
        $company_signed_text = 'Not Signed';
    }
}

include '../includes/header.php';
?>

<section class="container">
    <div class="card">
        <div class="card-header">
            <h2>Stage 1 Report (Company Profile)</h2>
            <p><strong>Student:</strong> <?php echo htmlspecialchars($assignment['student_name']); ?> | 
               <strong>Student ID:</strong> <?php echo htmlspecialchars($assignment['student_number']); ?></p>
        </div>
        <div class="card-body">
            <!-- Report Sections -->
            <h4>1. Introduction about the place of training</h4>
            <p><?php echo nl2br(htmlspecialchars($report['introduction_text'])); ?></p>

            <h4>2. Intended goal of training</h4>
            <p><?php echo nl2br(htmlspecialchars($report['intended_goals'])); ?></p>

            <h4>3. Company department, roles and software</h4>
            <p><?php echo nl2br(htmlspecialchars($report['company_details'])); ?></p>

            <!-- Company Supervisor Signature -->
            <h4>Company Supervisor Signature</h4>
            <div class="form-row">
                <div class="form-group">
                    <label>Status:</label>
                    <input type="text" value="<?php echo htmlspecialchars($company_signed_text); ?>" disabled>
                </div>
                <?php if (!empty($report['company_supervisor_signed_date'])): ?>
                    <div class="form-group">
                        <label>Signed On:</label>
                        <input type="text" value="<?php echo formatDate($report['company_supervisor_signed_date']); ?>" disabled>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Grading Form -->
            <h4>Supervisor Grading</h4>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="grade">Grade (0-5):</label>
                    <input type="number" name="grade" id="grade" min="0" max="5" value="<?php echo htmlspecialchars($report['supervisor_grade'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="comments">Comments:</label>
                    <textarea name="comments" id="comments" rows="4"><?php echo htmlspecialchars($report['supervisor_comments'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="grade_stage1" class="btn btn-primary mt-2">Submit Grade</button>
                <a href="view_student.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-secondary mt-2">Back</a>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
