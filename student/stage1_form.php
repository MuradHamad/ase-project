<?php
/**
 * Stage 1 Report Form
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'Stage 1 Report - Company Profile';

$user_id = getCurrentUserId();
$student_id = $_SESSION['student_id'];

// Get student details
$student_info = fetchOne(
    "SELECT academic_year, semester FROM students WHERE student_id = ?",
    [$student_id]
);

// Get student's current training assignment
$sql = "SELECT ta.*, c.*, 
        cs.full_name AS company_supervisor_name, 
        cs.email AS company_supervisor_email, 
        cs.phone AS company_supervisor_phone,
        u.full_name AS academic_supervisor_name
        FROM training_assignments ta
        JOIN companies c ON ta.company_id = c.company_id
        LEFT JOIN company_supervisors cs 
            ON c.company_id = cs.company_id AND cs.is_primary = 1
        JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
        JOIN users u ON ac.supervisor_id = u.user_id
        WHERE ta.student_id = ?
        ORDER BY ta.created_at DESC
        LIMIT 1";

$assignment = fetchOne($sql, [$student_id]);

if (!$assignment) {
    setFlashMessage('You need to be assigned to a company first.', 'error');
    header('Location: dashboard.php');
    exit();
}

// Check if report already exists
$existing_report = fetchOne(
    "SELECT * FROM stage1_reports WHERE assignment_id = ?",
    [$assignment['assignment_id']]
);

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_stage1'])) {

    $introduction_text = sanitizeInput($_POST['introduction_text'] ?? '');
    $intended_goals = sanitizeInput($_POST['intended_goals'] ?? '');
    $company_details = sanitizeInput($_POST['company_details'] ?? '');

    if (empty($introduction_text) || empty($intended_goals) || empty($company_details)) {
        $error = 'Please fill in all required fields.';
    } else {

        if ($existing_report) {
            // Update existing report (WITHOUT signature)
            $sql = "UPDATE stage1_reports SET 
                    introduction_text = ?, 
                    intended_goals = ?, 
                    company_details = ?,
                    status = 'submitted',
                    submitted_at = NOW()
                    WHERE report_id = ?";

            $result = executeUpdate($sql, [
                $introduction_text,
                $intended_goals,
                $company_details,
                $existing_report['report_id']
            ]);

        } else {
            // Insert new report (WITHOUT signature)
            $sql = "INSERT INTO stage1_reports 
                    (assignment_id, introduction_text, intended_goals, company_details, status, submitted_at)
                    VALUES (?, ?, ?, ?, 'submitted', NOW())";

            $result = executeUpdate($sql, [
                $assignment['assignment_id'],
                $introduction_text,
                $intended_goals,
                $company_details
            ]);
        }

        if ($result !== false) {
            setFlashMessage('Stage 1 Report submitted successfully!', 'success');
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Error submitting report. Please try again.';
        }
    }
}

// If report exists, load values into POST
if ($existing_report && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_POST = $existing_report;
}

/* -------------------------------------------------------
   SUPERVISOR SIGNATURE DISPLAY LOGIC
--------------------------------------------------------*/

$signature_value = $existing_report['company_supervisor_signature'] ?? null;

if ($signature_value === null) {
    $signature_label = "Not reviewed";
} elseif ($signature_value == 0) {
    $signature_label = "Rejected";
} elseif ($signature_value == 1) {
    $signature_label = "Approved";
} else {
    $signature_label = "Unknown";
}

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Stage 1 Report (Company Profile)</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="" data-validate>
        <div class="card-body">

            <!-- Student Information -->
            <h3 style="margin-bottom:20px;">Student Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Student Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars(getCurrentUserName()); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Student ID:</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['student_number']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Academic Year/Semester:</label>
                    <input type="text" value="<?php echo htmlspecialchars(($student_info['academic_year'] ?? '') . ' / ' . ($student_info['semester'] ?? '')); ?>" disabled>
                </div>
            </div>

            <!-- Company Information -->
            <h3 style="margin-top:30px; margin-bottom:20px;">Company & Training Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Place of Training (Company Name):</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['company_name']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Training Starting Date:</label>
                    <input type="text" value="<?php echo formatDate($assignment['training_start_date']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Full Address:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['full_address']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Phone No.:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['phone']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Fax:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['fax'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company E-mail:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['email']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Website:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['website'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Supervisor Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['company_supervisor_name'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Supervisor E-mail:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['company_supervisor_email'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Supervisor Phone:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['company_supervisor_phone'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Academic Supervisor Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['academic_supervisor_name']); ?>" disabled>
                </div>
            </div>

            <!-- Report Sections -->
            <h3 style="margin-top:30px; margin-bottom:20px;">Report Sections</h3>

            <div class="form-group">
                <label>1. Introduction about the place of training: *</label>
                <textarea name="introduction_text" rows="8" required><?php echo htmlspecialchars($_POST['introduction_text'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>2. Intended goals of training: *</label>
                <textarea name="intended_goals" rows="6" required><?php echo htmlspecialchars($_POST['intended_goals'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>3. Describe the company department, roles & software: *</label>
                <textarea name="company_details" rows="10" required><?php echo htmlspecialchars($_POST['company_details'] ?? ''); ?></textarea>
            </div>

            <!-- Supervisor Signature (READ ONLY) -->
            <h3 style="margin-top:30px; margin-bottom:20px;">Company Supervisor Signature</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Status:</label>
                    <input type="text" value="<?php echo htmlspecialchars($signature_label); ?>" disabled>
                </div>
            </div>

        </div>

        <div class="card-footer">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="submit_stage1" class="btn btn-primary">
                <?php echo $existing_report ? 'Update Report' : 'Submit Report'; ?>
            </button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
