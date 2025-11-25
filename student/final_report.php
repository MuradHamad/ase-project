<?php
/**
 * Final Report submission/view for student
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'Final Report';

// Include final report specific CSS
$additional_css = ($additional_css ?? []);
$additional_css[] = 'final-report.css';

$student_id = $_SESSION['student_id'];

// Get current/latest assignment
$current_assignment = fetchOne(
    "SELECT ta.* FROM training_assignments ta WHERE ta.student_id = ? ORDER BY ta.created_at DESC LIMIT 1",
    [$student_id]
);

if (!$current_assignment) {
    setFlashMessage('No training assignment found.', 'error');
    header('Location: dashboard.php');
    exit();
}

$assignment_id = $current_assignment['assignment_id'];

$existing = fetchOne("SELECT * FROM final_reports WHERE assignment_id = ?", [$assignment_id]);

// Fetch student and assignment details for display
$student_info = fetchOne(
    "SELECT u.full_name, s.student_number, s.academic_year, s.semester
     FROM students s
     JOIN users u ON s.student_id = u.user_id
     WHERE s.student_id = ?",
    [$student_id]
);

$assignment_details = fetchOne(
    "SELECT ta.*, c.company_name, u.full_name as supervisor_name
     FROM training_assignments ta
     JOIN companies c ON ta.company_id = c.company_id
     JOIN academic_supervisors ac ON ta.academic_supervisor_id = ac.supervisor_id
     JOIN users u ON ac.supervisor_id = u.user_id
     WHERE ta.assignment_id = ?",
    [$assignment_id]
);

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $data['training_type'] = sanitizeInput($_POST['training_type'] ?? 'inside_company');
    $data['duration'] = sanitizeInput($_POST['duration'] ?? '');
    $data['acknowledgment'] = sanitizeInput($_POST['acknowledgment'] ?? '');
    $data['objectives'] = sanitizeInput($_POST['objectives'] ?? '');
    $data['importance'] = sanitizeInput($_POST['importance'] ?? '');
    $data['nature_of_training'] = sanitizeInput($_POST['nature_of_training'] ?? '');
    $data['nature_of_supervision'] = sanitizeInput($_POST['nature_of_supervision'] ?? '');
    $data['training_experience_technical'] = sanitizeInput($_POST['training_experience_technical'] ?? '');
    $data['training_experience_personal'] = sanitizeInput($_POST['training_experience_personal'] ?? '');
    $data['training_experience_communication'] = sanitizeInput($_POST['training_experience_communication'] ?? '');
    $data['company_societal_impact'] = sanitizeInput($_POST['company_societal_impact'] ?? '');
    $data['relevance_to_major'] = sanitizeInput($_POST['relevance_to_major'] ?? '');
    $data['theoretical_appropriateness'] = sanitizeInput($_POST['theoretical_appropriateness'] ?? '');
    $data['suggestions'] = sanitizeInput($_POST['suggestions'] ?? '');
    // Extra fields from the PDF-like form that are not separate columns in final_reports
    $recommend_students = isset($_POST['recommend_students']) ? 1 : 0;
    $recommend_explanation = sanitizeInput($_POST['recommend_explanation'] ?? '');
    $company_cooperation = sanitizeInput($_POST['company_cooperation'] ?? '');
    $status = isset($_POST['submit_final']) ? 'submitted' : 'draft';

    // Append extra notes to suggestions so we don't change DB schema
    $extras = "";
    if ($recommend_students) {
        $extras .= "\n\nRecommendation: Student recommends this company to others.\n" . $recommend_explanation;
    } elseif ($recommend_explanation) {
        $extras .= "\n\nRecommendation explanation: " . $recommend_explanation;
    }
    if ($company_cooperation) {
        $extras .= "\n\nCompany cooperation: " . $company_cooperation;
    }
    if (!empty($extras)) {
        $data['suggestions'] = trim($data['suggestions'] . "\n" . $extras);
    }

    if ($existing) {
        $sql = "UPDATE final_reports SET training_type = ?, duration = ?, acknowledgment = ?, objectives = ?, importance = ?,
                nature_of_training = ?, nature_of_supervision = ?, training_experience_technical = ?, training_experience_personal = ?,
                training_experience_communication = ?, company_societal_impact = ?, relevance_to_major = ?, theoretical_appropriateness = ?,
                suggestions = ?, status = ? WHERE report_id = ?";
        $params = [
            $data['training_type'], $data['duration'], $data['acknowledgment'], $data['objectives'], $data['importance'],
            $data['nature_of_training'], $data['nature_of_supervision'], $data['training_experience_technical'], $data['training_experience_personal'],
            $data['training_experience_communication'], $data['company_societal_impact'], $data['relevance_to_major'], $data['theoretical_appropriateness'],
            $data['suggestions'], $status, $existing['report_id']
        ];
        executeUpdate($sql, $params);
        setFlashMessage('Final report updated successfully.', 'success');
    } else {
        $sql = "INSERT INTO final_reports (assignment_id, training_type, duration, acknowledgment, objectives, importance,
                nature_of_training, nature_of_supervision, training_experience_technical, training_experience_personal,
                training_experience_communication, company_societal_impact, relevance_to_major, theoretical_appropriateness,
                suggestions, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $assignment_id, $data['training_type'], $data['duration'], $data['acknowledgment'], $data['objectives'], $data['importance'],
            $data['nature_of_training'], $data['nature_of_supervision'], $data['training_experience_technical'], $data['training_experience_personal'],
            $data['training_experience_communication'], $data['company_societal_impact'], $data['relevance_to_major'], $data['theoretical_appropriateness'],
            $data['suggestions'], $status
        ];
        executeUpdate($sql, $params);
        setFlashMessage('Final report submitted successfully.', 'success');
    }

    header('Location: final_report.php');
    exit();
}

include '../includes/header.php';
?>

<div class="card final-report">
    <div class="card-header">
        <h2>Final Report</h2>
        <p>Assignment #: <?php echo htmlspecialchars($assignment_id); ?></p>
    </div>
    <div class="card-body">
        <form method="POST">

            <!-- Header info -->
            <div class="form-row">
                <div class="form-group">
                    <label>Student Name</label>
                    <p><?php echo htmlspecialchars($student_info['full_name'] ?? ''); ?></p>
                </div>
                <div class="form-group">
                    <label>Student ID</label>
                    <p><?php echo htmlspecialchars($student_info['student_number'] ?? ''); ?></p>
                </div>
                <div class="form-group">
                    <label>Academic Year / Semester</label>
                    <p><?php echo htmlspecialchars(($student_info['academic_year'] ?? '') . ' / ' . ($student_info['semester'] ?? '')); ?></p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Company Name</label>
                    <p><?php echo htmlspecialchars($assignment_details['company_name'] ?? ''); ?></p>
                </div>
                <div class="form-group">
                    <label>Academic Supervisor</label>
                    <p><?php echo htmlspecialchars($assignment_details['supervisor_name'] ?? ''); ?></p>
                </div>
            </div>

            <!-- Sections marks table (display only) -->
            <div class="form-group">
                <label>Section Marks (for reference)</label>
                <table>
                    <thead>
                        <tr>
                            <th>Section No.</th>
                            <th>Program ILO</th>
                            <th>Section mark</th>
                            <th>Student Mark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>1</td><td>T.3</td><td>2</td><td>--</td></tr>
                        <tr><td>2</td><td>T.3</td><td>4</td><td>--</td></tr>
                        <tr><td>3</td><td>T.3</td><td>3</td><td>--</td></tr>
                        <tr><td>4</td><td>T.3</td><td>5</td><td>--</td></tr>
                        <tr><td>5</td><td>T.3</td><td>2</td><td>--</td></tr>
                        <tr><td>6</td><td>T.3</td><td>2</td><td>--</td></tr>
                        <tr><td>7</td><td>T.3</td><td>2</td><td>--</td></tr>
                        <tr><td colspan="3"><strong>Total mark 20</strong></td><td>--</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Training type table -->
            <div class="form-group">
                <label>Training Type</label>
                <div class="training-type-table">
                    <label>Type of training</label>
                    <select name="training_type">
                        <option value="inside_company" <?php echo ($existing['training_type'] ?? '') === 'inside_company' ? 'selected' : ''; ?>>Inside company</option>
                        <option value="training_courses" <?php echo ($existing['training_type'] ?? '') === 'training_courses' ? 'selected' : ''; ?>>Training courses</option>
                        <option value="project" <?php echo ($existing['training_type'] ?? '') === 'project' ? 'selected' : ''; ?>>Project</option>
                    </select>
                    <label>Duration</label>
                    <input type="text" name="duration" value="<?php echo htmlspecialchars($existing['duration'] ?? ''); ?>" />
                    <label>Place of training</label>
                    <input type="text" name="place_of_training" value="" />
                </div>
            </div>

            <!-- 1 - Acknowledgement -->
            <div class="form-group">
                <h3>1 - Acknowledgement</h3>
                <p>(Thanks to the company which gave you an opportunity to do the training...)</p>
                <textarea name="acknowledgment" rows="6"><?php echo htmlspecialchars($existing['acknowledgment'] ?? ''); ?></textarea>
            </div>

            <!-- 2 - Introduction -->
            <div class="form-group">
                <h3>2 - Introduction</h3>
                <label>2.1 List the objectives of training</label>
                <textarea name="objectives" rows="4"><?php echo htmlspecialchars($existing['objectives'] ?? ''); ?></textarea>

                <label>2.2 From your point of view list the importance of training</label>
                <textarea name="importance" rows="4"><?php echo htmlspecialchars($existing['importance'] ?? ''); ?></textarea>

                <label>2.3 State the nature of training</label>
                <textarea name="nature_of_training" rows="3"><?php echo htmlspecialchars($existing['nature_of_training'] ?? ''); ?></textarea>

                <label>2.4 State the nature of supervision you had received</label>
                <textarea name="nature_of_supervision" rows="3"><?php echo htmlspecialchars($existing['nature_of_supervision'] ?? ''); ?></textarea>
            </div>

            <!-- 3 - Details of training experience -->
            <div class="form-group">
                <h3>3 - Details of training experience</h3>
                <label>Skill 1: Technical skills</label>
                <textarea name="training_experience_technical" rows="5"><?php echo htmlspecialchars($existing['training_experience_technical'] ?? ''); ?></textarea>

                <label>Skill 2: Personal skills</label>
                <textarea name="training_experience_personal" rows="5"><?php echo htmlspecialchars($existing['training_experience_personal'] ?? ''); ?></textarea>

                <label>Skill 3: Communication skills</label>
                <textarea name="training_experience_communication" rows="5"><?php echo htmlspecialchars($existing['training_experience_communication'] ?? ''); ?></textarea>
            </div>

            <!-- 4 - Company societal impact -->
            <div class="form-group">
                <h3>4 - Company Societal Impact</h3>
                <textarea name="company_societal_impact" rows="4"><?php echo htmlspecialchars($existing['company_societal_impact'] ?? ''); ?></textarea>
            </div>

            <!-- 5 - Conclusion -->
            <div class="form-group">
                <h3>5 - Conclusion</h3>
                <label>How relevant was your training to your major?</label>
                <textarea name="relevance_to_major" rows="3"><?php echo htmlspecialchars($existing['relevance_to_major'] ?? ''); ?></textarea>
            </div>

            <!-- 6 - Theoretical appropriateness -->
            <div class="form-group">
                <h3>6 - Theoretical Appropriateness</h3>
                <textarea name="theoretical_appropriateness" rows="4"><?php echo htmlspecialchars($existing['theoretical_appropriateness'] ?? ''); ?></textarea>
            </div>

            <!-- 7 - Suggestions -->
            <div class="form-group">
                <h3>7 - Suggestions</h3>
                <textarea name="suggestions" rows="4"><?php echo htmlspecialchars($existing['suggestions'] ?? ''); ?></textarea>
            </div>

            <!-- 8 - Recommendation -->
            <div class="form-group">
                <h3>8 - Do you recommend other students to train at the same company?</h3>
                <label><input type="checkbox" name="recommend_students" value="1" <?php echo (strpos($existing['suggestions'] ?? '', 'Recommendation:') !== false) ? 'checked' : ''; ?>> Yes</label>
                <textarea name="recommend_explanation" rows="3"><?php echo htmlspecialchars(''); ?></textarea>
            </div>

            <!-- 9 - Company cooperation -->
            <div class="form-group">
                <h3>9 - How do you evaluate the training company cooperation?</h3>
                <label><input type="radio" name="company_cooperation" value="very_cooperative"> Very cooperative</label><br>
                <label><input type="radio" name="company_cooperation" value="acceptable"> Acceptable</label><br>
                <label><input type="radio" name="company_cooperation" value="weak"> Weak</label><br>
                <label><input type="radio" name="company_cooperation" value="totally_uncooperative"> Totally uncooperative</label>
            </div>

            <div class="card-footer">
                <button type="submit" name="save_draft" class="btn btn-secondary">Save Draft</button>
                <button type="submit" name="submit_final" class="btn btn-primary">Submit Final Report</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
