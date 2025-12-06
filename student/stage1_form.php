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
$sql = "SELECT ta.*, c.*, cs.full_name as company_supervisor_name, cs.email as company_supervisor_email, cs.phone as company_supervisor_phone,
        u.full_name as academic_supervisor_name
        FROM training_assignments ta
        JOIN companies c ON ta.company_id = c.company_id
        LEFT JOIN company_supervisors cs ON c.company_id = cs.company_id AND cs.is_primary = 1
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
        $error = 'Please fill in all required fields';
    } else {
        if ($existing_report) {
            // Update existing report
            $sql = "UPDATE stage1_reports 
                    SET introduction_text = ?, intended_goals = ?, company_details = ?,
                        company_supervisor_signature = ?, company_supervisor_signed_date = ?,
                        status = 'submitted', submitted_at = NOW()
                    WHERE report_id = ?";
            $result = executeUpdate($sql, [
                $introduction_text,
                $intended_goals,
                $company_details,
                sanitizeInput($_POST['company_supervisor_signature'] ?? ''),
                !empty($_POST['company_supervisor_signed_date']) ? $_POST['company_supervisor_signed_date'] : null,
                $existing_report['report_id']
            ]);
        } else {
            // Insert new report
            $sql = "INSERT INTO stage1_reports 
                    (assignment_id, introduction_text, intended_goals, company_details,
                     company_supervisor_signature, company_supervisor_signed_date, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'submitted')";
            $result = executeUpdate($sql, [
                $assignment['assignment_id'],
                $introduction_text,
                $intended_goals,
                $company_details,
                sanitizeInput($_POST['company_supervisor_signature'] ?? ''),
                !empty($_POST['company_supervisor_signed_date']) ? $_POST['company_supervisor_signed_date'] : null
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

// If report exists, use its data
if ($existing_report && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_POST = $existing_report;
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
            <h3 style="margin-bottom: 20px;">Student Information</h3>
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
            <h3 style="margin-top: 30px; margin-bottom: 20px;">Company and Training Information</h3>
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
                    <label>Company Supervisor's Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['company_supervisor_name'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Supervisor's E-mail:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['company_supervisor_email'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Company Supervisor's Phone No:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['company_supervisor_phone'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Academic Supervisor's Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars($assignment['academic_supervisor_name']); ?>" disabled>
                </div>
            </div>

            <!-- Report Sections -->
            <h3 style="margin-top: 30px; margin-bottom: 20px;">Report Sections</h3>
            
            <div class="form-group">
                <label for="introduction_text">
                    1. Introduction about the place of training: <span style="color: red;">*</span> (1 point)
                </label>
                <div class="help-text">
                    (When the company is established, Vision and mission of the company, The areas in which company specialized, Where the company is located (list branches if any), etc....)
                </div>
                <textarea id="introduction_text" name="introduction_text" required 
                          rows="8"><?php echo htmlspecialchars($_POST['introduction_text'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="intended_goals">
                    2. Intended goal of training: <span style="color: red;">*</span> (1 Point)
                </label>
                <textarea id="intended_goals" name="intended_goals" required 
                          rows="6"><?php echo htmlspecialchars($_POST['intended_goals'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="company_details">
                    3. Describe the company department, roles and software: <span style="color: red;">*</span> (3 Points)
                </label>
                <textarea id="company_details" name="company_details" required 
                          rows="10"><?php echo htmlspecialchars($_POST['company_details'] ?? ''); ?></textarea>
            </div>

            <!-- Company Supervisor Signature -->
            <h3 style="margin-top: 30px; margin-bottom: 20px;">Company Supervisor Signature</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="company_supervisor_signature">Company Supervisor's Name and Signature:</label>
                    <input type="text" id="company_supervisor_signature" name="company_supervisor_signature"
                           value="<?php echo htmlspecialchars($_POST['company_supervisor_signature'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="company_supervisor_signed_date">Date:</label>
                    <input type="date" id="company_supervisor_signed_date" name="company_supervisor_signed_date"
                           value="<?php echo formatDateForInput($_POST['company_supervisor_signed_date'] ?? ''); ?>">
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

