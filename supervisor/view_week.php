<?php
/**
 * View and Grade a specific weekly follow-up (Supervisor)
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('supervisor');

$followup_id = isset($_GET['followup_id']) ? intval($_GET['followup_id']) : 0;
if ($followup_id <= 0) {
    setFlashMessage('Invalid weekly follow-up specified', 'error');
    header('Location: dashboard.php');
    exit();
}

$supervisor_id = $_SESSION['supervisor_id'];

// Fetch weekly follow-up and related assignment info
$weekly = fetchOne(
    "SELECT wf.*, ta.assignment_id, ta.academic_supervisor_id AS supervisor_id,
            s.student_id, u.full_name AS student_name, s.student_number,
            c.company_name
     FROM weekly_followups wf
     JOIN training_assignments ta ON wf.assignment_id = ta.assignment_id
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     JOIN companies c ON ta.company_id = c.company_id
     WHERE wf.followup_id = ?",
    [$followup_id]
);

// Authorization check
if (!$weekly || $weekly['supervisor_id'] != $supervisor_id) {
    setFlashMessage('You are not authorized to view this weekly follow-up', 'error');
    header('Location: dashboard.php');
    exit();
}

// Fetch tasks for this follow-up
$tasks = fetchAll(
    "SELECT * FROM weekly_tasks WHERE followup_id = ? ORDER BY task_order ASC",
    [$followup_id]
);

$error = '';
$success = '';

// Handle grading submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_grade'])) {
    $grade = isset($_POST['supervisor_grade']) ? floatval($_POST['supervisor_grade']) : null;
    $comments = sanitizeInput($_POST['supervisor_comments'] ?? '');

    if ($grade === null) {
        $error = 'Please enter a valid grade.';
    } else {
        $update = executeUpdate(
            "UPDATE weekly_followups 
             SET supervisor_grade = ?, supervisor_comments = ?, academic_supervisor_signed = 1, academic_supervisor_signed_date = NOW()
             WHERE followup_id = ?",
            [$grade, $comments, $followup_id]
        );

        if ($update !== false) {
            setFlashMessage('Grade and comments submitted successfully!', 'success');
            header("Location: view_week.php?followup_id=$followup_id");
            exit();
        } else {
            $error = 'Failed to save grade. Please try again.';
        }
    }
}

include '../includes/header.php';
?>

<section class="container">
    <h2>Weekly Follow-Up - Week <?php echo htmlspecialchars($weekly['week_number']); ?></h2>

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
        <div class="card-header">
            <h3>Student Information</h3>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($weekly['student_name']); ?></p>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($weekly['student_number']); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($weekly['company_name']); ?></p>
            <p><strong>Week Start:</strong> <?php echo formatDate($weekly['week_start_date']); ?></p>
            <p><strong>Week End:</strong> <?php echo formatDate($weekly['week_end_date']); ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Tasks / Duties</h3>
        </div>
        <div class="card-body">
            <?php if ($tasks): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Task / Duty</th>
                            <th>Notes</th>
                            <th>Gained Skills / Knowledge</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['task_order']); ?></td>
                            <td><?php echo htmlspecialchars($task['tasks_duties']); ?></td>
                            <td><?php echo htmlspecialchars($task['notes']); ?></td>
                            <td><?php echo htmlspecialchars($task['gained_skills']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No tasks found for this week.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Supervisor Grading</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group mb-3">
                    <label for="supervisor_grade">Grade:</label>
                    <input type="number" step="0.01" min="0" max="10" name="supervisor_grade" id="supervisor_grade" class="form-control"
                           value="<?php echo htmlspecialchars($weekly['supervisor_grade'] ?? ''); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="supervisor_comments">Comments:</label>
                    <textarea name="supervisor_comments" id="supervisor_comments" class="form-control" rows="5"><?php echo htmlspecialchars($weekly['supervisor_comments'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="submit_grade" class="btn btn-primary">Submit Grade & Comments</button>
                <a href="view_student.php?assignment_id=<?php echo $weekly['assignment_id']; ?>" class="btn btn-secondary">Back to Student</a>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
