<?php
/**
 * Weekly Follow-up Form
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'Weekly Follow-up';

$user_id = getCurrentUserId();
$student_id = $_SESSION['student_id'];

// Get student's current training assignment
$assignment = fetchOne(
    "SELECT ta.*, c.*, cs.full_name as company_supervisor_name, cs.email as company_supervisor_email, cs.phone as company_supervisor_phone,
     u.full_name as academic_supervisor_name
     FROM training_assignments ta
     JOIN companies c ON ta.company_id = c.company_id
     LEFT JOIN company_supervisors cs ON c.company_id = cs.company_id AND cs.is_primary = 1
     JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
     JOIN users u ON ac.supervisor_id = u.user_id
     WHERE ta.student_id = ? 
     ORDER BY ta.created_at DESC 
     LIMIT 1",
    [$student_id]
);

if (!$assignment) {
    setFlashMessage('You need to be assigned to a company first.', 'error');
    header('Location: dashboard.php');
    exit();
}

// Get existing weekly follow-ups to calculate week number
$existing_followups = fetchAll(
    "SELECT week_number FROM weekly_followups WHERE assignment_id = ? ORDER BY week_number DESC LIMIT 1",
    [$assignment['assignment_id']]
);

$next_week = !empty($existing_followups) ? ($existing_followups[0]['week_number'] + 1) : 1;
$max_weeks = WEEKS_IN_TRAINING;

if ($next_week > $max_weeks) {
    setFlashMessage('You have already submitted all weekly follow-ups.', 'info');
    header('Location: weekly_list.php');
    exit();
}

// Calculate week dates
$start_date = new DateTime($assignment['training_start_date']);
$start_date->modify('+' . (($next_week - 1) * 7) . ' days');
$end_date = clone $start_date;
$end_date->modify('+6 days');

$error = '';
$tasks = [['tasks_duties' => '', 'notes' => '', 'gained_skills' => '']];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_weekly'])) {
    $week_start = sanitizeInput($_POST['week_start_date']);
    $week_end = sanitizeInput($_POST['week_end_date']);
    
    // Get tasks from form
    $tasks_data = [];
    if (isset($_POST['tasks']) && is_array($_POST['tasks'])) {
        foreach ($_POST['tasks'] as $task) {
            if (!empty(trim($task['tasks_duties']))) {
                $tasks_data[] = [
                    'tasks_duties' => sanitizeInput($task['tasks_duties']),
                    'notes' => sanitizeInput($task['notes'] ?? ''),
                    'gained_skills' => sanitizeInput($task['gained_skills'] ?? '')
                ];
            }
        }
    }
    
    if (empty($tasks_data)) {
        $error = 'Please add at least one task';
    } else {
        // Insert weekly follow-up
        $sql = "INSERT INTO weekly_followups 
                (assignment_id, week_start_date, week_end_date, week_number,
                 company_supervisor_signed, company_supervisor_signed_date,
                 academic_supervisor_signed, academic_supervisor_signed_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $followup_id = false;
        $conn = getDBConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ississss',
            $assignment['assignment_id'],
            $week_start,
            $week_end,
            $next_week,
            isset($_POST['company_supervisor_signed']) ? 1 : 0,
            !empty($_POST['company_supervisor_signed_date']) ? $_POST['company_supervisor_signed_date'] : null,
            isset($_POST['academic_supervisor_signed']) ? 1 : 0,
            !empty($_POST['academic_supervisor_signed_date']) ? $_POST['academic_supervisor_signed_date'] : null
        );
        
        if ($stmt->execute()) {
            $followup_id = $conn->insert_id;
            
            // Insert tasks
            $task_sql = "INSERT INTO weekly_tasks (followup_id, tasks_duties, notes, gained_skills, task_order) 
                        VALUES (?, ?, ?, ?, ?)";
            $task_stmt = $conn->prepare($task_sql);
            
            foreach ($tasks_data as $index => $task) {
                $order = $index + 1;
                $task_stmt->bind_param('isssi',
                    $followup_id,
                    $task['tasks_duties'],
                    $task['notes'],
                    $task['gained_skills'],
                    $order
                );
                $task_stmt->execute();
            }
            $task_stmt->close();
            
            setFlashMessage('Weekly follow-up submitted successfully!', 'success');
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Error submitting weekly follow-up. Please try again.';
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Weekly Duties (Weekly Follow-up) - Week <?php echo $next_week; ?></h2>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" data-validate>
        <div class="card-body">
            <!-- Student and Company Information -->
            <h3>Student Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Student Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars(getCurrentUserName()); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Student ID:</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['student_number']); ?>" disabled>
                </div>
            </div>

            <!-- Week Information -->
            <div class="form-row">
                <div class="form-group">
                    <label for="week_start_date">Week From: <span style="color: red;">*</span></label>
                    <input type="date" id="week_start_date" name="week_start_date" required
                           value="<?php echo $start_date->format('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="week_end_date">Week To: <span style="color: red;">*</span></label>
                    <input type="date" id="week_end_date" name="week_end_date" required
                           value="<?php echo $end_date->format('Y-m-d'); ?>">
                </div>
            </div>

            <!-- Tasks Table -->
            <h3 style="margin-top: 30px;">Tasks and Duties</h3>
            <div class="help-text mb-2">
                The evaluation is considered as weekly evaluation, therefore the week that the student does not follow with the academic supervisor, two marks will be deducted from the weekly follow up total marks.
            </div>
            
            <div class="table-container">
                <table id="tasks-table">
                    <thead>
                        <tr>
                            <th>Tasks and Duties assigned to trainee</th>
                            <th>Notes</th>
                            <th>Gained skills or knowledge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $index => $task): ?>
                            <tr>
                                <td>
                                    <textarea name="tasks[<?php echo $index; ?>][tasks_duties]" 
                                              rows="3" required><?php echo htmlspecialchars($task['tasks_duties']); ?></textarea>
                                </td>
                                <td>
                                    <textarea name="tasks[<?php echo $index; ?>][notes]" 
                                              rows="3"><?php echo htmlspecialchars($task['notes']); ?></textarea>
                                </td>
                                <td>
                                    <textarea name="tasks[<?php echo $index; ?>][gained_skills]" 
                                              rows="3"><?php echo htmlspecialchars($task['gained_skills']); ?></textarea>
                                </td>
                                <td>
                                    <?php if ($index > 0): ?>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="window.removeTableRow(this)">Remove</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <button type="button" class="btn btn-sm btn-secondary mt-2" 
                    onclick="addTaskRow()">Add Another Task</button>
        </div>
        
        <div class="card-footer">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="submit_weekly" class="btn btn-primary">Submit Weekly Follow-up</button>
        </div>
    </form>
</div>

<script>
function addTaskRow() {
    const table = document.querySelector('#tasks-table tbody');
    const rowCount = table.rows.length;
    const newRow = table.insertRow();
    newRow.innerHTML = `
        <td><textarea name="tasks[${rowCount}][tasks_duties]" rows="3" required></textarea></td>
        <td><textarea name="tasks[${rowCount}][notes]" rows="3"></textarea></td>
        <td><textarea name="tasks[${rowCount}][gained_skills]" rows="3"></textarea></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="window.removeTableRow(this)">Remove</button></td>
    `;
}
</script>

<?php include '../includes/footer.php'; ?>

