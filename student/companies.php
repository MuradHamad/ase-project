<?php
/**
 * Company Selection Page
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('student');

$page_title = 'Browse Companies';

$user_id = getCurrentUserId();
$student_id = $_SESSION['student_id'];

// Check if student already has an assignment
$existing_assignment = fetchOne(
    "SELECT * FROM training_assignments WHERE student_id = ?",
    [$student_id]
);

// Get approved companies
$companies = fetchAll(
    "SELECT c.*, 
    (SELECT full_name FROM company_supervisors WHERE company_id = c.company_id AND is_primary = 1 LIMIT 1) as supervisor_name
    FROM companies c 
    WHERE c.approval_status = 'approved'
    ORDER BY c.company_name ASC"
);

// Handle company selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_company']) && !$existing_assignment) {
    $company_id = intval($_POST['company_id']);
    
    // Get a random supervisor (in real system, this would be assigned by coordinator)
    $supervisors = fetchAll("SELECT supervisor_id FROM academic_supervisors LIMIT 1");
    if (!empty($supervisors)) {
        $supervisor_id = $supervisors[0]['supervisor_id'];
        
        // Get current semester dates (simplified - adjust as needed)
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+20 days'));
        
        $sql = "INSERT INTO training_assignments 
                (student_id, company_id, academic_supervisor_id, training_start_date, 
                 training_end_date, status, total_hours, training_type)
                VALUES (?, ?, ?, ?, ?, 'assigned', ?, 'company')";
        
        $result = executeUpdate($sql, [
            $student_id,
            $company_id,
            $supervisor_id,
            $start_date,
            $end_date,
            TRAINING_HOURS
        ]);
        
        if ($result !== false) {
            setFlashMessage('Company selected successfully! You can now submit your reports.', 'success');
            header('Location: dashboard.php');
            exit();
        } else {
            setFlashMessage('Error selecting company. Please try again.', 'error');
        }
    }
}

include '../includes/header.php';
?>

<?php if ($existing_assignment): ?>
    <div class="alert alert-info">
        You already have a training assignment. 
        <a href="dashboard.php">Go to Dashboard</a>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Approved Companies</h2>
    </div>
    <div class="card-body">
        <?php if (empty($companies)): ?>
            <p>No approved companies available at the moment.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Website</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($company['company_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($company['full_address'] ?? '—'); ?></td>
                                <td>
                                    <?php if ($company['phone']): ?>
                                        <div>Phone: <?php echo htmlspecialchars($company['phone']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($company['email']): ?>
                                        <div>Email: <?php echo htmlspecialchars($company['email']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($company['website']): ?>
                                        <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank">
                                            Visit Website
                                        </a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$existing_assignment): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">
                                            <button type="submit" name="select_company" class="btn btn-sm btn-primary"
                                                    onclick="return confirm('Are you sure you want to select this company?');">
                                                Select
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-info">Already Assigned</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


