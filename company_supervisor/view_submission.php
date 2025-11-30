<?php
/**
 * View submissions for a trainee assignment (company supervisor)
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('company_supervisor');

$page_title = 'View Submission';

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$company_id = $_SESSION['company_id'] ?? null;
$company_supervisor_id = $_SESSION['company_supervisor_id'] ?? null;

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

// Handle actions: approve stage1 / weekly followup / submit evaluation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_stage1'])) {
        // mark company supervisor signed on stage1_reports (column is company_supervisor_signature)
        $res = executeUpdate("UPDATE stage1_reports SET company_supervisor_signature = 1, company_supervisor_signed_date = NOW() WHERE assignment_id = ?", [$assignment_id]);
        if ($res !== false) {
            setFlashMessage('Stage 1 report approved', 'success');
        } else {
            setFlashMessage('Failed to approve', 'error');
        }
        header('Location: view_submission.php?assignment_id=' . $assignment_id);
        exit();
    }

    if (isset($_POST['approve_weekly']) && isset($_POST['followup_id'])) {
        $followup_id = intval($_POST['followup_id']);
        $res = executeUpdate("UPDATE weekly_followups SET company_supervisor_signed = 1, company_supervisor_signed_date = NOW() WHERE followup_id = ? AND assignment_id = ?", [$followup_id, $assignment_id]);
        if ($res !== false) {
            setFlashMessage('Weekly follow-up approved', 'success');
        } else {
            setFlashMessage('Failed to approve weekly followup', 'error');
        }
        header('Location: view_submission.php?assignment_id=' . $assignment_id);
        exit();
    }

    if (isset($_POST['submit_evaluation'])) {
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

        $sql = "INSERT INTO company_evaluations (assignment_id, intended_goals, assigned_tasks_summary, feedback_curriculum_a, feedback_curriculum_b, feedback_curriculum_c, additional_notes, recommend_students, recommend_explanation, company_cooperation, company_supervisor_name, company_supervisor_signature, evaluation_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $res = executeUpdate($sql, [$assignment_id, $intended_goals, $assigned_tasks_summary, $feedback_a, $feedback_b, $feedback_c, $additional_notes, $recommend_students, $recommend_explanation, $company_cooperation, $_SESSION['full_name'] ?? '', 'signed', $evaluation_date]);
        if ($res !== false) {
            setFlashMessage('Company evaluation submitted', 'success');
        } else {
            setFlashMessage('Failed to submit evaluation', 'error');
        }
        header('Location: view_submission.php?assignment_id=' . $assignment_id . '#evaluation');
        exit();
    }
}

// Fetch reports
$student = fetchOne("SELECT u.full_name, s.student_number FROM users u JOIN students s ON u.user_id = s.student_id WHERE u.user_id = ?", [$assignment['student_id']]);
$stage1 = fetchOne("SELECT * FROM stage1_reports WHERE assignment_id = ? ORDER BY submitted_at DESC LIMIT 1", [$assignment_id]);
$weekly = fetchAll("SELECT * FROM weekly_followups WHERE assignment_id = ? ORDER BY week_number ASC", [$assignment_id]);
$final = fetchOne("SELECT * FROM final_reports WHERE assignment_id = ? ORDER BY submitted_at DESC LIMIT 1", [$assignment_id]);
$existing_evaluation = fetchOne("SELECT * FROM company_evaluations WHERE assignment_id = ? LIMIT 1", [$assignment_id]);

include '../includes/header.php';
?>

<div class="container">
    <h2>Submissions for <?php echo htmlspecialchars($student['full_name'] ?? '—'); ?> (<?php echo htmlspecialchars($student['student_number'] ?? '—'); ?>)</h2>

    <div class="card">
        <div class="card-header"><h3>Stage 1 Report</h3></div>
        <div class="card-body">
            <?php if ($stage1 === false): ?>
                <p>No stage 1 report submitted yet.</p>
            <?php else: ?>
                <div style="white-space: pre-wrap; border:1px solid #eaeaea; padding:12px; background:#fff;">
                    <?php echo nl2br(htmlspecialchars($stage1['introduction_text'] . "\n\n" . $stage1['intended_goals'] . "\n\n" . $stage1['company_details'])); ?>
                </div>
                <?php $stage1_signed = isset($stage1['company_supervisor_signature']) && $stage1['company_supervisor_signature']; ?>
                <p>Company Signed: <?php echo $stage1_signed ? 'Yes' : 'No'; ?>
                    <?php if ($stage1_signed && !empty($stage1['company_supervisor_signed_date'])): ?>
                        — Signed on <?php echo formatDate($stage1['company_supervisor_signed_date']); ?>
                    <?php endif; ?>
                </p>
                <div style="margin-top:8px;">
                    <a href="approve_stage1.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-sm btn-secondary">View Stage 1</a>
                    <?php if (!$stage1_signed): ?>
                        <form method="post" style="display:inline-block; margin-left:8px;">
                            <input type="hidden" name="approve_stage1" value="1">
                            <button class="btn btn-primary" type="submit">Approve Stage 1</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card" style="margin-top:15px;">
        <div class="card-header"><h3>Weekly Follow-ups</h3></div>
        <div class="card-body">
                    <?php if (!empty($weekly)): ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Date</th>
                                    <th>Company Signed</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($weekly as $wf): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($wf['week_number']); ?></td>
                                        <td>
                                            <?php
                                            if (!empty($wf['week_start_date']) || !empty($wf['week_end_date'])) {
                                                $start = !empty($wf['week_start_date']) ? formatDate($wf['week_start_date']) : '';
                                                $end = !empty($wf['week_end_date']) ? formatDate($wf['week_end_date']) : '';
                                                echo trim($start . (!empty($start) && !empty($end) ? ' - ' : '') . $end) ?: '-';
                                            } elseif (!empty($wf['submitted_at'])) {
                                                echo formatDate($wf['submitted_at']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($wf['company_supervisor_signed'])): ?>
                                                Yes
                                                <?php if (!empty($wf['company_supervisor_signed_date'])): ?>
                                                    — <?php echo formatDate($wf['company_supervisor_signed_date']); ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                No
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="approve_week.php?followup_id=<?php echo $wf['followup_id']; ?>" class="btn btn-sm btn-secondary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No weekly follow-ups submitted yet.</p>
                    <?php endif; ?>
        </div>
    </div>

    <div class="card" style="margin-top:15px;">
        <div class="card-header"><h3>Final Report</h3></div>
        <div class="card-body">
            <?php if ($final === false): ?>
                <p>No final report submitted yet.</p>
            <?php else: ?>
                <div style="white-space: pre-wrap; border:1px solid #eaeaea; padding:12px; background:#fff;">
                    <?php echo nl2br(htmlspecialchars($final['objectives'] . "\n\n" . $final['training_experience_technical'] . "\n\n" . $final['training_experience_personal'])); ?>
                </div>
            <?php endif; ?>
                <p>
                    Company Evaluation Submitted: 
                    <?php if (!empty($existing_evaluation)): ?>
                        Yes — submitted on <?php echo formatDate($existing_evaluation['submitted_at'] ?? $existing_evaluation['evaluation_date']); ?>
                    <?php else: ?>
                        No
                    <?php endif; ?>
                </p>
        </div>
    </div>

    <div class="card" id="evaluation" style="margin-top:15px;">
        <div class="card-header"><h3>Company Evaluation (Trainee Evaluation)</h3></div>
        <div class="card-body">
            <?php if ($existing_evaluation !== false): ?>
                <p><strong>Evaluation already submitted on <?php echo formatDate($existing_evaluation['submitted_at'] ?? $existing_evaluation['evaluation_date']); ?></strong></p>
                <div style="white-space: pre-wrap; border:1px solid #eaeaea; padding:12px; background:#fff;">
                    <?php
                    echo nl2br(htmlspecialchars(
                        "Intended goals:\n" . ($existing_evaluation['intended_goals'] ?? '') . "\n\n" .
                        "Assigned tasks summary:\n" . ($existing_evaluation['assigned_tasks_summary'] ?? '') . "\n\n" .
                        "Feedback A:\n" . ($existing_evaluation['feedback_curriculum_a'] ?? '') . "\n\n" .
                        "Feedback B:\n" . ($existing_evaluation['feedback_curriculum_b'] ?? '') . "\n\n" .
                        "Feedback C:\n" . ($existing_evaluation['feedback_curriculum_c'] ?? '') . "\n\n" .
                        "Additional notes:\n" . ($existing_evaluation['additional_notes'] ?? '')
                    ));
                    ?>
                </div>
                <div style="margin-top:12px;">
                    <a href="evaluate.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-secondary">View / Edit Evaluation</a>
                </div>
            <?php else: ?>
                <a href="evaluate.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-primary">Fill Company Evaluation</a>
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
