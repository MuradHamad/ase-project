<?php
/**
 * Company Supervisor - Evaluation Page (separate)
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('company_supervisor');

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$company_id = $_SESSION['company_id'] ?? null;

if ($assignment_id <= 0) {
    setFlashMessage('Invalid assignment selected', 'error');
    header('Location: dashboard.php');
    exit();
}

// Verify assignment belongs to this supervisor's company
$assignment = fetchOne("SELECT * FROM training_assignments WHERE assignment_id = ?", [$assignment_id]);
if (!$assignment || $assignment['company_id'] != $company_id) {
    setFlashMessage('You are not authorized to view this assignment', 'error');
    header('Location: dashboard.php');
    exit();
}

$existing = fetchOne("SELECT * FROM company_evaluations WHERE assignment_id = ? LIMIT 1", [$assignment_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $intended_goals = sanitizeInput($_POST['intended_goals'] ?? '');
    $assigned_tasks_summary = sanitizeInput($_POST['assigned_tasks_summary'] ?? '');
    $feedback_a = sanitizeInput($_POST['feedback_a'] ?? '');
    $feedback_b = sanitizeInput($_POST['feedback_b'] ?? '');
    $feedback_c = sanitizeInput($_POST['feedback_c'] ?? '');
    $additional_notes = sanitizeInput($_POST['additional_notes'] ?? '');
    $recommend_students = isset($_POST['recommend_students']) ? 1 : 0;
    $recommend_explanation = sanitizeInput($_POST['recommend_explanation'] ?? '');
    $company_cooperation = sanitizeInput($_POST['company_cooperation'] ?? '');
    $evaluation_date = $_POST['evaluation_date'] ?? date('Y-m-d');

    if ($existing) {
        $res = executeUpdate(
            "UPDATE company_evaluations SET intended_goals = ?, assigned_tasks_summary = ?, feedback_curriculum_a = ?, feedback_curriculum_b = ?, feedback_curriculum_c = ?, additional_notes = ?, recommend_students = ?, recommend_explanation = ?, company_cooperation = ?, company_supervisor_name = ?, company_supervisor_signature = ?, evaluation_date = ? WHERE assignment_id = ?",
            [$intended_goals, $assigned_tasks_summary, $feedback_a, $feedback_b, $feedback_c, $additional_notes, $recommend_students, $recommend_explanation, $company_cooperation, $_SESSION['full_name'] ?? '', 'signed', $evaluation_date, $assignment_id]
        );
    } else {
        $res = executeUpdate(
            "INSERT INTO company_evaluations (assignment_id, intended_goals, assigned_tasks_summary, feedback_curriculum_a, feedback_curriculum_b, feedback_curriculum_c, additional_notes, recommend_students, recommend_explanation, company_cooperation, company_supervisor_name, company_supervisor_signature, evaluation_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$assignment_id, $intended_goals, $assigned_tasks_summary, $feedback_a, $feedback_b, $feedback_c, $additional_notes, $recommend_students, $recommend_explanation, $company_cooperation, $_SESSION['full_name'] ?? '', 'signed', $evaluation_date]
        );
    }

    if ($res !== false) {
        setFlashMessage('Company evaluation saved', 'success');
        header('Location: view_submission.php?assignment_id=' . $assignment_id . '#evaluation');
        exit();
    } else {
        setFlashMessage('Failed to save evaluation', 'error');
    }
}

include '../includes/header.php';
?>

<div class="container">
    <h2>Company Evaluation for Assignment #<?php echo $assignment_id; ?></h2>

    <?php if ($existing): ?>
        <p><strong>Existing evaluation submitted on <?php echo formatDate($existing['submitted_at'] ?? $existing['evaluation_date']); ?></strong></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Intended goals of training</label>
            <textarea name="intended_goals" class="form-control" rows="4" required><?php echo htmlspecialchars($existing['intended_goals'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Summary of assigned tasks</label>
            <textarea name="assigned_tasks_summary" class="form-control" rows="4" required><?php echo htmlspecialchars($existing['assigned_tasks_summary'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Feedback A (The appropriateness of student's theoretical knowledge)</label>
            <textarea name="feedback_a" class="form-control" rows="2"><?php echo htmlspecialchars($existing['feedback_curriculum_a'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Feedback B (Convenience of training time)</label>
            <textarea name="feedback_b" class="form-control" rows="2"><?php echo htmlspecialchars($existing['feedback_curriculum_b'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Feedback C (Convenience of training mechanism)</label>
            <textarea name="feedback_c" class="form-control" rows="2"><?php echo htmlspecialchars($existing['feedback_curriculum_c'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Additional notes and suggestions</label>
            <textarea name="additional_notes" class="form-control" rows="4"><?php echo htmlspecialchars($existing['additional_notes'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Recommend student for employment?</label>
            <input type="checkbox" name="recommend_students" value="1" <?php echo (!empty($existing['recommend_students']) ? 'checked' : ''); ?>> Yes
            <br>
            <label>If yes/explain</label>
            <textarea name="recommend_explanation" class="form-control" rows="2"><?php echo htmlspecialchars($existing['recommend_explanation'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Company cooperation</label>
            <input type="text" name="company_cooperation" class="form-control" value="<?php echo htmlspecialchars($existing['company_cooperation'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Evaluation date</label>
            <input type="date" name="evaluation_date" class="form-control" value="<?php echo htmlspecialchars($existing['evaluation_date'] ?? date('Y-m-d')); ?>">
        </div>
        <div style="margin-top:12px;">
            <button type="submit" class="btn btn-primary"><?php echo $existing ? 'Update Evaluation' : 'Submit Evaluation'; ?></button>
            <a href="view_submission.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-secondary">Back to Submission</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
