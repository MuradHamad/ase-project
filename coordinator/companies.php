<?php
/**
 * Coordinator Companies Management
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('coordinator');

$page_title = 'Companies Management';

$user_id = getCurrentUserId();

// Get all companies with their approval status
$companies = fetchAll(
    "SELECT c.*, 
     (SELECT full_name FROM company_supervisors WHERE company_id = c.company_id AND is_primary = 1 LIMIT 1) as supervisor_name,
     (SELECT COUNT(*) FROM training_assignments WHERE company_id = c.company_id) as assignment_count
     FROM companies c 
     ORDER BY c.approval_status ASC, c.company_name ASC"
);

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_company'])) {
        $company_id = intval($_POST['company_id']);
        $sql = "UPDATE companies SET approval_status = 'approved', approved_by = ?, approved_at = NOW() 
                WHERE company_id = ?";
        executeUpdate($sql, [$user_id, $company_id]);
        setFlashMessage('Company approved successfully!', 'success');
        header('Location: companies.php');
        exit();
    } elseif (isset($_POST['reject_company'])) {
        $company_id = intval($_POST['company_id']);
        $sql = "UPDATE companies SET approval_status = 'rejected', approved_by = ?, approved_at = NOW() 
                WHERE company_id = ?";
        executeUpdate($sql, [$user_id, $company_id]);
        setFlashMessage('Company rejected.', 'info');
        header('Location: companies.php');
        exit();
    }
}

// Count statuses
$pending_count = 0;
$approved_count = 0;
$rejected_count = 0;
foreach ($companies as $c) {
    if ($c['approval_status'] === 'pending') $pending_count++;
    elseif ($c['approval_status'] === 'approved') $approved_count++;
    elseif ($c['approval_status'] === 'rejected') $rejected_count++;
}

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stats-grid">
        <div class="stat-card warning">
            <div class="stat-value"><?php echo $pending_count; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value"><?php echo $approved_count; ?></div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $rejected_count; ?></div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>
</div>

<?php if (empty($companies)): ?>
    <p>No companies found.</p>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h2>All Companies</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Supervisor</th>
                            <th>Status</th>
                            <th>Students Assigned</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($company['company_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($company['supervisor_name'] ?? '—'); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $company['approval_status'] === 'approved' ? 'success' : 
                                            ($company['approval_status'] === 'pending' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($company['approval_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo (int)$company['assignment_count']; ?></td>
                                <td>
                                    <?php if ($company['phone']): ?>
                                        <div>Phone: <?php echo htmlspecialchars($company['phone']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($company['email']): ?>
                                        <div>Email: <?php echo htmlspecialchars($company['email']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($company['approval_status'] === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">
                                            <button type="submit" name="approve_company" class="btn btn-sm btn-success">Approve</button>
                                            <button type="submit" name="reject_company" class="btn btn-sm btn-danger" onclick="return confirm('Reject this company?')">Reject</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="view_company.php?company_id=<?php echo $company['company_id']; ?>" class="btn btn-sm">Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
