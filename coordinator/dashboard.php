<?php
/**
 * Coordinator Dashboard
 * Field Training Management System
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('coordinator');

$page_title = 'Coordinator Dashboard';

$user_id = getCurrentUserId();

// Get pending companies
$pending_companies = fetchAll(
    "SELECT c.*, 
     (SELECT full_name FROM company_supervisors WHERE company_id = c.company_id AND is_primary = 1 LIMIT 1) as supervisor_name
     FROM companies c 
     WHERE c.approval_status = 'pending'
     ORDER BY c.created_at DESC"
);

// Get approved companies
$approved_companies = fetchAll(
    "SELECT c.*, 
     (SELECT full_name FROM company_supervisors WHERE company_id = c.company_id AND is_primary = 1 LIMIT 1) as supervisor_name
     FROM companies c 
     WHERE c.approval_status = 'approved'
     ORDER BY c.company_name ASC"
);

// Handle company approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_company'])) {
        $company_id = intval($_POST['company_id']);
        $sql = "UPDATE companies SET approval_status = 'approved', approved_by = ?, approved_at = NOW() 
                WHERE company_id = ?";
        executeUpdate($sql, [$user_id, $company_id]);
        setFlashMessage('Company approved successfully!', 'success');
        header('Location: dashboard.php');
        exit();
    } elseif (isset($_POST['reject_company'])) {
        $company_id = intval($_POST['company_id']);
        $sql = "UPDATE companies SET approval_status = 'rejected', approved_by = ?, approved_at = NOW() 
                WHERE company_id = ?";
        executeUpdate($sql, [$user_id, $company_id]);
        setFlashMessage('Company rejected.', 'info');
        header('Location: dashboard.php');
        exit();
    }
}

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stats-grid">
        <div class="stat-card warning">
            <div class="stat-value"><?php echo count($pending_companies); ?></div>
            <div class="stat-label">Pending Approvals</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value"><?php echo count($approved_companies); ?></div>
            <div class="stat-label">Approved Companies</div>
        </div>
    </div>
</div>

<!-- Pending Companies -->
<?php if (!empty($pending_companies)): ?>
    <div class="card">
        <div class="card-header">
            <h2>Pending Company Approvals</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Submitted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_companies as $company): ?>
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
                                <td><?php echo formatDate($company['created_at']); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">
                                        <button type="submit" name="approve_company" class="btn btn-sm btn-success"
                                                onclick="return confirm('Approve this company?');">
                                            Approve
                                        </button>
                                        <button type="submit" name="reject_company" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Reject this company?');">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Approved Companies -->
<div class="card">
    <div class="card-header">
        <h2>Approved Companies</h2>
    </div>
    <div class="card-body">
        <?php if (empty($approved_companies)): ?>
            <p>No approved companies yet.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Approved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approved_companies as $company): ?>
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
                                <td><?php echo formatDate($company['approved_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

