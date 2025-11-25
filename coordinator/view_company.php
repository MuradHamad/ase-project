<?php
/**
 * View Company Details (coordinator)
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
requireRole('coordinator');

$company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : 0;
if ($company_id <= 0) {
    setFlashMessage('Invalid company specified', 'error');
    header('Location: companies.php');
    exit();
}

$company = fetchOne("SELECT * FROM companies WHERE company_id = ?", [$company_id]);
if (!$company) {
    setFlashMessage('Company not found', 'error');
    header('Location: companies.php');
    exit();
}

// Get supervisors
$supervisors = fetchAll("SELECT * FROM company_supervisors WHERE company_id = ?", [$company_id]);

// Get assignments
$assignments = fetchAll(
    "SELECT ta.*, u.full_name as student_name, s.student_number FROM training_assignments ta
     JOIN students s ON ta.student_id = s.student_id
     JOIN users u ON s.student_id = u.user_id
     WHERE ta.company_id = ?
     ORDER BY ta.training_start_date DESC",
    [$company_id]
);

include '../includes/header.php';
?>

<section class="container">
    <h2><?php echo htmlspecialchars($company['company_name']); ?></h2>

    <div class="card">
        <div class="card-header">
            <h3>Company Information</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Address</label>
                    <p><?php echo htmlspecialchars($company['full_address'] ?? '—'); ?></p>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <p><?php echo htmlspecialchars($company['phone'] ?? '—'); ?></p>
                </div>
                <div class="form-group">
                    <label>Fax</label>
                    <p><?php echo htmlspecialchars($company['fax'] ?? '—'); ?></p>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <p><?php echo htmlspecialchars($company['email'] ?? '—'); ?></p>
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <p><?php echo htmlspecialchars($company['website'] ?? '—'); ?></p>
                </div>
                <div class="form-group">
                    <label>Approval Status</label>
                    <p><span class="badge badge-<?php echo $company['approval_status'] === 'approved' ? 'success' : ($company['approval_status'] === 'pending' ? 'warning' : 'danger'); ?>"><?php echo ucfirst($company['approval_status']); ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <h3 style="margin-top: 20px;">Company Supervisors</h3>
    <?php if (empty($supervisors)): ?>
        <p>No supervisors assigned.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Primary</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($supervisors as $s): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['email'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($s['phone'] ?? '—'); ?></td>
                        <td><?php echo $s['is_primary'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3 style="margin-top: 20px;">Assigned Students</h3>
    <?php if (empty($assignments)): ?>
        <p>No students assigned to this company.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Start Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($a['student_number']); ?></td>
                        <td><?php echo formatDate($a['training_start_date']); ?></td>
                        <td><?php echo htmlspecialchars($a['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p style="margin-top: 20px;">
        <a class="btn btn-secondary" href="companies.php">Back</a>
    </p>
</section>

<?php include '../includes/footer.php'; ?>
