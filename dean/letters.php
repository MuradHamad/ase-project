<?php
/**
 * Issue Training Request Letters (Dean)
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('dean');

$page_title = 'Training Request Letters';

$user_id = getCurrentUserId();

// Handle form submit to create a new letter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = isset($_POST['assignment_id']) ? intval($_POST['assignment_id']) : 0;
    $letter_content = isset($_POST['letter_content']) ? sanitizeInput($_POST['letter_content']) : '';
    $issued_date = isset($_POST['issued_date']) ? $_POST['issued_date'] : date('Y-m-d');

    if ($assignment_id > 0 && !empty($letter_content)) {
        $sql = "INSERT INTO training_request_letters (assignment_id, issued_by, letter_content, issued_date) VALUES (?, ?, ?, ?)";
        $result = executeUpdate($sql, [$assignment_id, $user_id, $letter_content, $issued_date]);

        if ($result !== false) {
            setFlashMessage('Letter issued successfully', 'success');
            header('Location: letters.php');
            exit();
        } else {
            setFlashMessage('Failed to issue letter', 'error');
        }
    } else {
        setFlashMessage('Please choose an assignment and enter letter content', 'warning');
    }
}

// Fetch existing letters
$letters_sql = "SELECT l.*, u.full_name AS student_name, s.student_number, issued_by_user.full_name AS issued_by_name
                FROM training_request_letters l
                JOIN training_assignments ta ON l.assignment_id = ta.assignment_id
                JOIN users u ON ta.student_id = u.user_id
                JOIN students s ON ta.student_id = s.student_id
                LEFT JOIN users issued_by_user ON l.issued_by = issued_by_user.user_id
                ORDER BY l.issued_date DESC";

$letters = fetchAll($letters_sql);

// Fetch assignments for select list
$assign_sql = "SELECT ta.assignment_id, u.full_name AS student_name, s.student_number, c.company_name
               FROM training_assignments ta
               JOIN users u ON ta.student_id = u.user_id
               JOIN students s ON ta.student_id = s.student_id
               JOIN companies c ON ta.company_id = c.company_id
               WHERE ta.status IN ('assigned','in_progress','completed')
               AND NOT EXISTS (SELECT 1 FROM training_request_letters WHERE assignment_id = ta.assignment_id)
               ORDER BY ta.training_start_date DESC";

$assignments = fetchAll($assign_sql);

// Check if we're pre-selecting an assignment
$selected_assignment = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;

include '../includes/header.php';

// If a specific letter is requested, show read-only view
if (isset($_GET['letter_id']) && intval($_GET['letter_id']) > 0) {
    $letter_id = intval($_GET['letter_id']);
    $letter_sql = "SELECT l.*, u.full_name AS student_name, s.student_number, issued_by_user.full_name AS issued_by_name, ta.assignment_id
                   FROM training_request_letters l
                   JOIN training_assignments ta ON l.assignment_id = ta.assignment_id
                   JOIN users u ON ta.student_id = u.user_id
                   JOIN students s ON ta.student_id = s.student_id
                   LEFT JOIN users issued_by_user ON l.issued_by = issued_by_user.user_id
                   WHERE l.letter_id = ? LIMIT 1";
    $letter = fetchOne($letter_sql, [$letter_id]);

    ?>
    <div class="container">
        <?php if ($letter === false): ?>
            <div class="card">
                <div class="card-body">
                    <p>Letter not found.</p>
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h3>View Letter #<?php echo htmlspecialchars($letter['letter_id']); ?></h3>
                </div>
                <div class="card-body">
                    <p><strong>Student:</strong> <?php echo htmlspecialchars($letter['student_name'] ?? '—'); ?> (<?php echo htmlspecialchars($letter['student_number'] ?? '—'); ?>)</p>
                    <p><strong>Issued By:</strong> <?php echo htmlspecialchars($letter['issued_by_name'] ?? '—'); ?></p>
                    <p><strong>Issue Date:</strong> <?php echo formatDate($letter['issued_date']); ?></p>
                    <hr>
                    <div style="white-space: pre-wrap; border:1px solid #eaeaea; padding:12px; background:#fff;">
                        <?php echo nl2br(htmlspecialchars($letter['letter_content'])); ?>
                    </div>
                    <div style="margin-top:15px;">
                        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php';
    exit();
}
?>

<div class="container">
    <h2>Training Request Letters</h2>

    <div class="card">
        <div class="card-header">
            <h3>Issue New Letter</h3>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label for="assignment_id">Select Assignment <span style="color: red;">*</span></label>
                    <select name="assignment_id" id="assignment_id" class="form-control" required>
                        <option value="">-- Select Assignment --</option>
                        <?php if (!empty($assignments)): ?>
                            <?php foreach ($assignments as $a): ?>
                                <option value="<?php echo $a['assignment_id']; ?>" <?php echo $selected_assignment == $a['assignment_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($a['student_name'] . ' (' . $a['student_number'] . ') - ' . $a['company_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No pending assignments</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="issued_date">Issue Date <span style="color: red;">*</span></label>
                    <input type="date" name="issued_date" id="issued_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="letter_content">Letter Content <span style="color: red;">*</span></label>
                    <textarea name="letter_content" id="letter_content" class="form-control" rows="12" required placeholder="Enter the letter content here..."></textarea>
                </div>
                <div style="margin-top: 15px;">
                    <button type="submit" class="btn btn-primary">Issue Letter</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Previously issued letters list removed as requested -->
</div>

<script>
function showLetter(id, content) {
    alert('Letter #' + id + ':\n\n' + content);
}
</script>

<?php include '../includes/footer.php'; ?>
