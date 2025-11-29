<?php
/**
 * View and grade the Final Report (Supervisor)
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

// Fetch final report
$final = fetchOne("SELECT * FROM final_reports WHERE assignment_id = ?", [$assignment_id]);

if (!$final) {
    setFlashMessage('Final report has not been submitted yet.', 'error');
    header("Location: view_student.php?assignment_id=$assignment_id");
    exit();
}

$error = '';

// Handle grading submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_grade'])) {
    $grade = isset($_POST['supervisor_grade']) ? floatval($_POST['supervisor_grade']) : null;
    $comments = sanitizeInput($_POST['supervisor_comments'] ?? '');

    if ($grade === null) {
        $error = 'Please enter a valid grade.';
    } else {
        $update = executeUpdate(
            "UPDATE final_reports 
             SET supervisor_grade = ?, supervisor_comments = ?, status = 'graded'
             WHERE report_id = ?",
            [$grade, $comments, $final['report_id']]
        );

        if ($update !== false) {
            setFlashMessage('Grade and comments submitted successfully!', 'success');
            header("Location: view_final.php?assignment_id=$assignment_id");
            exit();
        } else {
            $error = 'Failed to save grade. Please try again.';
        }
    }
}

include '../includes/header.php';
?>

<section class="container">
    <h2>Final Report - <?php echo htmlspecialchars($assignment['student_name']); ?></h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?>">
            <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header"><h3>Report Content</h3></div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label>Training Type:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($final['training_type']); ?>" disabled>
            </div>
            <div class="form-group mb-3">
                <label>Duration:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($final['duration']); ?>" disabled>
            </div>
            <div class="form-group mb-3">
                <label>Acknowledgment:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['acknowledgment']); ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label>Section 1 - Objectives:</label>
                <textarea class="form-control" rows="4" disabled><?php echo htmlspecialchars($final['Section_1']); ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label>Section 2.1 - Importance:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['Section_2_1']); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Section 2.2 - Nature of Training:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['nature_of_training']); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Section 2.3 - Nature of Supervision:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['nature_of_supervision']); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Section 2.4 - Technical Training Experience:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['training_experience_technical']); ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label>Section 3 - Skill 1 (Personal):</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['training_experience_personal']); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Section 3 - Skill 2 (Communication):</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['training_experience_communication']); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Section 3 - Skill 3 (Company Societal Impact):</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['company_societal_impact']); ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label>Section 4 - Relevance to Major:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['relevance_to_major']); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Section 5 - Theoretical Appropriateness:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['theoretical_appropriateness']); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Section 6 - Suggestions:</label>
                <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($final['suggestions']); ?></textarea>
            </div>

            <p><strong>Submitted at:</strong> <?php echo formatDate($final['submitted_at']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($final['status']); ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3>Supervisor Grading</h3></div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group mb-3">
                    <label for="supervisor_grade">Grade:</label>
                    <input type="number" step="0.01" min="0" max="100" name="supervisor_grade" id="supervisor_grade" class="form-control"
                           value="<?php echo htmlspecialchars($final['supervisor_grade'] ?? ''); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="supervisor_comments">Comments:</label>
                    <textarea name="supervisor_comments" id="supervisor_comments" class="form-control" rows="5"><?php echo htmlspecialchars($final['supervisor_comments'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="submit_grade" class="btn btn-primary">Submit Grade & Comments</button>
                <a href="view_student.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-secondary">Back to Student</a>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
